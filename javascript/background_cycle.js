function swapBkImage() {
    if(bkImages.length > index) index++;
    else index = 0;
    $('#bkImage img').attr('src', bkImages[index]).fadeIn().delay(3000).fadeOut();
}

$(document).ready(function() {
    if(typeof(bkImages) != undefined) {    
        if(bkImages.length > 1) {
            var t = window.setInterval('swapBkImage()', 6000);
        }
    }

})

