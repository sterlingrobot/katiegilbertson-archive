<?php
class tables_xataface__email_templates {
	


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
		echo "<h1>Manage Email Templates</h1>";
		echo "<div class=\"portalHelp\">This section allows you to manage your email templates.  An email template can be used when sending email to found sets to import a pre-formatted email message, subject, and from heading.</div>";
		echo '<div>[<a href="'.DATAFACE_SITE_HREF.'?-action=email_manage">Back to Email Management</a>]</div>';
	}
	
	public function block__before_email_body_widget(){
		Dataface_JavascriptTool::getInstance()->import('xataface/modules/ckeditor/plugins/SchemaBrowser.js');
		Dataface_ModuleTool::getInstance()->loadModule('modules_Email')->addPaths();
		Dataface_JavascriptTool::getInstance()->import('xataface/modules/Email/email_template_form.js');
	}
	

	
	private $tablenames;
	public function valuelist__tablenames(){
		if ( !isset($this->tablenames) ){
			$this->tablenames = array();
			$app = Dataface_Application::getInstance();
			
			
			if ( isset($app->_conf['_email_tables']) ){
				foreach ($app->_conf['_email_tables'] as $k=>$v){
					$this->tablenames[$k] = $v;
				}
			} else {
				foreach ($app->_conf['_tables'] as $k=>$v){
					$this->tablenames[$k] = $v;
				}
			}
		}
		return $this->tablenames;
	}
	
	
	
	public function getTitle($record){
		return $record->val('template_name');
	}
	
	public function titleColumn(){
		return 'template_name';
	}
	
	
	
}