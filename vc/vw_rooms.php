<rooms>
<room room_name="Lobby" room_description="Welcome!" user_number="0" capacity="100" />
<room room_name="Fun" room_description="Haha!" user_number="0" capacity="50" />
<room room_name="Hangout" room_description="Chill..." user_number="0" capacity="50" />
<?php
	$public_rooms=array("Lobby", "Fun", "Hangout");

		include("../../../../wp-config.php");

		global $wpdb;
		$table_name = $wpdb->prefix . "vw_vcsessions";
		
		$root_url = get_bloginfo( "url" ) . "/";
		
		$page_id = get_option("vw_vc_page");
		if ($page_id > 0) $permalink = get_permalink( $page_id );		
		else $permalink = $root_url . "wp-content/plugins/videowhisper-video-conference-integration/vc/";
			 
		//clean recordings
		$exptime=time()-30;
		$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
		$wpdb->query($sql);
			
		$wpdb->flush();
		
		$items =  $wpdb->get_results("SELECT room, count(*) as users FROM `$table_name` where status='1' and type='1' GROUP BY room");

		if ($items)	foreach ($items as $item) if (!in_array($item->room, $public_rooms)) echo "<room room_name=\"".$item->room."\" room_description=\"Welcome to ".$item->room."!\" user_number=\"" . $item->users ."\" capacity=\"100\" private_room=\"1\"/>";
?>
</rooms>