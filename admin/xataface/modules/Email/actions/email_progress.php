<?php
class actions_email_progress {

	function handle($params){
	
		$app = Dataface_Application::getInstance();
		$query = $app->getQuery();
		$jobId = @$query['-job-id'];
		
		if ( !@$jobId ) throw new Exception("No job id provided");
		
		$jobRecord = df_get_record('xataface__email_jobs', array("job_id"=>'='.$jobId));
		if ( !$jobRecord ){
			throw new Exception("The job could not be found.");
		}
		
		$emailRecord = df_get_record('xataface__email_newsletters', array('id'=>'='.$jobRecord->val('email_id')));
		if ( !$emailRecord ){
			throw new Exception("The email for this job could not be found.");
		}
		if ( class_exists('Dataface_AuthenticationTool') ){
			
			
			$postedBy = $emailRecord->val('posted_by');
			if ( $postedBy != Dataface_AuthenticationTool::getInstance()->getLoggedInUserName() ){
				return Dataface_Error::permissionDenied("Only the user who sent this email can see its progress.");
				
			}
		}
		
		$mod = Dataface_ModuleTool::getInstance()->loadModule('modules_Email');
		$mod->addPaths();
		
		Dataface_JavascriptTool::getInstance()->import('xataface/modules/Email/email_progress.js');
		df_display(array(
				'jobId' => $jobId
			),
			'xataface/modules/email/email_progress.html'
		);
		
		
	
	}

}