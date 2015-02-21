<?php
ini_set('display_errors', 1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/function.resize.php');
$settings = array('w'=>40,'h'=>40,'crop'=>true);
?>

<img src="<?php echo resize('../images/profile-contact.jpg', $settings); ?>" />

<?php phpinfo(); ?>