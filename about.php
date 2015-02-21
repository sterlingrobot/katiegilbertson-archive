<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning documentary filmmaker and editor in Bozeman, specializing in Editing and Story Consulting & Development">
	<title>Katie Lose Gilbertson :: About</title>
        <link href="css/global.css" rel="stylesheet" type="text/css" />
        <link href="css/menu.css" rel="stylesheet" type="text/css" /> 
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>  
        <script type="text/javascript">
            $(function() {
                $('.fadein').css('visibility','hidden');
                var speed = 'slow';
                (function shownext(jq){
                    var factor = jq.eq(0)[0].className;
                    if(factor.indexOf('fast') > -1) speed = 'fast';
                    jq.eq(0).css('visibility','visible').hide().fadeIn(speed, function(){
                        (jq=jq.slice(1)).length && shownext(jq);
                    });
                })($('.fadein'))
            })
        </script> 
    
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <?php include('includes/menu.php'); ?>
            </div>
            <div id="content">
                <div id="bkImage">
                    <img src="images/profile-about.jpg" border="0" alt="Katie Lose Gilbertson :: Editor | Writer | Story Consultant" />
                </div>
                <section>
                    <h1 class="fadein">About Me</h1>
                    <p>An award winning documentary filmmaker and editor based in beautiful Bozeman, Montana.  Her work has screened on PBS, TERRA, Current TV, National Parks, and in numerous film festivals worldwide.  Additionally, Stories of Trust is being used across the country to promote public engagement in climate change, and is a tool in ongoing litigation for a Climate Recovery Plan that is based on science and human rights, not political agenda.
 
                    <p>Katie holds an MFA in Science and Natural History Filmmaking, a BFA in Theatre, and a minor in biology. The fusion of these artistic mediums creates a complexity in her storytelling offering great attention to audience, emotional engagement, pacing, revelation, and story arc. 
                        <br>
                        <p><a href="contact.php">Contact Me</a>
                </section>
            </div>
            <div id="footer">
                <footer>
                    <?php include('includes/footer.php'); ?>
                </footer>
            </div>
        </div>
    </body>
</html>
