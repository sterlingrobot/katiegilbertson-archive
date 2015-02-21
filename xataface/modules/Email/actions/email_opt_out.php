<?php
class actions_email_opt_out {
	const ADDRESS_NOT_FOUND = 8404;
	const ADDRESS_ALREADY_FOUND = 8300;
	

	function handle(&$params){
		$app = Dataface_Application::getInstance();
		$query = $app->getQuery();
		
		if ( @$_POST['--opt-in'] ){
			// The user wants to opt in to the list
			
			$this->optIn(@$_POST['--email-id']);
			
			
		} else if ( @$_POST['--opt-out'] ){
			// The user wants to opt out of the list
			$this->optOut(@$_POST['--email-id']);
		
		} else {
			// Display the form
			
			
			
			$mod = Dataface_ModuleTool::getInstance()->loadModule('modules_Email');
			$mod->addPaths();
			Dataface_JavascriptTool::getInstance()->import('xataface/modules/Email/email_opt_out.js');
			$addr = $this->getEmailAddressFromId($query['email']);
			if ( !@$query['email'] or !$addr){
				// No email ID was provided ... Display error
				df_display(array(), 'xataface/modules/email/email_opt_out_error.html');
			
			} else {
				$context = array('emailId'=>$query['email'], 'emailAddress'=> $addr);
				
				if ( $this->checkBlackList($query['email']) ){
					//echo "Blacklisted";
					$context['currentlyBlackListed'] = 1;
				} else {
					//echo "Not blacklisted";
					$context['currentlyBlackListed'] = 0;
				}
				df_display($context, 'xataface/modules/email/email_opt_out.html');
				
			}
				
		}
		
		
	}
	
	
	function optIn($emailId){
	
		try {
			if ( $this->checkBlackList($emailId) ){
				$addr = $this->getEmailAddressFromId($emailId);
				df_q("delete from dataface__email_blacklist where email='".addslashes($addr)."'");
				$app = Dataface_Application::getInstance();
				$del = $app->getDelegate();
				$method = 'Email__afterOptIn';
				if ( isset($del) and method_exists($del, $method) ){
					$del->$method($addr);
				}
				$this->out(array(
					'code' => 200,
					'message' => 'Opt-in successful.  Thank you for your participation.'
				));
			} else {
				throw new Exception('You have already opted into our mail list and should be able to receive our mailouts.', self::ADDRESS_NOT_FOUND);
				
			}
			
		
		} catch (Exception $ex){
		
			if ( $ex->getCode() > 8000 and $ex->getCode() < 9000 ){
				$this->out(array(
					'code' => intval($ex->getCode()-8000),
					'message' => $ex->getMessage()
				));
			}
		
		}
	
	}
	
	
	function optOut($emailId){
		error_log("Opting out $emailId");
		try {
			if ( !$this->checkBlackList($emailId) ){
				$addr = $this->getEmailAddressFromId($emailId);
				df_q("insert into  dataface__email_blacklist (email) values ('".addslashes($addr)."')");
				$app = Dataface_Application::getInstance();
				$del = $app->getDelegate();
				$method = 'Email__afterOptOut';
				if ( isset($del) and method_exists($del, $method) ){
					$del->$method($addr);
				}
				$this->out(array(
					'code' => 200,
					'message' => 'Opt-out successful.  You will no longer receive emails from us.'
				));
			} else {
				throw new Exception('You have already opted out.  You should no longer receive any email from us.', self::ADDRESS_ALREADY_FOUND);
				
			}
			
		
		} catch (Exception $ex){
		
			if ( $ex->getCode() > 8000 and $ex->getCode() < 9000 ){
				$this->out(array(
					'code' => intval($ex->getCode()-8000),
					'message' => $ex->getMessage()
				));
			}
		
		}
	}
	
	
	function getEmailAddressFromId($emailId){
		$res = df_q("select recipient_email from xataface__email_log where `uniqid`='".addslashes($emailId)."'");
		if ( mysql_num_rows($res) == 0 ) return null;
		list($addr) = mysql_fetch_row($res);
		@mysql_free_result($res);
		return $addr;
		
		
	}
	
	
	function checkBlackList($emailId){
		$addr = $this->getEmailAddressFromId($emailId);
		
		$res = df_q("select email from dataface__email_blacklist where `email`='".addslashes($addr)."' limit 1");
		if ( mysql_num_rows($res) == 0 ){
			@mysql_free_result($res);
			return false;
		} else {
			@mysql_free_result($res);
			return true;
		}
	
	}
	
	function out($out){
		header('Content-type: text/json; charset="'.Dataface_Application::getInstance()->_conf['oe'].'"');
		echo json_encode($out);
	}
}