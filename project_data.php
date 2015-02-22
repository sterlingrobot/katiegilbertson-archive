    <?php
    require_once('/includes/configure.php');
    require_once('function.resize.php');
    $project_id = $_GET['id'];
    mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
    mysql_select_db(DB_DEFAULT);
    $result = mysql_query("SELECT p.id, p.is_subproject, p.name, p.description, YEAR(p.date_completed) AS date_completed, p.employer, p.status, p.role, p.images_folder, p.video_link, p.video_pswd, s2p.subprojects_id FROM projects p
        LEFT JOIN subprojects_to_projects s2p ON s2p.projects_id = p.id
        WHERE p.id = $project_id LIMIT 1");
    $project = mysql_fetch_assoc($result);
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

    $awards_result = mysql_query("SELECT * FROM awards_to_projects WHERE projects_id = $project_id ORDER BY award DESC");
    if($awards_result) {
        while ($row = mysql_fetch_assoc($awards_result)) {
            $awards[] = $row;
        }
    } else {
            $awards = FALSE;
    }
?>
    <script type='text/javascript'> bkImages = new Array();</script>
<?php
    /*$bkImgsettings = array('w'=>500,'h'=>500,'crop'=>true);
    $imgIdx = 0;
    foreach (scandir($project['images_folder']) as $entry) {
        if (!is_dir($entry)) {
            if (in_array(mime_content_type($project['images_folder'] . '/' . $entry), $image_types)) {
                ?>
                    <script type='text/javascript'> bkImages[<?=$imgIdx?>] = '<?=resize($project['images_folder'] . '/' . $entry,$bkImgsettings)?>'</script>
                <?php
            } else {
                foreach (scandir($project['images_folder'] . '/'. $entry) as $img) {
                    if (in_array(mime_content_type($project['images_folder'] . '/' . $entry . '/' . $img), $image_types)) {
                        ?>
                            <script type='text/javascript'> bkImages[<?=$imgIdx?>] = '<?=resize($project['images_folder'] . '/' . $entry . '/' . $img ,$bkImgsettings)?>'</script>
                        <?php
                    }
                }
            }
        }
        $imgIdx++;
    }*/

    if($awards) : ?>
<div class="awards">
<?php
    //echo print_r($awards);
          foreach($awards as $award) : ?>
    <div class="award laurel" <?=($award['laurel_image'] != null)? 'style="background: none; margin-top: 1em;"' : ''?>>
        <img src="<?=($award['laurel_image'] != null)? ROOT . DIRECTORY_SEPARATOR . $award['laurel_image'] : ROOT . '/css/images/1px_spacer.gif'?>" height="<?=($award['laurel_image'] != null)? '80' : '1'?>" />
        <h4 class="provider"><?=$award['provider']?></h4>
        <p class="awardname"><?=$award['award']?></p>
    </div>
<?php endforeach; ?>

</div>
<?php endif; ?>

<?php    if(isset($project['video_link'])) :
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
    <div class="project_slideshow">
<?php

        $settings = array('w'=>490,'h'=>280,'crop'=>true);
        $imgIdx = 0;
        foreach (scandir(ROOT . DIRECTORY_SEPARATOR . $project['images_folder']) as $entry) {
            if (!is_dir($entry)) {
                if (in_array(mime_content_type(ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/' . $entry), $image_types)) {
                    ?>
                        <img src="<?=resize(ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/' . $entry,$settings)?>" border="0" />
                        <!--<script type='text/javascript'> bkImages[$imgIdx?>] = 'resize($project['images_folder'] . '/' . $entry,array('w'=>500,'h'=>500,'crop'=>true))?>'</script>-->
                    <?php
                } else {
                    foreach (scandir(ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/'. $entry) as $img) {
                        if (in_array(mime_content_type(ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/' . $entry . '/' . $img), $image_types)) {
                            ?>
                                <img src="<?=resize(ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/' . $entry . '/' . $img ,$settings)?>" border="0" />
                                <!--<script type='text/javascript'> bkImages[$imgIdx?>] = 'resize($project['images_folder'] . '/' . $entry . '/' . $img ,array('w'=>500,'h'=>500,'crop'=>true))?>'</script>-->
                            <?php
                        }
                    }
                }
            }
            $imgIdx++;
        }
?>
    </div>
    <script type="text/javascript">
        $('.project_slideshow').cycle({
			fx:     'fade',
			speed:  1500,
			timeout: 4000,
			pause:  1
		});

    </script>

<?php
    endif;
    /******HACK for TRUST to load Alaska first from main project********/
    $id = ($subprojects_id > 0) ? $subprojects_id : $_GET['id'];
    if($subprojects_id > 0) {
        $result = mysql_query("SELECT id, is_subproject, name, description, YEAR(date_completed) AS date_completed, employer, status, role, images_folder, video_link FROM projects WHERE id = $subprojects_id LIMIT 1");
        $project = mysql_fetch_assoc($result);
        $main_result = mysql_query('SELECT description FROM projects WHERE id = (SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = ' . $_GET['id'] . ')');
        $main_project = mysql_fetch_assoc($main_result);
        $main_desc = $main_project['description'];
    }
    /******END HACK***********************************/
    if($project['is_subproject']) {
        $main_result = mysql_query('SELECT id,description FROM projects WHERE id = (SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = ' . $id . ')');
        list($main_id,$main_desc) = mysql_fetch_row($main_result);
        $sibling_project_query = 'SELECT p.id,p.name,p.role,YEAR(p.date_completed) as date_completed,p.images_folder FROM subprojects_to_projects sp LEFT JOIN projects p ON p.id = sp.subprojects_id WHERE sp.projects_id = '. $main_id;
        $sibling_projects_result = mysql_query($sibling_project_query);
        $sibling_projects = array();
        while ($row1 = mysql_fetch_array($sibling_projects_result)) {
            $sibling_projects[] = $row1;
        }
        ?>
            <div style="width: 60%; margin: 1em auto;">
                <h3><?=$project['name']?><span class="status">(<?=($project['date_completed'] !== null)? $project['date_completed'] : $project['status']?>)</span><br>
                <span class="role"><?=$project['role']; ?></span></h3>
                <p class="subproject_desc"><?=$project['description']?></p>
            </div>
        <?php
    }
?>

    <div class="full_desc" style="display:none;">
        <p><?=$main_desc?></p>
    </div>
<?php
    if($project['is_subproject']) :
        $settings = array('w'=>50,'h'=>50,'crop'=>true);
        echo '<h4 class="press related">related work</h4>';
        echo '<div class="related-work">';
        foreach($sibling_projects as $sibling) :
            $url = "/p/" . GenerateUrl($sibling['name']). DIRECTORY_SEPARATOR . GenerateUrl($sibling['role']) .  DIRECTORY_SEPARATOR . $sibling['id'];
            $src = (file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $sibling['id'] . '.jpg'))? resize(ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $sibling['id'] . '.jpg',$settings) : '/css/images/1px_spacer.gif' ;
?>
            <div class="subproject_item" id="project_id_<?=$sibling['id']; ?>" class="project_item fadein fast"><a class="subproject" href="<?=$url?>" alt="<?=$sibling['name'] . ' (' . $sibling['date_completed']?>)" title="<?=$sibling['name'] . ' (' . $sibling['date_completed']?>)"><img src="<?=$src?>" border="0" /></a></div>
<?php
        endforeach;
        echo '</div>';
    endif;
        $resources_result = mysql_query('SELECT * FROM resources_to_projects WHERE projects_id = ' . $_GET['id']);
    if(@mysql_num_rows($resources_result) > 0) {
        $resources = array();
        while($row = mysql_fetch_assoc($resources_result)) {
            $resources[] = $row;
        }
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
    }

    function convertUrlQuery($query) {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }
?>
