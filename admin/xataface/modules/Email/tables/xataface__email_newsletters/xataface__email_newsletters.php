<?php
class tables_xataface__email_newsletters {

	
	


	/**
	 * @brief The permissions for this table.  Since this is a module we wanted to keep it 
	 * flexible.  The rules are as follows:
	 * 
	 * <ol>
	 * <li>If the current table is the newsletters table, then it only allows read only
	 * 		access to the user that posted the message.  Other users get no access.
	 * </li>
	 * <li>If the current table is another table we grant the "new" permission if the user
	 * 		has the "email" permission for the current table.  This allows the user to 
	 * 		use the new record form that is displayed in the email action.
	 * </li>
	 */
	public function getPermissions($record){
		
	
		$app = Dataface_Application::getInstance();
		$query =& $app->getQuery();
		
		if ( $query['-table'] == 'xataface__email_newsletters' ){
			// Create a record for a table we know exists so we can get
			// the default application permissions.
			$dummy = new Dataface_Record('dataface__version', array());
			if ( $dummy->checkPermission('email') ){
				$perms =  array(
					'view' => 1,
					'list' => 1,
					'find' => 1,
					'__partial__' => 1,
					'edit'=>0,
					'new' => 0,
					'import' => 0,
					'copy' => 0,
					'update_set'=>0
				);
				
				if ( class_exists('Dataface_AuthenticationTool') ){
					$username = Dataface_AuthenticationTool::getInstance()->getLoggedInUserName();
					if ( $record and $username and $username == $record->val('posted_by') ){
						$perms['cancel email job'] = 1;
					}
				}
				return $perms;
			} else {
				return Dataface_PermissionsTool::NO_ACCESS();
			}
		
		} else {
			$table = Dataface_Table::loadTable($query['-table']);
			$perms = $table->getPermissions();
			if ( @$perms['email'] ){
				$out =  array('new'=>1);
				if ( class_exists('Dataface_AuthenticationTool') ){
					$username = Dataface_AuthenticationTool::getInstance()->getLoggedInUserName();
					if ( $record and $username and $username == $record->val('posted_by') ){
						$out['cancel email job'] = 1;
					}
				}
				
				return $out;
			} else {
				return Dataface_PermissionsTool::NO_ACCESS();
			}
		
		}
	}
	
	
	public function rel_log____field__permissions($record){
	
		if ( $record->checkPermission('view') ){
			return Dataface_PermissionsTool::READ_ONLY();
		} else {
			return Dataface_PermissionsTool::NO_ACCESS();
		}
	}
	
	
	public function rel_attachments__permissions($record){
		$dummy = new Dataface_Record('dataface__version', array());
		if ( $dummy->checkPermission('email') ){
			$perms =  array(
				'add new related record' => 1,
				'remove related record' => 1,
				'delete related record' => 1
			);
			
			
			return $perms;
		} 
		return null;
	}

	
	/**
	 * @brief Called before a record is inserted into the newsletters table.
	 *  This sets the posted_by field to the currently logged-in user.
	 */
	public function beforeInsert($record){
		
		if( class_exists('Dataface_AuthenticationTool') ){
			$auth = Dataface_AuthenticationTool::getInstance();
		
			$record->setValue('posted_by', $auth->getLoggedInUserName());
		}
	}
	
	public function block__after_form_open_tag(){
		
	}
	
	
	/**
	 * @brief Adds some content to the beginning of the email form to display some
	 * instructions and add some interactivity on the form.
	 */
	public function block__before_content_widget(){
		Dataface_ModuleTool::getInstance()->loadModule('modules_Email')->addPaths();
		
		Dataface_JavascriptTool::getInstance()->import('xataface/modules/Email/email_form.js');
		echo "<div id=\"template-instructions\"></div>";
	}
	
	
	public function block__before_main_section(){
		//echo '<h2>Email History</h2>';
	
	}
	
	public function section__progress($record){
	
		ob_start();
		$mod = Dataface_ModuleTool::getInstance()->loadModule('modules_Email');
		$mod->addPaths();
		
		$job = df_get_record('xataface__email_jobs', array('email_id'=>'='.$record->val('id')));
		if ( !$job ) return null;
		
		Dataface_JavascriptTool::getInstance()->import('xataface/modules/Email/email_progress.js');
		df_display(array(
				'jobId' => $job->val('job_id')
			),
			'xataface/modules/email/email_progress_section.html'
		);
		
		$contents = ob_get_contents();
		ob_end_clean();
	
	
		return array(
			'content' => $contents,
			'label' => 'Progress',
			'order' => 10,
			'class' => 'main'
		);
	}
	
	
	function attachments__permissions($record){
		$app = Dataface_Application::getInstance();
		if ( @$app->_conf['modules_Email'] and @$app->_conf['modules_Email']['attachments'] ){
			return array('view'=>1,'edit'=>1,'new'=>1);
		} else {
			return Dataface_PermissionsTool::NO_ACCESS();
		}
	}
	
	public function block__before_main_column(){
		echo "<h1>Email History</h1>";
		echo "<div class=\"portalHelp\">This section allows you to browse the history of emails that have been sent by the system.</div>";
		echo '<div>[<a href="'.DATAFACE_SITE_HREF.'?-action=email_manage">Back to Email Management</a>]</div>';
	}
	
	
	public function start_time__display($record){
		if ( !$record->val("start_time") ) return '';
		else return strftime('%c', $record->val('start_time'));
	}
	
	public function end_time__display($record){
		if ( !$record->val('end_time') ) return '';
		else return strftime('%c', $record->val('end_time'));
	}
	
	
	public function valuelist__archive_categories(){
		return array(
			1=>'Recent Jobs',
			2 => 'Archived Jobs'
		);
	}
	
	


	
}