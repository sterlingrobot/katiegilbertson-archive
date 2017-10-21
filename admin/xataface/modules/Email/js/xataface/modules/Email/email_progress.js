//require <jquery.packed.js>
//require <jquery-ui.min.js>
//require-css <jquery-ui/jquery-ui.css>

/**
 * @description
 * This script provides javascript interactivity for the 
 * xataface/modules/email/email_progress.html and
 * xataface/modules/email/email_progress_section.html templates.
 *
 * <p>This page is meant to show the user the progress of an email job.
 * it also serves as the catalyst to start the job (via the postJob() function).</p>
 *
 * <p>It periodically polls the server to see the job status and this polling will
 * start the job if it hasn't started yet.</p>
 *
 */
(function(){
	var $ = jQuery;
	$(document).ready(function(){
	
		var preparePanel = $('#email-preparing-panel');
		var progressPanel = $('#email-progress-panel').hide();
		var successPanel = $('#email-complete-panel').hide();
		var cancelledPanel = $('#email-cancelled-panel').hide();
		var jobId = progressPanel.attr('data-job-id');
		var complete = false;
		var cancelled = false;
		
		postJob(jobId);
		
		var interval = setInterval(function(){
				postJob(jobId);
			}, 
			1000
		);
		
		$('#job-progress-bar').progressbar();
		
		$('.cancel-button').click(function(){
			cancelJob(jobId);
		});
		
		$('.resume-button').click(function(){
			resumeJob(jobId);
		});
		
		
		
		
		
		/**
		 * @description
		 * Performs AJAX request to the server to run the specified job.  If the job
		 * is already running, then it will simply return the job status.
		 *
		 * <p>This also updates the UI to display the status of the job.</p>
		 *
		 * @param {int} jobId The ID of the job.
		 *
		 * @see email_cron_job action
		 */
		function postJob(jobId){
			var url = DATAFACE_SITE_HREF;
			var q = {
				'-action': 'email_cron_job',
				'--job-id': jobId,
				'-table': 'xataface__email_newsletters'
			};
			
			$.post(url, q, function(res){
			
				try {
					if ( res.code == 200 ){
						// Job is complete
						complete = true;
						preparePanel.hide();
						progressPanel.hide();
						successPanel.show();
						cancelledPanel.hide();
						$('#total-attempted').text(res.data.sent_emails);
						$('#total-successful').text(res.data.successful_emails);
						$('#total-failed').text(res.data.failed_emails);
						$('#total-blacklisted').text(res.data.blacklisted_emails);
						$('#job-started-at').text(res.data.start_time);
						$('#job-finished-at').text(res.data.end_time);
						clearInterval(interval);
					
					} else if ( res.code == 201 ){
						// Job is in progress
						if ( !complete ){
							preparePanel.hide();
							progressPanel.show();
							cancelledPanel.hide();
							
							var sent = res.data.sent_emails*1.0;
							var total = res.data.total_emails*1.0;
							var ratio = 0.0;
							if ( total > 0 ){
								ratio = Math.floor(sent/total*100.0);
							}
	
							$('#job-progress-bar').progressbar({value: ratio});
							progressPanel.each(function(){
								$('.now-sending', this).text(parseInt(res.data.sent_emails)+1);
								$('.num-emails-in-job', this).text(res.data.total_emails);
								$('.num-attempted', this).text(res.data.sent_emails);
								$('.num-successful', this).text(res.data.successful_emails);
								$('.num-failed', this).text(res.data.failed_emails);
								$('.num-blacklisted', this).text(res.data.blacklisted_emails);
							
							});
						}
					
					} else if ( res.code == 301 ){
						// The job is currently cancelled
						preparePanel.hide();
						progressPanel.hide();
						successPanel.hide();
						cancelledPanel.show();
						cancelled = true;
						cancelledPanel.each(function(){
							$('.now-sending', this).text(parseInt(res.data.sent_emails)+1);
							$('.num-emails-in-job', this).text(res.data.total_emails);
							$('.num-attempted', this).text(res.data.sent_emails);
							$('.num-successful', this).text(res.data.successful_emails);
							$('.num-failed', this).text(res.data.failed_emails);
							$('.num-blacklisted', this).text(res.data.blacklisted_emails);
						
						});
						
						clearInterval(interval);
						
						
						
					
					} else if ( res.code == 404 ){
						throw 'Could not find job';
						
					}
				
				} catch (e){
				
					$('.portalMessage').text(res);
				}
			});
		
		}
		
		
		
		function cancelJob(jobId){
		
			var q = {
				'-action': 'email_cancel_job',
				'--job-id': jobId,
				'-table': 'xataface__email_newsletters'
			};
			
			$.post(DATAFACE_SITE_HREF, q, function(res){
				try {
					if ( res.code != 200 ){
						throw "Failed to cancel job: "+res.message;
					}
				} catch (e){
					alert(e);
				}
			
			});
		}
		
		
		function resumeJob(jobId){
			cancelledPanel.hide();
			preparePanel.fadeIn();
			
		
			var q = {
				'-action': 'email_resume_job',
				'--job-id': jobId,
				'-table': 'xataface__email_newsletters'
			};
			
			$.post(DATAFACE_SITE_HREF, q, function(res){
				try {
					if ( res.code != 200 ){
						throw "Failed to resume job: "+res.message;
					} else {
						
						try {
							clearInterval(interval);
						} catch (e){}
						interval = setInterval(function(){
								postJob(jobId);
							}, 
							1000
						);
						
					}
				} catch (e){
					alert(e);
				}
			
			});
		}
		
		
	});
	
	

})();