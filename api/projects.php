<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/configure.php');

$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DEFAULT . ';charset=utf8', DB_USERNAME, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

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
if(isset($_GET['id'])) $where = ' WHERE id = :id';

$stmt = $db->prepare("SELECT id, is_subproject, name, description, YEAR(date_completed) AS date_completed, employer, status, role, images_folder, video_link, social_links, sort FROM projects $where ORDER BY is_subproject ASC");
$stmt->bindParam('id', $_GET['id']);
$stmt->execute();
$projects = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if(!$row['is_subproject'] || isset($_GET['id'])) {
        $projects[$row['id']] = $row;
        $projects[$row['id']]['subprojects'] = array();
        $stmt3 = $db->prepare("SELECT * FROM awards_to_projects WHERE projects_id = :id
                                UNION SELECT * FROM awards_to_projects WHERE projects_id IN
                                    (SELECT subprojects_id FROM subprojects_to_projects WHERE projects_id = :id2)
                                ORDER BY award DESC");
        $stmt3->bindParam('id', $row['id']);
        $stmt3->bindParam('id2', $row['id']);
        $stmt3->execute();
        $projects[$row['id']]['awards'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt2 = $db->prepare("SELECT projects_id FROM subprojects_to_projects WHERE subprojects_id = :id LIMIT 1");
        $stmt2->bindParam('id', $row['id']);
        $stmt2->execute();
        $parent = $stmt2->fetch(PDO::FETCH_ASSOC);
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

echo json_encode($projects);