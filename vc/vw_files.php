<?php
if ($_GET["room"]) $room=$_GET["room"];
if ($_POST["room"]) $room=$_POST["room"];
?>
<files>
<?php

//do not allow access to other folders
if ( strstr($room,"/") || strstr($room,"..") ) exit;

$dir="uploads";
$dir.="/$room";

if (!file_exists($dir)) mkdir($dir);
@chmod($dir, 0766);

$handle=opendir($dir);
while
(($file = readdir($handle))!==false)
{
if (($file != ".") && ($file != "..") && (!is_dir("$dir/".$file))) echo "<file file_name=\"".$file."\" file_size=\"".filesize("$dir/".$file)."\" />";
}
closedir($handle);
?>
</files>