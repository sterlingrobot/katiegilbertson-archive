

function loadData(el, auto) {
	'use strict';

	$('#backbtn').css('visibility', 'hidden');
	if(typeof(auto) == 'undefined') auto = true;
	if(auto) event.preventDefault();
	if(!el.hasClass('selected')) {
		var id = /(\/|=)\d{1,2}$/.test(window.location.href) && /\d{1,2}$/.exec(window.location.href)[0];
		var url;
		if(id) {
			url = '/project_data.php?id='+id;
		} else if(el.parent().hasClass('subproject_item')) {
			id = el.closest('.project_item').attr('id').split('_').pop();
			url = '/project_data.php?id='+el.parent().get(0).id.split('_').pop();
		} else {
			id = el.closest('.project_item').attr('id').split('_').pop();
			url = '/project_data.php?id='+id;
		}
		$('#project_id_'+id+'_data').html('<img style="text-align:left;" src="/css/images/loader.gif" />').fadeIn('fast');
		$.ajax({
			url: url,
			success: function(data) {

				var $ctr = $('#project_id_'+id+'_data').length ?
					$('#project_id_'+id+'_data') :
					$('<div id="project_id_' + id + '_data"></div>')
						.appendTo('#content')
						.wrap('<div id="project_id_' + id + '" class="project_item"></div>')
						.closest('.project_item')
						.wrap('<section></section>');

				$('.subproject_item:first').css('margin-left', '11em');

				$ctr.html(data).fadeIn('fast');
				$('#project_id_'+id+' .synopsis').hide();

				var desc = $('#project_id_'+id+'_data .full_desc').html();
				$('#project_id_'+id+' .project_desc').html(desc);

				$('.project_item .image, .project_item h2').each( function() {$(this).addClass('selected')}).css('cursor', 'default');
				$('#backbtn').css('visibility', 'visible');
				$('.cycle-slideshow').cycle();
			}
		});
	}
}
