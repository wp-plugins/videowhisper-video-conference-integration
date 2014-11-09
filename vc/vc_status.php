<?php
/*
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
*/


$s=$_POST['s'];
$u=$_POST['u'];
$r=$_POST['r'];
$m=$_POST['m'];
$cam=$_POST[cam];
$mic=$_POST[mic];
$currentTime=$_POST[ct];
$lastTime=$_POST[lt];

include("inc.php");

	//sanitize variables
	include("incsan.php");
	sanV($s);
	sanV($u);
	sanV($r);
	sanV($m, 0, 0);

	//exit if no valid session name or room name
	if (!$s) exit;
	if (!$r) exit;

global $wpdb;
$table_name = $wpdb->prefix . "vw_vcsessions";
$wpdb->flush();

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

	//do not clean more often than 25s (mysql table invalidate)
	$lastClean = 0; $cleanNow = false;
	$lastCleanFile = $options['uploadsPath'] . 'lastclean.txt';

	if (file_exists($lastCleanFile)) $lastClean = file_get_contents($lastCleanFile);
	if (!$lastClean) $cleanNow = true;
	else if ($ztime - $lastClean > 25) $cleanNow = true;

	if ($cleanNow)
	{
	if (!$options['onlineExpiration']) $options['onlineExpiration'] = 310;
	$exptime=$ztime-$options['onlineExpiration'];
	$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
	$wpdb->query($sql);
	file_put_contents($lastCleanFile, $ztime);
	}




$maximumSessionTime=0; //900000ms=15 minutes

$disconnect=""; //anything else than "" will disconnect with that message
?>timeTotal=<?=$maximumSessionTime?>&timeUsed=<?=$currentTime?>&lastTime=<?=$currentTime?>&disconnect=<?=$disconnect?>&loadstatus=1