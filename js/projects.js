

function loadData(el, auto) {
    $('#backbtn').css('visibility', 'hidden');
    if(typeof(auto) == 'undefined') auto = true;
    if(auto) event.preventDefault();
    if(!el.hasClass('selected')) {
        var id;
        var url;
        if(el.parent().hasClass('subproject_item')) {
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
//               $('.project_item:not(#project_id_'+id+')').each(function() { $(this).slideUp(1000) });
                 $('.subproject_item:first').css('margin-left', '11em');
//               $('#project_id_'+id+'_data').hide().html(data).slideDown(1000);
                 $('#project_id_'+id+'_data').html(data).fadeIn('fast');
                 $('#project_id_'+id+' .synopsis').hide();
                 var desc = $('#project_id_'+id+'_data .full_desc p').html();
                 $('#project_id_'+id+' .project_desc p').html('<span class="ellipsis_text">'+desc+'</span>');
                 $('.project_item .image, .project_item h2').each( function() { $(this).addClass('selected')}).css('cursor', 'default');
                 $('#backbtn').css('visibility', 'visible');
//                 $('#backbtn a').on('click', function() {
//                     event.preventDefault();
//                     $('.project_item .image, .project_item h2').each( function() { $(this).removeClass('selected')}).css('cursor', 'pointer');
//                     $('#project_id_'+id+'_data').slideUp(1000);
//                     $('.subproject_item:first').css('margin-left', '0.2em');
//                     var trunc = $('#project_id_'+id+' .project_desc p').ThreeDots();
//                     $('.project_item').slideDown(1000);
//                     $('#backbtn').css('visibility', 'hidden');
//                     $(' .synopsis').show();
//
//                 })
            }
        });
    }
}

//$('#backbtn').css('visibility', 'hidden');
//$('.project_item .image, .project_item h2, a.subproject').on('click', function(){ loadData($(this)) });



