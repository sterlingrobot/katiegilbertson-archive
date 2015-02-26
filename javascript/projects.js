

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
                 $('.subproject_item:first').css('margin-left', '11em');
                 $('#project_id_'+id+'_data').html(data).fadeIn('fast');
                 $('#project_id_'+id+' .synopsis').hide();
                 var desc = $('#project_id_'+id+'_data .full_desc').html();
                 $('#project_id_'+id+' .project_desc').html(desc);
                 $('.project_item .image, .project_item h2').each( function() { $(this).addClass('selected')}).css('cursor', 'default');
                 $('#backbtn').css('visibility', 'visible');
                 $('.cycle-slideshow').cycle();
            }
        });
    }
}