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

//$(function(){
//
//    $("ul.dropdown li").hover(function(){
//    
//        $(this).addClass("hover");
//        $('ul:first',this).css('visibility', 'visible');
//    
//    }, function(){
//    
//        $(this).removeClass("hover");
//        $('ul:first',this).css('visibility', 'hidden');
//    
//    });
//    
//    $("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
//
//});

//$(function(){
//
//    var config = {    
//         sensitivity: 3, // number = sensitivity threshold (must be 1 or higher)    
//         interval: 200,  // number = milliseconds for onMouseOver polling interval    
//         over: doOpen,   // function = onMouseOver callback (REQUIRED)    
//         timeout: 200,   // number = milliseconds delay before onMouseOut    
//         out: doClose    // function = onMouseOut callback (REQUIRED)    
//    };
//    
//    function doOpen() {
//        $(this).addClass("hover");
//        $('ul:first',this).css('visibility', 'visible');
//    }
// 
//    function doClose() {
//        $(this).removeClass("hover");
//        $('ul:first',this).css('visibility', 'hidden');
//    }
//
//    $("ul.dropdown li").hoverIntent(config);
//    
//    $("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
//
//});

