var tim;

$(function(){
    $('.projects').hover(
        function(){
            $('.subprojects').fadeIn(500);
        },
        function(){
            tim = setTimeout('hideSubprojects()', 500);
        }
    );
    $('.subprojects').hover(
        function() {
            $(this).css('display', 'block');
            clearTimeout(tim);
        },
        function(){
            tim = setTimeout('hideSubprojects()', 500);
        });

});

function hideSubprojects() {
    $('.subprojects').fadeOut(500);
}
