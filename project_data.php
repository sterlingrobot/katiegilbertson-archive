<?php
    // ini_set('display_errors', 1);

    require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/configure.php');
    require_once('function.resize.php');

    $project_id = $_GET['id'];

    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DEFAULT . ';charset=utf8', DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $db->prepare("SELECT p.id, p.is_subproject, p.name, p.description, YEAR(p.date_completed) AS date_completed, p.employer, p.status, p.role, p.images_folder, p.video_link, p.video_pswd, s2p.subprojects_id FROM projects p LEFT JOIN subprojects_to_projects s2p ON s2p.projects_id = p.id WHERE p.id = ? LIMIT 1");

    $stmt->execute(array($project_id));

    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    $main_desc = $project['description'];
    $subprojects_id = $project['subprojects_id'];

    $image_types = array(
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
    );

    /******HACK for TRUST to load Alaska first from main project********/
    if($project_id == 3) $project_id = 5;
    elseif($subprojects_id > 0) $project_id = $subprojects_id;  // Load first subproject if this is a parent placeholder

    $stmt2 = $db->prepare("SELECT * FROM awards_to_projects WHERE projects_id = ? ORDER BY award DESC");
    $stmt2->execute(array($project_id));

    $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>


<?php

    if(isset($project['video_link'])) :
            $video_link = $project['video_link'];
            $video_link .= (strpos($project['video_link'], "?") > -1) ? "&" : "?";
            $video_urlquery = parse_url($project['video_link'], PHP_URL_QUERY);
            $video_query = convertUrlQuery($video_urlquery);
            $video_h = isset($video_query['h']) ? $video_query['h'] : "281";
            $video_w = isset($video_query['w']) ? $video_query['w'] : "500";

    ?>

<div class="project_video">
    <img src="/css/images/top-lt-rnd.png" class="top left corner" />
    <img src="/css/images/top-rt-rnd.png" class="top right corner" />
    <img src="/css/images/btm-lt-rnd.png" class="bottom left corner" />
    <img src="/css/images/btm-rt-rnd.png" class="bottom right corner" />

    <iframe src="<?=$video_link?>title=0&portrait=0&byline=0"  width="<?=$video_w?>" height="<?=$video_h?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" seamless webkitAllowFullScreen mozallowfullscreen allowFullScreen seamless></iframe>
</div>
<?php   if(isset($project['video_pswd'])) :
    ?>
        <span style="margin-left: 12em;">Use password: <b><?=$project['video_pswd']?></b> to view.</span>
    <?php
         endif;
    else:
?>
    <div class="cycle-slideshow"
        data-cycle-fx="fade"
        data-cycle-speed="1500"
        data-cycle-timeout="2000"
        data-cycle-pause="1">
<?php

        $settings = array('w'=>490,'h'=>280,'crop'=>true);
        $dir = scandir($project['images_folder']);

        foreach ($dir as $entry) {

            $src = '/' . $project['images_folder'] . '/' . $entry;

            if (!is_dir($entry)) {

                if (in_array(finfo_file(finfo_open(FILEINFO_MIME_TYPE), $project['images_folder'] . '/' . $entry), $image_types)) {
                        echo '<img src="' . $src . '" border="0"  height="300" />';
                } else {
                    $dir2 = scandir($project['images_folder'] . '/'. $entry);
                    foreach ($dir2 as $img) {
                        $src2 = '/' . $project['images_folder'] . '/' . $entry . '/' . $img;
                        if (in_array(mime_content_type($project['images_folder'] . '/' . $entry . '/' . $img), $image_types)) {
                            echo '<img src="' . $src2 . '" border="0" height="300" />';
                        }
                    }
                }
            }
        }
?>
    </div>

<?php

    endif;

    /******HACK for TRUST to load Alaska first from main project********/

    $id = ($subprojects_id > 0) ? $subprojects_id : $project_id;

    if($subprojects_id > 0) {

        $stmt3 = $db->prepare("SELECT id, is_subproject, name, description, YEAR(date_completed) AS date_completed, employer, status, role, images_folder, video_link FROM projects WHERE id = ? LIMIT 1");

        $stmt3->execute(array($id));

        $project = $stmt3->fetch(PDO::FETCH_ASSOC);

        $stmt4 = $db->prepare('SELECT description FROM projects WHERE id = (SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = ?)');

        $stmt4->execute(array($project_id));

        $main_desc = $stmt4->fetch(PDO::FETCH_ASSOC)['description'];

    }
    /******END HACK***********************************/


    if($project['is_subproject']) {

        $stmt5 = $db->prepare('SELECT id,description FROM projects WHERE id = (SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = ?)');

        $stmt5->execute(array($id));

        list($main_id,$main_desc) = $stmt5->fetch(PDO::FETCH_NUM);

        $stmt6 = $db->prepare('SELECT p.id,p.name,p.role,YEAR(p.date_completed) as date_completed,p.images_folder FROM subprojects_to_projects sp LEFT JOIN projects p ON p.id = sp.subprojects_id WHERE sp.projects_id = ?');

        $stmt6->execute(array($main_id));

        $sibling_projects = $stmt6->fetchAll(PDO::FETCH_ASSOC);

        ?>

            <div style="width: 60%; margin: 1em auto;">
                <h3><?=$project['name']?><span class="status">(<?=($project['date_completed'] !== null)? $project['date_completed'] : $project['status']?>)</span><br>
                <span class="role"><?=$project['role']; ?></span></h3>
                <p class="subproject_desc"><?=$project['description']?></p>
            </div>

        <?php
    }
?>

    <div class="full_desc" style="display:none">

        <p><?php echo $main_desc; ?></p>

    </div>

<?php

    if($project['is_subproject']) :

        $settings = array('w'=>50,'h'=>50,'crop'=>true);
        echo '<h4 class="press related">related work</h4>';
        echo '<div class="related-work">';

        foreach($sibling_projects as $sibling) :

            $url = "/p/" . GenerateUrl($sibling['name']). DIRECTORY_SEPARATOR . GenerateUrl($sibling['role']) .  DIRECTORY_SEPARATOR . $sibling['id'];

            $src = (file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $sibling['id'] . '.jpg')) ?
                resize( DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $sibling['id'] . '.jpg',$settings) :
                '/css/images/1px_spacer.gif' ;

?>
            <div class="subproject_item" id="project_id_<?=$sibling['id']; ?>" class="project_item fadein fast">
                <a class="subproject"
                    href="<?=$url?>"
                    alt="<?=$sibling['name'] . ' (' . $sibling['date_completed']?>)"
                    title="<?=$sibling['name'] . ' (' . $sibling['date_completed']?>)">
                    <img src="<?=$src?>" border="0" />
                </a>
            </div>

<?php
        endforeach;

        echo '</div>';

    endif;

        $stmt7 = $db->prepare('SELECT * FROM resources_to_projects WHERE projects_id = ?');

        $stmt7->execute(array($project_id));

        $resources = $stmt7->fetchAll(PDO::FETCH_ASSOC);

        $settings = array('w'=>70,'h'=>100,'crop'=>true);

 ?>

    <h4 class="press">press</h4>

<?php

        foreach ($resources as $resource) {

            $src = (file_exists(FS_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $resource['thumbnail']))? resize('http://www.katiegilbertson.com/assets' .  DIRECTORY_SEPARATOR . $resource['thumbnail'], $settings) : '/images/pdf-logo.png' ;
                ?>

                    <div class="subproject_item resource" id="project_id_<?=$resource['projects_id']?>" class="project_item fadein fast"><a class="subproject" href="<?=$resource['link']?>" alt="<?=$resource['name']?>" title="<?=$resource['name']?>" target="_blank"> <img src="<?=$src?>" border="0" /></a><br/><span><?=$resource['name']?></span></div>

            <?php
        }


    function convertUrlQuery($query) {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = isset($item[1]) ? $item[1] : '';
        }
        return $params;
    }
?>
