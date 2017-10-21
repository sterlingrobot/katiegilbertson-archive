<?php
class tables_xataface__email_attachments {


	function init($table){
	
		$field =& $table->getField('file');
		$app = Dataface_Application::getInstance();
		if ( @$app->_conf['modules_Email'] ){
			$conf = $app->_conf['modules_Email'];
			if ( @$conf['attachments'] ){
				$field['savepath'] = $conf['attachments'];
				
			}
			if ( @$conf['attachments_url']){
				$field['url'] = $conf['attachments_url'];
			}
		}
		
		$savepath = $field['savepath'];
		
		if ( !is_dir($savepath) or !is_writable($savepath) ){

			$field['widget']['type'] = 'hidden';
		}

	}

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
		// Create a record for a table we know exists so we can get
		// the default application permissions.
		$dummy = new Dataface_Record('dataface__version', array());
		if ( $dummy->checkPermission('email') ){
			$perms =  array(
				'view' => 1,
				'__partial__' => 1,
				'edit'=>1,
				'new' => 1,
				'import' => 0,
				'copy' => 0,
				'update_set'=>0
			);
			
			return $perms;
		} else {
			return Dataface_PermissionsTool::NO_ACCESS();
		}
	}
}