<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning documentary filmmaker and editor in Bozeman, specializing in Editing and Story Consulting & Development">

        <title>Katie Lose Gilbertson :: Contact</title>
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
                    <img src="images/profile-contact.jpg" border="0" alt="Katie Lose Gilbertson :: Editor | Writer | Story Consultant" />
                </div>
                <section>
                    <h1 class="fadein">Contact Me</h1>
                    <p>Need help finding your story?  Or do you already know it?  I can help from story development through editing.</p>
                    <div id="qualities">
                        <span class="fadein">Documentary Editing</span><br/>
                        <span class="fadein">Narrative Editing</span><br/>
                        <span class="fadein">Story Consulting & Development</span><br/>
                        <span class="fadein">Writing</span>
                    </div>
                    <h3>Katie Lose Gilbertson</h3>
                    <p>C: 406.624.9888
                    <p><a href="mailto:katie@katiegilbertson.com" title="Email">katie[at]katiegilbertson.com</a>
                    <p><a href="//www.linkedin.com/profile/view?id=63497429" target="_blank" title="LinkedIn">
                            <img src="images/LinkedIn_Logo30px.png" width="30" />
                        </a>
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
