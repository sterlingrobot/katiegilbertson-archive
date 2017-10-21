/* 
 * Loads content into content div via AJAX
 */

$(function() {
    $('#nav a').click(function() {
       event.preventDefault();
       var targeturl = $(this).attr('href');
       $.ajax({
           url: targeturl,
           dataType: 'html',
           success: function(data) {
               var content = $(data).find('#content').html();
               $('#content').html(content);
               $('#nav li').removeClass('selected');
               $('#nav a').each(function(el){
                   if(targeturl == $(this).attr('href')) {
                       $(this).parent('li').addClass('selected');
                       $(this).parent('li').parent('ul').parent('li').addClass('selected');
                   }
               });
                $('.fadein').css('visibility','hidden');
                var speed = 'slow';
                (function shownext(jq){
                    var factor = jq.eq(0)[0].className;
                    if(factor.indexOf('fast') > -1) speed = 'fast';
                    jq.eq(0).css('visibility','visible').hide().fadeIn(speed, function(){
                        (jq=jq.slice(1)).length && shownext(jq);
                    });
                })($('.fadein'))
           }
       }); 
    });
});
