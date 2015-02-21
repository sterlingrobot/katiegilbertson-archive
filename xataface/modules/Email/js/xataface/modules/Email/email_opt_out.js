//require <jquery.packed.js>
(function(){

	var $ = jQuery;
	
	var wrapper = $('#email-opt-out-wrapper').css('padding', '20px');
	
	function showSlide(name){
		$('.slide', wrapper).hide();
		$('.slide.'+name, wrapper).fadeIn();
	
	}
	
	
	var emailId = wrapper.attr('data-email-id');
	var currentlyBlackListed = wrapper.attr('data-email-currently-blacklisted');
	
	if ( currentlyBlackListed ) showSlide('opted-out');
	else showSlide('opted-in');
	
	
	$('button.opt-out', wrapper).click(function(){
		$('button.opt-out', wrapper).attr("disabled", true);
		optOut(emailId);
	});
	
	$('button.opt-in', wrapper).click(function(){
		$('button.opt-in', wrapper).attr("disabled", true);
		optIn(emailId);
	});
	
	$('a.re-opt-in-link', wrapper)
		.attr('href', window.location.href)
		.click(function(){
		showSlide('opted-out');
		return false;
	});
	
	$('a.re-opt-out-link', wrapper)
		.attr('href', window.location.href)
		.click(function(){
		showSlide('opted-in');
		return false;
	});
	
	
	
	
	function optOut(emailId){
	
		var q = {
		
			'-action': 'email_opt_out',
			'--email-id': emailId,
			'--opt-out': 1
		};
		
		$.post(DATAFACE_SITE_HREF, q, function(res){
			$('button.opt-out', wrapper).attr("disabled", false);
		
			try {
				
				if ( res.code == 200 ){
					showSlide('opt-out-success');
				} else {
					throw res.message || 'Failed due to server error';
				}
			
			} catch (e){
			
				alert(e);
			}
		});
	}
	
	
	function optIn(emailId){
	
		var q = {
		
			'-action': 'email_opt_out',
			'--email-id': emailId,
			'--opt-in': 1
		};
		
		$.post(DATAFACE_SITE_HREF, q, function(res){
			$('button.opt-in', wrapper).attr("disabled", false);
			try {
				
				if ( res.code == 200 ){
					showSlide('opt-in-success');
				}
			
			} catch (e){
			
				alert(e);
			}
		});
	}

})();