<?php
include("inc.php");
if (!is_user_logged_in())
{
echo 'loadstatus=0';
exit; //only logged in users can upload
}

if ($_GET['room']) $room=$_GET['room'];
if ($_POST['room']) $room=$_POST['room'];
$filename=$_FILES['vw_file']['name'];

include_once('incsan.php');
sanV($room);
if (!$room) exit;
sanV($filename); //sanitise
if (!$filename) exit;

//suppress uploads to other folders or containing .php
if (strstr(strtolower($filename),'.php')) exit;
if (strstr($room,'/') || strstr($room,'..') ) exit;
if (strstr($filename,'/') || strstr($filename,'..') ) exit;
if ($room[0]=='.' || $filename[0]=='.') exit;

$destination='uploads/'.$room.'/';
if ($_GET['slides']) $destination .= 'slides/';

$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$allowed = array('zip','rar','jpg','jpeg','png','gif','txt','doc','docx','pdf','mp4', 'flv', 'avi', 'mpg', 'ppt','pptx', 'pps', 'ppsx', 'doc', 'docx', 'odt', 'odf', 'rtf', 'xls', 'xlsx');

if (in_array($ext,$allowed)) move_uploaded_file($_FILES['vw_file']['tmp_name'], $destination . $filename);
?>loadstatus=1
