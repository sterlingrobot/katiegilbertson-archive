<?php
class actions_email_manage {

	function handle($params){
	
		df_register_skin('email_module', dirname(__FILE__).'/../templates');
		
		df_display(array(), 'xataface/modules/email/manage.html');
	}
}