<!DOCTYPE html>

<?php
    require('includes/configure.php');
    require('includes/function.resize.php');
    require('includes/function.truncate.php');

    mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
    mysql_select_db(DB_DEFAULT);
    $where = '';
    if(isset($_GET['status'])) {
        switch($_GET['status']) {
            case 'completed' :
                $where = ' WHERE date_completed IS NOT NULL ';
                break;
            case 'current' :
                $where = ' WHERE date_completed IS NULL ';
                break;
            default :
                break;
        }
    }
    if(isset($_GET['id'])) $where = ' WHERE id = "' . $_GET['id'] . '"';

    $result = mysql_query("SELECT id, is_subproject, name, description, YEAR(date_completed) AS date_completed, employer, status, role, images_folder, video_link, social_links, sort FROM projects $where ORDER BY is_subproject ASC");
    $projects = array();
    while($row = mysql_fetch_assoc($result)) {
        if(!$row['is_subproject'] || isset($_GET['id'])) {
            $projects[$row['id']] = $row;
            $projects[$row['id']]['subprojects'] = array();
        } else {
            $p_result = mysql_query("SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = " . $row['id'] . " LIMIT 1");
            $parent = mysql_fetch_row($p_result);
            if(!isset($projects[$parent[0]])) {

            } else {
                $projects[$parent[0]]['subprojects'][] = $row;
            }
        }
    }
    usort($projects, "project_sort");

// Define the custom sort function
function project_sort($a,$b) {
    if ($a['sort'] == $b['sort']) {
        return 0;
    }
    return ($a['sort'] < $b['sort']) ? -1 : 1;
}

//    echo '<pre>';
//    echo mysql_error();
//    echo print_r($projects);
//    echo '</pre>';
    if(isset($_GET['id'])) {
        foreach($projects as $project) {
            $url = '/p/' . GenerateUrl($project['name']) . DIRECTORY_SEPARATOR . GenerateUrl($project['role']) . DIRECTORY_SEPARATOR . $project['id'];
            CheckUrl($url);
        }
    }
?>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning documentary filmmaker and editor in Bozeman, specializing in Editing and Story Consulting & Development">
        <meta name="keywords" content="editor, documentary editor, film editor, video editor, bozeman, montana, pbs editor, independent lens editor">
<?php if(isset($_GET['id'])) : ?>
        <title><?php foreach($projects as $project) { echo $project['name'] . " | " . $project['role']; }?></title>
<?php else : ?>
        <title>Freelance Video Editor | Documentary Film :: My Work</title>
<?php endif; ?>
        <link href="/css/global.css" rel="stylesheet" type="text/css" />
        <link href="/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="/css/projects.css" rel="stylesheet" type="text/css" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.0.0.js"></script>
        <script src="http://malsup.github.io/jquery.cycle.all.js"></script>
        <!--<script src="javascript/content.js" type="text/javascript"></script>-->
        <script src="/javascript/menu.js" type="text/javascript"></script>
        <script src="/javascript/threedots.js" type="text/javascript"></script>
<?php if(isset($_GET['id'])) : ?>
        <script type="text/javascript">
            var t = setTimeout('loadData($(".project_item .image"), false)', 1000);
        </script>
<?php endif; ?>
        <script type="text/javascript">
            var bkImages = new Array();
            var index = 0;
<?php
   /* $i=0;
    foreach($projects as $project) :
        $img_settings = array('w'=>500,'h'=>500,'crop'=>true);

        if(isset($_GET['id']) && file_exists($project['images_folder'] . '/main_' . $project['id'] . '.jpg')) $img_src = resize($project['images_folder'] . '/main_' . $project['id'] . '.jpg', $img_settings);
        elseif(file_exists($project['images_folder'] . '/main.jpg')) $img_src = resize($project['images_folder'] . '/main.jpg', $img_settings);
        $i++; */?>
            //bkImages[//$i?>] = '//$img_src?>';
<?php //endforeach; ?>
        </script>
        <!--<script src="javascript/background_cycle.js" type="text/javascript"></script>-->
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <?php include('menu.php'); ?>
            </div>
            <div id="content">
                <div id="bkImage">
                    <img src="/css/images/1px_spacer.gif" />
                </div>
                <div id="backbtn" style="visibility:hidden;"><a href="/projects.php" title="Back to Projects">&nbsp;</a></div>
                    <?php
                        $img_settings = array('w'=>128,'h'=>128,'crop'=>true);
                        foreach($projects as $project) {
                    ?>
                <section>
                    <div id="project_id_<?=$project['id']?>" class="project_item fadein fast">
                        <?php
                            $url = '/p/' . GenerateUrl($project['name']) . DIRECTORY_SEPARATOR . GenerateUrl($project['role']) . DIRECTORY_SEPARATOR . $project['id'];
                            $img_src = '/css/images/1px_spacer.gif';
                            if(isset($_GET['id']) && file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $project['id'] . '.jpg')) $img_src = resize(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $project['id'] . '.jpg',$img_settings);
                            elseif(file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main.jpg')) $img_src = resize(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main.jpg', $img_settings);

                            $awards_result = mysql_query("SELECT * FROM awards_to_projects WHERE projects_id = {$project['id']}
                                                            UNION SELECT * FROM awards_to_projects WHERE projects_id IN (SELECT subprojects_id FROM subprojects_to_projects WHERE projects_id = {$project['id']}) ORDER BY award DESC");
                            if($awards_result) {
                                while ($row = mysql_fetch_assoc($awards_result)) {
                                    $awards[$project['id']][] = $row;
                                }
                            } else {
                                    $awards[$project['id']] = FALSE;
                            }

                        ?>
                        <a href="<?=$url?>"><img class="image" src="<?=$img_src?> " /></a>
                <?php if($project['social_links']) : ?>
                        <div id="social_links">
                <?php
                        $links = explode('\n', $project['social_links']);
                        foreach($links as $link) : ?>
                            <a href="//<?=$link?>" target="_blank">

                <?php       switch(substr($link, 0, 10)) {
                                case 'www.facebo' :
                                    echo '<img src="/images/f_logo_36px.png" width="36" height="36" alt="Visit Facebook Page" />';
                                    break;
                                default :
                                    break;
                            } ?>
                            </a>
                <?php endforeach; ?>
                        </div>
                <?php endif; ?>

                        <a href="<?=$url?>"><h2><?php echo $project['name']; ?>
                            <span class="status">(<?=($project['date_completed'] !== null)? $project['date_completed'] : $project['status']?>)</span><br>
                            <span class="role"><?=$project['role']; ?></span>
                        </h2></a>
                        <span class="employer"><?=($project['employer'] !== null)? 'For ' . $project['employer'] : ''?></span>

                <?php if($awards[$project['id']]) : ?>
                        <div class="awards synopsis">
                        <?php
                            //echo print_r($awards);
                        foreach($awards[$project['id']] as $award) : ?>
                                <div class="award laurel" <?=($award['laurel_image'] != null)? 'style="background: none; margin-top: 1em;"' : ''?>>
                                    <img src="<?=($award['laurel_image'] != null)? ROOT . DIRECTORY_SEPARATOR . $award['laurel_image'] : '/css/images/1px_spacer.gif'?>" height="<?=($award['laurel_image'] != null)? '80' : '1'?>" />
                                <h4 class="provider"><?=$award['provider']?></h4>
                                <p class="awardname"><?=$award['award']?></p>
                            </div>
                        <?php endforeach; ?>

                        </div>
                        <script type="text/javascript">
                            $('.awards').cycle({
                                        fx:     'fade',
                                        speed:  500
                                    });

                        </script>

                <?php endif; ?>
                        <div id="project_id_<?=$project['id']?>_data" class="data" style="display: none; margin-top: 1em;"> </div>
                        <?php
                            if(isset($project['subprojects'])) {
                                echo '<br/>';
                                $settings = array('w'=>50,'h'=>50,'crop'=>true);
                                foreach($project['subprojects'] as $subproject){
                                    $url = "/p/" . GenerateUrl($subproject['name']). DIRECTORY_SEPARATOR . GenerateUrl($subproject['role']) .  DIRECTORY_SEPARATOR . $subproject['id'];
                                    $src = (file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $subproject['images_folder'] . '/main_' . $subproject['id'] . '.jpg'))? resize(ROOT . DIRECTORY_SEPARATOR . $subproject['images_folder'] . '/main_' . $subproject['id'] . '.jpg',$settings) : '/css/images/1px_spacer.gif' ;
                                    ?>
                                        <div class="subproject_item" id="project_id_<?=$subproject['id']; ?>" class="project_item fadein fast"><a class="subproject" href="<?=$url?>" alt="<?=$subproject['name'] . ' (' . $subproject['date_completed']?>)" title="<?=$subproject['name'] . ' (' . $subproject['date_completed']?>)"><img src="<?=$src?>" border="0" /></a></div>
                                    <?php
                                }
                            }
                        ?>
                                        <div class="project_desc">
                                            <p><?=truncate($project['description'], 100)?></p>
                                        </div>

                        <br class="clearfloat" />
                    </div>
                </section>
                    <?php
                        }
                    ?>
                    <script src="/javascript/projects.js" type="text/javascript"></script>

            </div>
            <div id="footer">
                <footer>
                    <?php include('footer.php'); ?>
                </footer>
            </div>
        </div>
    </body>
</html>
