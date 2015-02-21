<?php
class actions_email_resume_job {
	const PERMISSION_DENIED = 8401.0;
	const JOB_NOT_FOUND = 8404.1;
	const EMAIL_NOT_FOUND = 8404.2;
	
	
	
	function handle($params){
		session_write_close();
		header('Connection:close');
		$app = Dataface_Application::getInstance();
		$query = $app->getQuery();
		
		try {
		
			if ( !@$query['--job-id'] ){
				throw new Exception("No job ID specified");
			}
			
			$job = df_get_record('xataface__email_jobs', array('job_id'=>'='.$query['--job-id']));
			if ( !$job ){
				throw new Exception("Job could not be found", self::JOB_NOT_FOUND);
			}
			
			$email = df_get_record('xataface__email_newsletters', array('id'=>'='.$job->val('email_id')));
			if ( !$email ){
				throw new Exception("Email could not be found", self::EMAIL_NOT_FOUND);
			}
			
			
			
			if ( !$job->checkPermission('cancel email job') or !$email->checkPermission('cancel email job') ){
				throw new Exception("You don't have permission to cancel this job", self::PERMISSION_DENIED);
			}
			
			
			df_q("update xataface__email_jobs set cancelled=0 where job_id='".addslashes($query['--job-id'])."'");
			
			$this->out(array(
				'code' => 200,
				'message' => 'Successfully resumed job'
			));
		
		} catch (Exception $ex){
		
			if ( $ex->getCode() > 8000 and $ex->getCode() < 9000 ){
				$this->out(array(
					'code' => intval($ex->getCode()-8000),
					'message' => $ex->getMessage()
				));
			} else {
				throw $ex;
			}
		}
	}
	
	
	function out($out){
		header('Content-type: text/json; charset="'.Dataface_Application::getInstance()->_conf['oe'].'"');
		echo json_encode($out);
	
	}
}