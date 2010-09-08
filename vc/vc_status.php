<?php
/*
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
*/

$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];
$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];
$message=$_POST[m];
$cam=$_POST[cam];
$mic=$_POST[mic];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];

include("../../../../wp-config.php");
include("inc.php");

global $wpdb;
$table_name = $wpdb->prefix . "vw_vcsessions";
$wpdb->flush();

	$s=$_POST['s'];
	$u=$_POST['u'];
	$r=$_POST['r'];

	$ztime=time();

	$sql = "SELECT * FROM $table_name where session='$s' and status='1'";
	$session = $wpdb->get_row($sql);
	if (!$session)
	{
	$sql="INSERT INTO `$table_name` ( `session`, `username`, `room`, `message`, `sdate`, `edate`, `status`, `type`) VALUES ('$s', '$u', '$r', '$m', $ztime, $ztime, 1, 1)";
    $wpdb->query($sql);
	}
	else
	{
	$sql="UPDATE `$table_name` set edate=$ztime, room='$r', username='$u', message='$m' where session='$s' and status='1'";
    $wpdb->query($sql);
	}

	$exptime=$ztime-30;
	$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
  $wpdb->query($sql);

  
$maximumSessionTime=0; //900000ms=15 minutes

$disconnect=""; //anything else than "" will disconnect with that message
?>timeTotal=<?=$maximumSessionTime?>&timeUsed=<?=$currentTime?>&lastTime=<?=$currentTime?>&disconnect=<?=$disconnect?>&loadstatus=1