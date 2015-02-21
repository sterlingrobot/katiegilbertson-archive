<?php
    ini_set('display_errors', '1');

    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_COMPILE_ERROR);

    require('includes/configure.php');
    require('includes/function.resize.php');

    mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
    mysql_select_db(DB_DEFAULT);

    $result = mysql_query("SELECT a.provider, a.award, a.laurel_image FROM awards_to_projects a ORDER BY RAND()");
    $awards = array();
    while($row = mysql_fetch_assoc($result)) {
            $awards[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning Documentary Filmmaker and Video Editor in Bozeman, specializing in Documentary Editing and Story Consulting & Development">
	<title>Professional Video Editor | Documentary Film | Writing Consultant</title>
        <link href="/css/global.css" rel="stylesheet" type="text/css" />
        <link href="/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="/css/projects.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            #news {
                position: absolute;
                z-index: 1;
                left: 11em;
                top: 3em;
                padding: 0.5em;
                background: rgba(255,255,255,0.5);
                border-radius: 1em;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
                box-shadow: 4px 4px 8px rgba(255,255,255,0.8);
            }
            #news h4 {
                color: darkgreen;
                margin: 0 0 0 95px;
            }
            #news img {
                float: left;
                border-radius: 0.75em;
                padding: 0.2em;
                background: rgba(255,255,255,0.3);
            }
            #news a img:hover {
                background: rgba(127, 255, 0,0.3);
            }
            #news a h4:hover {
                color: rgb(127, 255, 0);
            }
        </style>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.0.0.js"></script>
        <script src="http://malsup.github.io/jquery.cycle.all.js"></script>
        <script type="text/javascript">
            $(function() {
                $('.fadein').css('visibility','hidden');
                $('#nav').hide();
                (function shownext(jq){
                    jq.eq(0).css('visibility','visible').hide().fadeIn(1000, function(){
                        (jq=jq.slice(1)).length && shownext(jq);
                    });
                })($('.fadein'))
                setTimeout(function () { $('#nav').fadeIn(1000); }, 3000);
                $('.awards').animate({ left : '-300em' }, 400000, 'linear');
            })
        </script>
        <!--<script src="javascript/content.js" type="text/javascript"></script>-->
        <script src="/javascript/menu.js" type="text/javascript"></script>
        <script src="/javascript/threedots.js" type="text/javascript"></script>
    </head>
    <body id="main">
        <div id="wrapper">
            <div id="header">
                <?php include('menu.php'); ?>
            </div>
            <div id="news">
                <a href="/p/indian-relay/editor/1">
                    <img src="http://www.katiegilbertson.com/includes/cache/4ab5026642f36e54b5ea5052371a90ed_w128_h128_cp_sc.jpg" width="55" height="55" />
                    <h4>Indian Relay<br/>EMMY<sup>&reg;</sup> Winner!<br>Documentary - Cultural<br><small>NATAS Northwest</small></h4>
                </a>
            </div>
            <div id="content">
                <div id="bkImage">
                    <img src="/images/profile.jpg" border="0" alt="Professional Video Editor | Documentary Film | Writing Consultant" />
                </div>
                <section>
                    <h1 class="fadein">Katie Lose Gilbertson<span id="title"><strong>Story Arc</strong>itech</span></h1>
                    <div id="qualities">
                        <span class="fadein">Story</span>
                        <span class="fadein">Connection</span>
                        <span class="fadein">Clarity</span>
                    </div>

                    <p><strong>Katie Lose Gilbertson</strong> is an award winning documentary filmmaker and editor who weaves compelling stories that capture your heart, make you laugh, and make you tune out the world around you.
                    <p>I want to help you tell your story.
                </section>
                <div class="awards" style="position: absolute; left: 0; bottom: 0; width: 150%; height: 100px;">
                <?php foreach($awards as $award) : ?>
                    <div class="award laurel float" style="width: 12em; opacity: 0.7; <?=($award['laurel_image'] != null)? 'background: none;' : ''?>">
                        <img src="<?=($award['laurel_image'] != null)? $award['laurel_image'] : 'css/images/1px_spacer.gif'?>" height="<?=($award['laurel_image'] != null)? '80' : '1'?>" />
                        <?php if($award['laurel_image'] == null) : ?>
                        <h4 class="provider"><?=$award['provider']?></h4>
                        <p class="awardname"><?=$award['award']?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>

            <div id="footer">
                <footer>
                    <?php include('footer.php'); ?>
                </footer>
            </div>
        </div>
    </body>
</html>
