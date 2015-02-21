<?php
$filename = $_POST['filename'];
$content = $_POST['content'];
$file = fopen($filename, 'w');
fwrite($file, stripslashes($content));
fclose($file);

header('Location: ' . $filename);