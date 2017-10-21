<?php
/*-------------------------------------------------------------------------------
 * Xataface Web Application Framework
 * Copyright (C) 2005-2008 Web Lite Solutions Corp (shannah@sfu.ca)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *-------------------------------------------------------------------------------
 */


/**
 * This action sends email to the current found set.  It provides the user with
 * an email form where they can type a subject and a body.  They can include
 * macro variables that will be parsed for each record so that each email can
 * be customized to an extent.
 *
 * @author Steve Hannah <steve@weblite.ca>
 * @created August 2007
 * @modified January 2012 To use new table structures  (by Steve Hannah)
 */
class actions_email {

	var $messages = array();
	var $emailTable;
	var $joinTable;
	var $recipientsTable;
	var $emailColumn;

	/**
	 * implements action handle() method.
	 */
	function handle(&$params){
		$action =& $params['action'];
		//print_r($params);
		$app =& Dataface_Application::getInstance();
		$query =& $app->getQuery();
		$query['-skip'] = 0;
		$query['-limit'] = 9999999;
		
		// Let's validate some of the parameters first
		
		// The actions.ini file should define an email_column and email_table parameters
		// to indicate:
		//	a. the name of the column from the current table that should be used
		//	    as the "send to" email address.
		//	b. the name of the table that should store the email messages.
		
		
		import('Dataface/Ontology.php');
		Dataface_Ontology::registerType('Person', 'Dataface/Ontology/Person.php', 'Dataface_Ontology_Person');
		$ontology =& Dataface_Ontology::newOntology('Person', $query['-table']);
		$action['email_column'] = $ontology->getFieldname('email');
		
		
		
		if ( !@$action['email_column'] ) return PEAR::raiseError("No email column specified in actions.ini", DATAFACE_E_WARNING);
		if ( !@$action['email_table'] ) return PEAR::raiseError("No email table specified in actions.ini", DATAFACE_E_WARNING);
		
		// Make sure the table and column names are not malicious.
		$this->emailColumn = $col = $action['email_column'];
		if ( strpos($col, '`') !== false ) return PEAR::raiseError("Invalid email column name: '$col'", DATAFACE_E_WARNING);
		
		$this->emailTable = $table = 'xataface__email_newsletters';//$action['email_table'];
		if ( strpos($table, '`') !== false ) return PEAR::raiseError("Invalid email table name: '$table'", DATAFACE_E_WARNING);
		
		$this->joinTable = $join_table = 'xataface__email_log';//$query['-table'].'__email';
		$join_table = $this->joinTable;
		$this->recipientsTable = $query['-table'];
			// The name of the table that tracks which records have had email sent.
		
		// Next make sure that the email table(s) exist(s)
		if ( !Dataface_Table::tableExists($table, false) || !Dataface_Table::tableExists($join_table, false) ){
			$this->createEmailTables($table, $join_table);
		}
		
		$emailTableObj =& Dataface_Table::loadTable($this->emailTable);
		$contentField =& $emailTableObj->getField('content');
		$contentField['widget']['atts']['rows'] = 20;
		$contentField['widget']['atts']['cols'] = 60;
		$contentField['widget']['label'] = 'Message body';
		$contentField['widget']['description'] = 'Please enter your message content in plain text.';
		$contentField['widget']['type'] = 'ckeditor';
		$contentField['widget']['ckeditor']['toolbar'] = 'XBasic';
		$contentField['widget']['ckeditor']['extraPlugins'] = 'SchemaBrowser';
		//$contentField['widget']['atts']['data-xf-schemabrowser-tablename'] = $query['-table'];
		Dataface_JavascriptTool::getInstance()->import('xataface/modules/ckeditor/plugins/SchemaBrowser.js');
		
		//$contentField['widget']['editor'] = 'ckeditor';
		
		$subjectField =& $emailTableObj->getField('subject');
		$subjectField['widget']['atts']['size'] = 60;
		
		$fromField =& $emailTableObj->getField('from');
		$fromField['widget']['atts']['size'] = 60;
		$fromField['widget']['description'] = 'e.g. Web Lite Solutions &lt;info@weblite.ca&gt;';
		
		
		$ccField =& $emailTableObj->getField('cc');
		$ccField['widget']['atts']['size'] = 60;
		$ccField['widget']['label'] = 'Bcc';
		$ccField['widget']['description'] = 'e.g. youremail@example.com.  Attention:  Copies of every email sent in this batch will be sent to this bcc address if added.  This may result in quite a large number of emails being sent to this single address.  Use this feature carefully.';
		
		$ignoreBlacklistField =& $emailTableObj->getField('ignore_blacklist');
		$ignoreBlacklistField['widget']['type'] = 'checkbox';
		$ignoreBlacklistField['widget']['description'] = 'The black list is a list of email addresses that have opted out of receiving email.  I.e. Users on the black list do not want to receive email.  Check this box if you want to send to blacklisted addresses despite their wish to be left alone.';
		
		
		$templateField =& $emailTableObj->getField('template_id');
		$templateField['widget']['filters']['table_name'] = $query['-table'];
		
		$form = df_create_new_record_form($table);
		$form->_build();
		
		$form->addElement('hidden','-action');
		$form->addElement('hidden','-table');
		$form->setDefaults(array('-action'=>$query['-action'], '-table'=>$query['-table']));
		$form->insertElementBefore($form->createElement('checkbox', 'send_now', '','Send now (leave this box unchecked if you wish these emails to be queued for later sending by the daily cron job.  Recommended to leave this box unchecked for large found sets (&gt;100 records).)'),'submit_new_newsletters_record');
		$form->addElement('hidden', '-query_string');
		$form->setDefaults(array('-query_string'=>base64_encode(serialize($query))));
		$form->setSubmitLabel("Add to Mail Queue");
		if ( @$app->_conf['from_email'] ){
			$form->setDefaults(array('from'=>$app->_conf['from_email']));
		}
		
		

		if ( $form->validate() ){
			$res = $form->process(array(&$form,'save'), true);
			if ( PEAR::isError($res) ) return $res;
			
			// The form saved ok.. so we can send the emails.
			$vals = $form->exportValues();
			$q2 = unserialize(base64_decode($vals['-query_string']));
			$qb = new Dataface_QueryBuilder($query['-table'], $q2);
			$recTable = Dataface_Table::loadTable($query['-table']);
			
			$tkeys = $recTable->keys();
			$keyCol = null;
			foreach ($tkeys as $key=>$val){
				$keyCol = $key;
				break;
			}
			$sql = "insert ignore into `$join_table` (recipient_email,messageid,date_created,primary_key) select distinct (`".$col."`) `".$col."`, '".$form->_record->val('id')."' as messageid, now() as date_created, `".$keyCol."` as primary_key ".$qb->_from()." ".$qb->_secure($qb->_where());
			
			//echo $sql;exit;
			$sres = df_q($sql);
			
			
			//if ( !$sres ) trigger_error(mysql_error(df_db()), E_USER_ERROR);
			$jobId = $this->postJob($form->_record->val('id'), $this->emailTable, $this->joinTable, $this->recipientsTable, $this->emailColumn);
			$q2['-action'] = 'email_progress';
			$q2['-job-id'] = $jobId;
			unset($q2['-limit']);
			header('Location: '.$app->url($q2).'&--msg='.urlencode("The message has been queued for delivery"));
			exit;
			
		
		}
		
		$addresses = array();
		
		ob_start();
		$form->display();
		$context = array();
		$context['email_form'] = ob_get_contents();
		$profileTable =& Dataface_Table::loadTable($query['-table']);
		
		$context['fields'] = array_keys($profileTable->fields(false,true,true));
		$modurl = DATAFACE_SITE_URL.'/modules/Email';
		if ( realpath(__FILE__) == realpath(DATAFACE_PATH.'/modules/Email/email.php') ){
			$modurl = DATAFACE_URL.'/modules/Email';
		}
		
		$context['EMAIL_ROOT'] = $modurl;
		
		ob_end_clean();
		df_register_skin('email', dirname(__FILE__).'/../templates');
		df_display($context, 'email_form.html');
		
		
	}
	
	function isBlackListed($email){
		$app = Dataface_Application::getInstance();
		$method = 'Email__isBlackListed';
		$del = $app->getDelegate();
		if ( isset($del) and method_exists($del, $method) ){
			$res = $del->$method($email);
			if ( isset($res) and is_bool($res) ){
				return $res;
			}
		}
		if ( !Dataface_Table::tableExists('dataface__email_blacklist') ) $this->createEmailTables(null,null);
		$res = mysql_query("select email from dataface__email_blacklist where email='".addslashes($email)."' limit 1", df_db());
		if ( !$res ) trigger_error(mysql_error(df_db()), E_USER_ERROR);
		list($num) = mysql_fetch_row($res);
		@mysql_free_result($res);
		return $num;
	}
	
	function getBlackListed($emails){
		if ( !Dataface_Table::tableExists('dataface__email_blacklist') ) $this->createEmailTables(null,null);
		if ( !is_array($emails) ) $emails = array($emails);
		$res = mysql_query("select email from dataface__email_blacklist where email in ('".implode("','", array_map('addslashes',$emails))."')", df_db());
		$out = array();
		if (!$res ) trigger_error(mysql_error(df_db()), E_USER_ERROR);
		while ($row = mysql_fetch_row($res) ) $out[] = $row[0];
		@mysql_free_result($res);
		return $out;
	}
	
	/**
	 * Creates the email tables necessary to store the email.
	 * @param $tablename The name of the table that is to store the email
	 *					 messages themselves.
	 * @param $join_table The name of the table that stores the status
	 *					  of each sent email.
	 * @return void
	 */
	function createEmailTables($tablename, $join_table){
		$app =& Dataface_Application::getInstance();
		
		$sql = array();
		if ( isset($tablename) ){
			$sql[] = "create table if not exists `{$tablename}` (
			`id` int(11) not null auto_increment,
			`template_id` int(11) default null,
			`subject` varchar(128) not null,
			`cc` varchar(128) default null,
			`from` varchar(128) default null,
			`content` text,
			`ignore_blacklist` tinyint(1) default 0,
			posted_by varchar(255) default null,
			primary key (`id`)) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		}
		if ( isset($join_table) ){
			$sql[] = "create table if not exists `{$join_table}` (
			`messageid` int(11) not null,
			`recipient_email` varchar(128) not null,
			`sent` tinyint(1) default 0,
			`date_created` datetime default null,
			`date_sent` datetime default null,
			`success` tinyint(1) default null,
			`comment` varchar(255) default null,
			primary key (`messageid`,`recipient_email`))";
		}
		
		
		$sql[] = "create table if not exists `dataface__email_blacklist` (
			`email` varchar(255) not null primary key
			)";
		
		foreach ($sql as $q ){
			$res = mysql_query($q, $app->db());
			if ( !$res ) trigger_error(mysql_error($app->db()), E_USER_ERROR);
		}
		
		try {
			// The comment field was added in version 2.0... adding it now
			// just in case there was a legacy table that we are adding to.
			df_q("alter table `{$join_table}` add `comment` varchar(255) after `success`");
		} catch (Exception $ex){}
		return true;
		
		
	
	}
	
	
	private $_tempRecipientRecord;
	/**
	 * @brief Called on matches of field macros.
	 * $matches[0] e.g. {$fieldname}
	 * $matches[1] e.g. fieldname
	 */
	function _replaceFieldCallback($matches){
		return $this->_tempRecipientRecord->display($matches[1]);
	}
	
	
	function getAttachments($emailId){
		$res = df_q("select file from xataface__email_attachments where email_id='".addslashes($emailId)."'");
		
		$out = array();
		
		$field =& Dataface_Table::loadTable('xataface__email_attachments')->getField('file');
		$savepath = $field['savepath'];
		if ( !$savepath || !is_dir($savepath) ){
			return array();
		}
		$ajaxUploadMod = Dataface_ModuleTool::getInstance()->loadModule('modules_ajax_upload');
		if ( !$ajaxUploadMod ||  PEAR::isError($ajaxUploadMod) ){
			error_log("Could not send attachments because the ajax_upload module is not loaded.");
			return array();
		}
		
		while ($row = mysql_fetch_row($res) ){
			
			$fileName = basename($row[0]);
			$filePath = $savepath . DIRECTORY_SEPARATOR. $fileName;
			if ( !file_exists($filePath) ){
				error_log("Failed to attach file $filePath because it could not be found.");
				continue;
			}
			$contents = file_get_contents($filePath);
			$mimetype = $ajaxUploadMod->getMimeType($filePath);
			$attachment = MIME::message($contents, $mimetype, $fileName, 'ISO-8859-1', 'base64', 'attachment');
			$out[] = $attachment;
		}
		@mysql_free_result($res);
		return $out;
	}
	
	
	/**
	 * Sends the email specified by $emailId to all recipients.
	 * @param integer $emailId The id of the email message.
	 * @param string $emailTable Optional the name of the table containing the email messages.
	 * @param string $joinTable Optional the name of the table corresponding to a single recipient of the given email.
	 * @param string $recipientsTable The name of the table where the recipients originated from.
	 * @param string $emailColumn The name of the column that stored the email address.
	 */
	function sendMail($emailId, $emailTable=null, $joinTable = null, $recipientsTable = null , $emailColumn = null){
		require_once dirname(__FILE__).'/../lib/XPM/MIME.php';
		if ( isset($emailTable) ) $this->emailTable = $emailTable;
		if ( isset($joinTable) ) $this->joinTable = $joinTable;
		if ( isset($recipientsTable) ) $this->recipientsTable = $recipientsTable;
		if ( isset($emailColumn) ) $this->emailColumn = $emailColumn;
		$app =& Dataface_Application::getInstance();
		$conf =& $app->_conf;
		
		// We want to be able to override the replacement context
		// via a delegate class method.  
		$decorateEmailContextFunc = 'decorateEmailContext';
		$recipientsTableObj = Dataface_Table::loadTable($this->recipientsTable);
		$recipientsTableDelegate =& $recipientsTableObj->getDelegate();
		$appDelegate =& $app->getDelegate();
		$tableDecorateEmailContextFuncExists = (isset($recipientsTableDelegate) and method_exists($recipientsTableDelegate, $decorateEmailContextFunc) );
		
		$onSuccessFunc = 'Email__onSuccess';
		$onSuccessAppFuncExists = (isset($appDelegate) and method_exists($appDelegate, $onSuccessFunc));
		$onSuccessTableFuncExists = (isset($recipientsTableDelegate) and method_exists($recipientsTableDelegate, $onSuccessFunc) );
		
		$onFailFunc = 'Email__onFail';
		$onFailAppFuncExists = (isset($appDelegate) and method_exists($appDelegate, $onFailFunc));
		$onFailTableFuncExists = (isset($recipientsTableDelegate) and method_exists($recipientsTableDelegate, $onFailFunc) );
		
		
		$appDecorateEmailContextFuncExists = (isset($appDelegate) and method_exists($appDelegate, $decorateEmailContextFunc));
		$optOutMessageFunc = 'getEmailOptOutMessage';
		$appOptOutMessageFuncExists = (isset($appDelegate) and method_exists($appDelegate, $optOutMessageFunc));
		$tableOptOutMessageFuncExists = (isset($recipientsTableDelegate) and method_exists($recipientsTableDelegate, $optOutMessageFunc));
		
		$defaultEmailOptOutHtml= null;
		$defaultEmailOptOutText = null;
		if ( @$conf['modules_Email'] ){
			$emailConf =& $conf['modules_Email'];
			if ( @$emailConf['opt_out_html'] ){
				$defaultEmailOptOutHtml = $emailConf['opt_out_html'];
			}
			if ( @$emailConf['opt_out_text'] ){
				$defaultEmailOptOutText = $emailConf['opt_out_text'];
			}
		}
		
		
		
		if ( @$conf['_mail']['func'] ) $mail_func = $conf['_mail']['func'];
		else $mail_func = 'mail';
		
		$emailTableObj =& Dataface_Table::loadTable($this->emailTable);
		
		$recTable = Dataface_Table::loadTable($this->recipientsTable);
			
		$tkeys = $recTable->keys();
		$keyCol = null;
		foreach ($tkeys as $key=>$val){
			$keyCol = $key;
			break;
		}
		
		$emailTableObj->addRelationship('recipients', 
			array('__sql__' => 'select * from `'.$this->recipientsTable.'` r inner join `'.$this->joinTable.'` j on (`r`.`'.$this->emailColumn.'` = j.recipient_email and `r`.`'.$keyCol.'`= j.primary_key) inner join `'.$this->emailTable.'` e on e.id = j.messageid where e.id=\''.addslashes($emailId).'\'')
			);
			
	
		$email = df_get_record($this->emailTable, array('id'=>$emailId));
		if ( !$email) return PEAR::raiseError("Failed to send email because no message with id {$emailId} could be found.", DATAFACE_E_ERROR);
		
		$jres = df_q("select job_id from xataface__email_jobs where email_id='".addslashes($emailId)."'");
		if ( mysql_num_rows($jres) == 0 ){
			throw new Exception("Could not find job associated with email.");
		}
		list($jobId) = mysql_fetch_row($jres);
		@mysql_free_result($jres);
		
		$template = null;
		if ( $email->val('template_id') ){
			$template = df_get_record('xataface__email_templates', array('template_id'=>'='.$email->val('template_id')));
		}
		
		// Let's update the count
		$totalRecipients = $email->numRelatedRecords('recipients');
		$res = df_q("update xataface__email_jobs set total_emails='".addslashes($totalRecipients)."' where email_id='".addslashes($emailId)."'");
		
		
		$recipients = $email->getRelatedRecordObjects('recipients', 0,500, 'sent=0');
		foreach ($recipients as $recipient ){
			
			
			// Check to make sure that job hasn't been cancelled.
			$jres = df_q("select cancelled from xataface__email_jobs where job_id='".addslashes($jobId)."'");
			if ( mysql_num_rows($jres) == 0 ){
				throw new Exception("Could not find job record.  Must have been cancelled.");
			}
			list($cancelled) = mysql_fetch_row($jres);
			@mysql_free_result($res);
			if ( $cancelled ){
				return false;
			}
			//sleep(5);
			
			
			$recipientObj = $recipient->toRecord($this->recipientsTable);
			
			
			//$values = $recipient->strvals();
			if ( $appDecorateEmailContextFuncExists ){
				$appDelegate->$decorateEmailContextFunc($email, $template,  $recipientObj);
			}
			
			if ( $tableDecorateEmailContextFuncExists ){
				$recipientsTableDelegate->$decorateEmailContextFunc($email, $template, $recipientObj);
			}
			
			
			
			
			$keys = array();
			//foreach ($values as $key=>$val) $keys[] = '/%'.$key.'%/';
			//$values = array_values($values);
			//$content = preg_replace($keys, $values, $recipient->strval('content'));
			$this->_tempRecipientRecord = $recipientObj;
			$content = preg_replace_callback('/\{\$([^\}]+)\}/', array($this, '_replaceFieldCallback'), $email->strval('content'));
			
			$uniqid = uniqid();
			df_q("update xataface__email_log set uniqid='".addslashes($uniqid)."' where log_id='".addslashes($recipient->val('log_id'))."'");
			
			
			$opt_out_url = df_absolute_url(DATAFACE_SITE_HREF.'?-action=email_opt_out&email='.urlencode($uniqid));
			
			$opt_out_html = <<<END
			<hr />
<p>If you don't want to receive email updates from us, you can opt out of our mailing list by clicking <a href="$opt_out_url">here</a> .</p>
END;

			if ( $defaultEmailOptOutHtml ){
				$opt_out_html = str_replace('$url', $opt_out_url, $defaultEmailOptOutHtml);
			}
			
			if ( $defaultEmailOptOutText ){
				$opt_out_text = str_replace('$url', $opt_out_url, $defaultEmailOptOutText);
			}

			

			$opt_out_text = <<<END

------------------------------------------------------------------
If you don't want to receive email updates from us, you can opt out of our mailing list by going to $opt_out_url .
END;


			if ( $defaultEmailOptOutText ){
				$opt_out_text = str_replace('$url', $opt_out_url, $defaultEmailOptOutText);
			}
			
			
			$opt_out_params = array();
			if ( $appOptOutMessageFuncExists ){
				$opt_out_paramsa = $appDelegate->$optOutMessageFunc($recipientObj, $opt_out_url);
				if ( is_array($opt_out_paramsa) ){
					$opt_out_params = $opt_out_paramsa;
				}
			}
			if ( $tableOptOutMessageFuncExists ){
				$opt_out_paramst = $recipientsTableDelegate->$optOutMessageFunc($recipientObj, $opt_out_url);
				if ( is_array($opt_out_paramst) ){
					$opt_out_params = array_merge($opt_out_params, $opt_out_paramst);
				}
			}
			
			if ( @$opt_out_params['html'] ){
				$opt_out_html = $opt_out_params['html'];
			}
			
			if ( @$opt_out_params['text'] ){
				$opt_out_text = $opt_out_params['text'];
			}
			
			$html_content = $content . $opt_out_html;
			
			$content .= $opt_out_text;
			
			$headers = array();
			
			if ( trim($email->strval('cc')) ){
				$headers[] = "Bcc: ".$email->strval('cc');
			}
			
			if ( trim($email->strval('from')) ){
				$headers[] = "From: ".$email->strval('from');
				$headers[] = "Reply-to: ".$email->strval('from');
			}
			
			if ( @$app->_conf['mail_host'] ){
				$headers[] = 'Message-ID: <' . md5(uniqid(time())) . '@'.$app->_conf['mail_host'].'>';
			}
			//$headers[] = "Content-Type: text/plain; charset=".$app->_conf['oe'];
			
			$joinRecord = $recipient->toRecord($this->joinTable);
			
			if ( !trim($recipient->val('recipient_email')) ){
				$joinRecord->setValue('success',0);
				$joinRecord->setValue('sent',1);
				$joinRecord->setValue('comment', 'Blank address');
				$joinRecord->save();
				
				if( $onFailAppFuncExists ){
				    $appDelegate->$onFailFunc($recipientObj, $email);
				}
				
				if ( $onFailTableFuncExists ){
				    $recipientsTableDelegate->$decorateEmailContextFunc($recipientObj, $email);
				}
				
				unset($joinRecord);
				unset($recipient);
				continue;
			}
			
			
			// path to 'MIME.php' file from XPM4 package
			
			
			// get ID value (random) for the embed image
			$id = MIME::unique();
			
			// set text/plain version of message
			$text = MIME::message(htmlspecialchars_decode(strip_tags(preg_replace(array('/<br[^>]*>/i','/<div[^>]*>/i','/<p[^>]*>/i', '/<table[^>]*>/i'), array("\r\n","\r\n","\r\n","\r\n"),$content))), 'text/plain', null, $app->_conf['oe']);
			// set text/html version of message
			$html = MIME::message($html_content, 'text/html', null,  $app->_conf['oe']);
			// add attachment with name 'file.txt'
			//$at[] = MIME::message('source file', 'text/plain', 'file.txt', 'ISO-8859-1', 'base64', 'attachment');
			$at = $this->getAttachments($emailId);
			//$file = 'xpertmailer.gif';
			// add inline attachment '$file' with name 'XPM.gif' and ID '$id'
			//$at[] = MIME::message(file_get_contents($file), FUNC::mime_type($file), 'XPM.gif', null, 'base64', 'inline', $id);
			
			// compose mail message in MIME format
			//print_r($html);
			//print_r($text);
			//exit;
			$mess = MIME::compose($text, $html, $at);
			
			$le = defined('PHP_EOL') ? PHP_EOL : "\n";
			
			
			if ( !$email->val('ignore_blacklist') and $this->isBlackListed($recipient->val('recipient_email')) ){
				error_log("\nEmail address '".$recipient->val('recipient_email')."' is black listed so we do not send email to this address...");
				$joinRecord->setValue('success',0);
				$joinRecord->setValue('sent',1);
				$joinRecord->setValue('comment', 'Black listed');
				df_q("update xataface__email_jobs set sent_emails=sent_emails+1, blacklisted_emails=blacklisted_emails+1 where email_id='".addslashes($emailId)."'");
				
				if( $onFailAppFuncExists ){
				    $appDelegate->$onFailFunc($recipientObj, $email);
				}
				
				if ( $onFailTableFuncExists ){
				    $recipientsTableDelegate->$onFailFunc($recipientObj, $email);
				}
				
			}
			
			else if ( $mail_func($recipient->strval('recipient_email'), $email->strval('subject'), $mess['content'], implode($le, $headers).$le.$mess['header']) ){
				$joinRecord->setValue('success',1);
				$joinRecord->setValue('sent',1);
				df_q("update xataface__email_jobs set sent_emails=sent_emails+1, successful_emails=successful_emails+1 where email_id='".addslashes($emailId)."'");
				//echo "Successfully sent email to ".$recipient->val('recipient_email');
				//echo "Successfully sent email to {$recipient->strval('recipient_email')}" ;
				//exit;
				
				if( $onSuccessAppFuncExists ){
				    $appDelegate->$onSuccessFunc($recipientObj, $email);
				}
				
				if ( $onSuccessTableFuncExists ){
				    $recipientsTableDelegate->$onSuccessFunc($recipientObj, $email);
				}
			} else {
				$joinRecord->setValue('success',0);
				$joinRecord->setValue('sent',1);
				$this->messages[] = "Failed to send email to ".$email->val('recipient_email');
				error_log("Failed to send email to ".$email->val('recipient_email'));
				df_q("update xataface__email_jobs set sent_emails=sent_emails+1, failed_emails=failed_emails+1 where email_id='".addslashes($emailId)."'");
				//echo "Failed to send";
				//exit;
				
				if( $onSuccessAppFuncExists ){
				    $appDelegate->$onSuccessFunc($recipientObj, $email);
				}
				
				if ( $onSuccessTableFuncExists ){
				    $recipientsTableDelegate->$onSuccessFunc($recipientObj, $email);
				}
			}
			
			$joinRecord->setValue('date_sent',date('Y-m-d H:i:s'));
			$joinRecord->save();
			
			unset($joinRecord);
			unset($recipient);
			
		
		}
	
	}
	
	function postJob($emailId, $emailTable=null, $joinTable = null, $recipientsTable = null , $emailColumn = null){

		$res = df_q("select count(*) from `$joinTable` where messageid='".addslashes($emailId)."'");
		list($count) = mysql_fetch_row($res);
		//echo "Posting job to join table: $joinTable with count ".$count;exit;
		
		$res = df_q(
			"insert into xataface__email_jobs (
				email_id,
				email_table,
				join_table,
				recipients_table,
				email_column,
				active,
				total_emails,
				start_time
				)
				values (
				'".addslashes($emailId)."',
				'".addslashes($emailTable)."',
				'".addslashes($joinTable)."',
				'".addslashes($recipientsTable)."',
				'".addslashes($emailColumn)."',
				1,
				'".addslashes($count)."',
				'".time()."'
				)");
		return mysql_insert_id(df_db());
		
		
	}
}


?>