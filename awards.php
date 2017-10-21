<!DOCTYPE html>

<?php

    require('includes/configure.php');
    require('includes/function.resize.php');

    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DEFAULT . ';charset=utf8', DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $where = (isset($_GET['id'])) ? ' WHERE p.id = :id' : '';

    $stmt = $db->prepare("SELECT p.id, a.provider, a.award, a.laurel_image, p.name, YEAR(p.date_completed) AS date_completed, p.status, p.role FROM awards_to_projects a LEFT JOIN projects p ON p.id = a.projects_id $where  ORDER BY date_completed DESC");
    $stmt->bindParam('id', $_GET['id']);
    $stmt->execute();

    $awards = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $awards[$row['id']][] = $row;
    }
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Katie Lose Gilbertson :: Awards</title>
        <link href="css/global.css" rel="stylesheet" type="text/css" />
        <link href="css/menu.css" rel="stylesheet" type="text/css" />
        <link href="css/projects.css" rel="stylesheet" type="text/css" />

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
                <section>
                    <h1 class="fadein">Awards</h1>
                    <?php foreach($awards as $project) : ?>
                                <div id="project_id_<?=$project[0]['id']?>" class="project_item awards list fadein fast">
                                    <a href="projects.php?id=<?=$project[0]['id']?>"><h2><?php echo $project[0]['name']; ?>
                                        <span class="status">(<?=($project[0]['date_completed'] !== null)? $project[0]['date_completed'] : $project[0]['status']?>)</span><br>
                                        <span class="role"><?=$project[0]['role']; ?></span>
                                    </h2></a>
                                    <?php foreach($project as $award) : ?>
                                        <div class="award laurel float" <?=($award['laurel_image'] != null)? 'style="background: none; margin-top: 1em;"' : ''?>>
                                            <img src="<?=($award['laurel_image'] != null)? $award['laurel_image'] : 'css/images/1px_spacer.gif'?>" height="<?=($award['laurel_image'] != null)? '80' : '1'?>" />
                                            <h4 class="provider"><?=$award['provider']?></h4>
                                            <p class="awardname"><?=$award['award']?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
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
