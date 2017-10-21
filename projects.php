<!DOCTYPE html>

<?php
// ini_set('display_errors', 1);

require ('includes/configure.php');
require ('includes/function.resize.php');
require ('includes/function.truncate.php');

$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DEFAULT . ';charset=utf8', DB_USERNAME, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$projects_query = "SELECT id, is_subproject, name, subtitle, description, YEAR(date_completed) AS date_completed, employer, status, role, images_folder, video_link, social_links, sort FROM projects ";

if(isset($_GET['id'])) {
    $projects_query .= "WHERE id = :id ";
}

$projects_query .= "ORDER BY is_subproject ASC";

$stmt = $db->prepare($projects_query);

if(isset($_GET['id'])) {
    $stmt->bindParam('id', $_GET['id']);
}

$stmt->execute();

$projects = array();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $project) :

    if(!$project['is_subproject']) {

        $projects[$project['id']] = $project;
        $projects[$project['id']]['subprojects'] = array();
        $stmt3 = $db->prepare("SELECT * FROM awards_to_projects WHERE projects_id = :id
                                UNION SELECT * FROM awards_to_projects WHERE projects_id IN
                                    (SELECT subprojects_id FROM subprojects_to_projects WHERE projects_id = :id2)
                                ORDER BY award DESC");

        $stmt3->bindParam('id', $project['id']);
        $stmt3->bindParam('id2', $project['id']);
        $stmt3->execute();
        $projects[$project['id']]['awards'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $project['images_folder'];

        $projects[$project['id']]['images'] = getDirectoryTree($dir,'(jpg|jpeg|png|gif)');

    } else {

        $stmt2 = $db->prepare("SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = :id LIMIT 1");
        $stmt2->bindParam('id', $project['id']);
        $stmt2->execute();
        $parent = $stmt2->fetch(PDO::FETCH_ASSOC);

        if(!isset($projects[$parent[0]])) {

        } else {
            $projects[$parent[0]]['subprojects'][] = $row;
        }
    }

endforeach;

usort($projects, "project_sort");

// Define the custom sort function
function project_sort($a,$b) {
    if ($a['sort'] == $b['sort']) {
        return 0;
    }
    return ($a['sort'] < $b['sort']) ? -1 : 1;
}

function getDirectoryTree($outerDir, $x) {

    $dirs = array_diff(scandir($outerDir), array('.', '..'));
    $dir_array = array();
    foreach($dirs as $d) {

        if(is_dir($outerDir . DIRECTORY_SEPARATOR . $d)) {
            $dir_array[] = getDirectoryTree($outerDir . '/' . $d , $x);
        } else {
            if ($x ? preg_match('/' . $x .'$/i', $d) : 1) {
                $outerDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', $outerDir);
                $dir_array[] = $outerDir . '/' . $d;
            }
        }
    }

    $return = array();
    array_walk_recursive($dir_array, function($a) use (&$return) { $return[] = $a; });

    return $return;
}

if (isset($_GET['id'])) {
    foreach ($projects as $project) {
        $url = '/p/' . GenerateUrl($project['name']) . '/' . GenerateUrl($project['role']) . '/' . $project['id'];
        // CheckUrl($url);
    }
}
?>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning documentary filmmaker and editor in Bozeman, specializing in Editing and Story Consulting & Development">
        <meta name="keywords" content="editor, documentary editor, film editor, video editor, bozeman, montana, pbs editor, independent lens editor">
<?php
if (isset($_GET['id'])): ?>
        <title><?php
    foreach ($projects as $project) {
        echo $project['name'] . " | " . $project['role'];
    } ?></title>
<?php
else: ?>
        <title>Freelance Video Editor | Documentary Film :: My Work</title>
<?php
endif; ?>
        <link href="/css/global.css" rel="stylesheet" type="text/css" />
        <link href="/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="/css/projects.css" rel="stylesheet" type="text/css" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.0.0.js"></script>
        <script src="//malsup.github.io/jquery.cycle2.js"></script>
        <script src="/js/projects.js"></script>
        <script src="/js/threedots.js"></script>
<?php
if (isset($_GET['id'])): ?>
        <script type="text/javascript">
            var t = setTimeout('loadData($(".project_item .image"), false)', 1000);
        </script>
<?php
endif; ?>
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <?php include ('menu.php'); ?>
            </div>

            <div id="content">

                <div id="backbtn" style="visibility:hidden;"><a href="/projects.php" title="Back to Projects">&nbsp;</a></div>

                    <?php

    $img_settings = array('w' => 128, 'h' => 128, 'crop' => true);
    
    foreach ($projects as $project) {

?>
                <section>
                    <div id="project_id_<?php echo $project['id'] ?>" class="project_item fadein fast">

<?php

        $url = '/p/' . GenerateUrl($project['name']) . DIRECTORY_SEPARATOR . GenerateUrl($project['role']) . DIRECTORY_SEPARATOR . $project['id'];

        $img_src = '/css/images/1px_spacer.gif';

        if (isset($_GET['id']) && file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $project['id'] . '.jpg')) $img_src = resize(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $project['id'] . '.jpg', $img_settings);

        elseif (file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main.jpg')) $img_src = resize(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main.jpg', $img_settings);

?>
                        <a href="<?php echo $url?>"><img class="image" src="<?php echo $img_src?> " /></a>
<?php

    if ($project['social_links']): ?>

                        <div id="social_links">
                <?php
        $links = explode('\n', $project['social_links']);
        foreach ($links as $link): ?>
                            <a href="//<?php echo $link?>" target="_blank">

                <?php
            switch (substr($link, 0, 10)) {
                case 'www.facebo':
                    echo '<img src="/images/f_logo_36px.png" width="36" height="36" alt="Visit Facebook Page" />';
                    break;

                default:
                    break;
            } ?>
                            </a>
                <?php
        endforeach; ?>
                        </div>
                <?php
    endif; ?>

                        <a href="<?php echo $url ?>">
                            <h2><?php echo $project['name']; ?>
                                <span class="status">(<?php echo ($project['date_completed'] !== null) ? $project['date_completed'] : $project['status'] ?>)</span>
                                <span class="<?php echo $project['subtitle'] ? 'subtitle' : ''; ?>"><?php echo $project['subtitle']; ?></span>
                                <br><span class="role"><?php echo $project['role']; ?></span>
                            </h2>
                        </a>
                        <span class="employer"><?php echo ($project['employer'] !== null) ?
                                substr($project['employer'], 0, 4) == 'Inde' ? $project['employer'] :
                                'For ' . $project['employer'] :
                                '' ?></span>

                <?php

    if ($project['awards']): ?>
    
                        <div class="cycle-slideshow awards synopsis"
                                data-cycle-fx="fade"
                                data-cycle-speed="500">
                        <?php

        //echo print_r($awards);
        foreach ($project['awards'] as $award): ?>
                                <div class="award laurel" <?php echo ($award['laurel_image'] != null) ? 'style="background: none; margin-top: 1em;"' : '' ?>>
                                    <img src="<?php echo ($award['laurel_image'] != null) ? ROOT . DIRECTORY_SEPARATOR . $award['laurel_image'] : '/css/images/1px_spacer.gif' ?>" height="<?php echo ($award['laurel_image'] != null) ? '80' : '1' ?>" />
                                    <h4 class="provider"><?php echo $award['provider'] ?></h4>
                                    <p class="awardname"><?php echo $award['award'] ?></p>
                                </div>
                        <?php
        endforeach; ?>

                        </div>
                <?php
    endif; ?>
                        <div id="project_id_<?php echo $project['id'] ?>_data" class="data" style="display: none; margin-top: 1em;"> </div>
                        <?php
    if (isset($project['subprojects'])) {
        echo '<br/>';
        $settings = array('w' => 50, 'h' => 50, 'crop' => true);
        foreach ($project['subprojects'] as $subproject) {
            $url = "/p/" . GenerateUrl($subproject['name']) . DIRECTORY_SEPARATOR . GenerateUrl($subproject['role']) . DIRECTORY_SEPARATOR . $subproject['id'];
            $src = (file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $subproject['images_folder'] . '/main_' . $subproject['id'] . '.jpg')) ? resize(ROOT . DIRECTORY_SEPARATOR . $subproject['images_folder'] . '/main_' . $subproject['id'] . '.jpg', $settings) : '/css/images/1px_spacer.gif';
?>
                                        <div class="subproject_item" id="project_id_<?php echo $subproject['id']; ?>" class="project_item fadein fast"><a class="subproject" href="<?php echo $url ?>" alt="<?php echo $subproject['name'] . ' (' . $subproject['date_completed'] ?>)" title="<?php echo $subproject['name'] . ' (' . $subproject['date_completed'] ?>)"><img src="<?php echo $src ?>" border="0" /></a></div>
                                    <?php
        }
    }
?>
                        <div class="project_desc">
                            <p><?php echo truncate($project['description'], 100) ?></p>
                        </div>

                        <br class="clearfloat" />
                    </div>
                </section>
                    <?php
}
?>
            </div>
            <div id="footer">
                <footer>
                    <?php
include ('footer.php'); ?>
                </footer>
            </div>
        </div>
    </body>
</html>
