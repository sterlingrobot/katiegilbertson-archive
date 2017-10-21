<?php
class tables_xataface__email_log {


	/**
	 * @brief Disables acess to the email log directly.  We don't want
	 * direct access to it.  Access should only be granted through 
	 * the log in the email_newsletters table.
	 */
	function getPermissions($record){
	
		return Dataface_PermissionsTool::NO_ACCESS();
	}
}