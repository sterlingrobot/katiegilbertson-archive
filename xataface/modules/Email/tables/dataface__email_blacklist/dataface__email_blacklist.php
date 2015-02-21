<?php
class tables_dataface__email_blacklist {
	public function getPermissions($record){
	
		$dummy = new Dataface_Record('dataface__version', array());
		if ( $dummy->checkPermission('email') ){
			return array(
				'view' => 1,
				'list' => 1,
				'find' => 1,
				'__partial__' => 1,
				'edit'=> 1,
				'new' => 1,
				'delete' => 1
			);
		} else {
			return Dataface_PermissionsTool::NO_ACCESS();
		}
	}
	
	public function block__before_main_column(){
		echo "<h1>Manage Email Black List</h1>";
		echo "<div class=\"portalHelp\">This section allows you to manage the list of people who have opted out of your mailouts.  Addresses on this black-list will automatically be filtered out when sending batch emails.</div>";
		echo '<div>[<a href="'.DATAFACE_SITE_HREF.'?-action=email_manage">Back to Email Management</a>]</div>';
	}
	
}