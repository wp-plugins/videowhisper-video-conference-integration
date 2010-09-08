<?php
if ($_GET["room"]) $room=$_GET["room"];
if ($_POST["room"]) $room=$_POST["room"];

$filename=$_FILES['vw_file']['name'];

//do not allow uploads to other folders
if ( strstr($room,"/") || strstr($room,"..") ) exit;
if ( strstr($filename,"/") || strstr($filename,"..") ) exit;

$ext=strtolower(substr($filename,-4));
$allowed=array(".swf",".zip",".rar",".jpg","jpeg",".png",".gif",".txt",".doc","docx",".htm","html",".pdf",".mp3",".flv",".avi",".mpg",".ppt",".pps");

if (in_array($ext,$allowed)) move_uploaded_file($_FILES['vw_file']['tmp_name'], "uploads/".$room."/".$filename);
?>loadstatus=1
