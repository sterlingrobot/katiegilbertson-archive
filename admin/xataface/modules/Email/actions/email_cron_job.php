<?php

class actions_email_cron_job {
	const JOB_IN_PROGRESS = 201;
	const JOB_NOT_FOUND = 404;
	const JOB_COMPLETE = 200;
	const JOB_CANCELLED = 301;
	
	private $mutex;
	function handle($params){
		session_write_close();
		ignore_user_abort(true);
		set_time_limit(0);
		$app = Dataface_Application::getInstance();
		$app->_conf['nocache'] = 1;
		$query = $app->getQuery();
		
		if ( !@$query['--job-id'] ){
			throw new Exception("No job id provided");
		}
		
		$res = $this->performJob($query['--job-id']);
		
		switch ($res){
		
			case self::JOB_IN_PROGRESS:
				$res2 = df_q("select * from xataface__email_jobs where job_id='".addslashes($query['--job-id'])."'");
				$row = mysql_fetch_assoc($res2);
				if ( !$row ){
					// No progress listed
					throw new Exception("The job could not be found", self::JOB_NOT_FOUND);
				}
				$row['start_time'] = strftime('%c', $row['start_time']);
				$out = array(
					'code' => self::JOB_IN_PROGRESS,
					'message' => 'Job in progress',
					'data' => $row
				);
				
				$this->out($out);
				break;
				
			case self::JOB_COMPLETE:
				$res2 = df_q("select * from xataface__email_jobs where job_id='".addslashes($query['--job-id'])."'");
				$row = mysql_fetch_assoc($res2);
				if ( !$row ){
					// No progress listed
					throw new Exception("The job could not be found", self::JOB_NOT_FOUND);
				}
				$row['start_time'] = strftime('%c', $row['start_time']);
				$row['end_time'] = strftime('%c', $row['end_time']);
				$out = array(
					'code' => self::JOB_COMPLETE,
					'message' => 'Job complete',
					'data' => $row
				);
				
				$this->out($out);
				break;
				
			case self::JOB_CANCELLED:
				$res2 = df_q("select * from xataface__email_jobs where job_id='".addslashes($query['--job-id'])."'");
				$row = mysql_fetch_assoc($res2);
				if ( !$row ){
					// No progress listed
					throw new Exception("The job could not be found", self::JOB_NOT_FOUND);
				}
				$row['start_time'] = strftime('%c', $row['start_time']);
				$out = array(
					'code' => self::JOB_CANCELLED,
					'message' => 'Job paused',
					'data' => $row
				);
				
				$this->out($out);
				break;
			
				
				
			default:
				$out = array(
					'code' => $res,
					'message' => 'An error occurred trying to perform job.'
				);
				$this->out($out);
				break;
		}
		
		
	
	}
	
	
	function performJob($jobId){
		if  (!$this->mutex('email_cron_job_'.basename($jobId)) ){
			return self::JOB_IN_PROGRESS;
		}
		require_once dirname(__FILE__).'/email.php';
		$action = new actions_email;
		
		$res = df_q("select * from xataface__email_jobs where job_id='".addslashes($jobId)."'");
		
		$row = mysql_fetch_assoc($res);
		if ( !$row ){
			return self::JOB_NOT_FOUND;		
		}
		
		if ( $row['complete'] ){
			return self::JOB_COMPLETE;
		}
		
		if ( $row['cancelled'] ){
			return self::JOB_CANCELLED;
		}
		
		//echo "\nSending mail for job $row[job_id] ...";
		$res2 = mysql_query("delete from `".$row['join_table']."` where recipient_email='' and messageid='".addslashes($row['email_id'])."'", df_db());
		$action->sendMail($row['email_id'],$row['email_table'],$row['join_table'],$row['recipients_table'],$row['email_column']);		
		
		// check to see if all the messages for this job have been sent yet
		$res2 = mysql_query("select count(*) from `".$row['join_table']."` where sent<>1 and messageid='".addslashes($row['email_id'])."'", df_db());
		if ( !$res2 ) trigger_error(mysql_error(df_db()), E_USER_ERROR);
		list($num)=mysql_fetch_row($res2);
		@mysql_free_result($res2);
		if ( $num==0 ){
		
			$res2 = df_q("update xataface__email_jobs set active=0, complete=1, end_time='".addslashes(time())."' where job_id='".addslashes($jobId)."'", df_db());
			
			return self::JOB_COMPLETE;
			//echo "\nJob $row[job_id] is complete!  Deleting job...";
			//$res2 = mysql_query("delete from dataface__email_jobs where job_id='".addslashes($row['job_id'])."' limit 1", df_db());
			//if ( !$res2 ) trigger_error(mysql_error(df_db()), E_USER_ERROR);
		} else {
			//echo "\nAfter sending mail for job $row[job_id], there are still $num messages left to send.";
			$res = df_q("select * from xataface__email_jobs where job_id='".addslashes($jobId)."'");
		
			$row = mysql_fetch_assoc($res);
			if ( !$row ){
				return self::JOB_NOT_FOUND;		
			}
			
			if ( $row['cancelled'] ){
				return self::JOB_CANCELLED;
			} else {
			
				return self::JOB_IN_PROGRESS;
			}
		}
		
	}
	
	
	
	/**
	 * Obtain a mutex (to make sure we aren't running multiple instances
	 * of this script concurrently.
	 *
	 * This function will return true if it succeeded in obtaining the mutex
	 *	(i.e.  no other instance of this script is running.  And false otherwise.
	 * @param string $name The name of the mutex to acquire.
	 */
	function mutex($name){
		
		$path = sys_get_temp_dir().'/'.$name.'.mutex';
		//echo $path;
		$this->mutex = fopen($path, 'w');
		if ( flock($this->mutex, LOCK_EX | LOCK_NB) ){
			register_shutdown_function(array($this,'clear_mutex'));
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Clears the most recently acquired mutex.
	 */
	function clear_mutex(){
		
		if ( $this->mutex ){
			fclose($this->mutex);
		}
	}
	
	
	function out($out){
		header('Content-type: text/json; charset="'.Dataface_Application::getInstance()->_conf['oe'].'"');
		echo json_encode($out);
	}

}