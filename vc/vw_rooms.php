<?php
$listed_rooms=array(); //keep track of duplicates

include("../../../../wp-config.php");

global $wpdb;

$table_name = $wpdb->prefix . "vw_vcsessions";
$table_name3 = $wpdb->prefix . "vw_vcrooms";


//clean recordings
$exptime=time()-30;
$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
$wpdb->query($sql);
$wpdb->flush();

$options = VWvideoConference::getAdminOptions();


$userRoom = $_COOKIE["userRoom"];
$userRoom = sanitize_file_name($userRoom);

//private room?
if ($userRoom)  $pr = $wpdb->get_row("SELECT * FROM $table_name3 where name='$userRoom'");
if ($pr)
{
	$ptype = ($pr->type==1?'':'1');
	$capacity = ($pr->capacity?$pr->capacity:$options['capacityDefault']);
}
else
{
	$ptype = 1;
	$capacity = $options['capacityDefault'];
}

?>
<rooms>
<?php

//current room
if ($userRoom)
{
	$listed_rooms[] = $userRoom;
	echo "<room room_name=\"".$userRoom."\" room_description=\"Welcome to ".$userRoom."!\" user_number=\"0\" capacity=\"$capacity\" private_room=\"$ptype\"/>";
}

//owned rooms (public and private)
$items =  $wpdb->get_results("SELECT * FROM `$table_name3` where status='1' and owner='" . $current_user->ID . "'");

if ($items) foreach ($items as $item)
if (!in_array($item->name, $listed_rooms))
		{
			$listed_rooms[] = $item->name;
			$item->name = sanitize_file_name($item->name);
			echo "<room room_name=\"".$item->name."\" room_description=\"Welcome to ".$item->name."!\" user_number=\"0\" capacity=\"" . ($item->capacity?$item->capacity:$options['capacityDefault']) . "\" private_room=\"" . ($item->type==1?'':'1') . "\"/>";
		}

	//default landing room
	if ($options['landingRoom']=='lobby')
		if (!in_array($options['lobbyRoom'], $listed_rooms))
		{
			echo "<room room_name=\"" . $options['lobbyRoom'] . "\" room_description=\"Welcome to ".$options['lobbyRoom']."!\" user_number=\"0\" capacity=\"" . $options['capacityDefault'] . "\" private_room=\"\"/>";
			$listed_rooms[] = $options['lobbyRoom'];
		}

	//rest of public room
	$items =  $wpdb->get_results("SELECT name FROM `$table_name3` where status='1' and type='1'");

if ($items) foreach ($items as $item)
		if (!in_array($item->name, $listed_rooms))
			echo "<room room_name=\"".$item->name."\" room_description=\"Welcome to ".$item->name."!\" user_number=\"0\" capacity=\"" . ($item->capacity?$item->capacity:$options['capacityDefault']) . "\" private_room=\"\"/>";
?>
</rooms>