<?php

include("../../../../wp-config.php");

$options = get_option('VWvideoConferenceOptions');
$rtmp_server = $options['rtmp_server'];
$rtmp_amf = $options['rtmp_amf'];
$userName =  $options['userName']; if (!$userName) $userName='user_nicename';
$canAccess = $options['canAccess'];
$accessList = $options['accessList'];

$serverRTMFP = $options['serverRTMFP'];
$p2pGroup = $options['p2pGroup'];
$supportRTMP = $options['supportRTMP'];
$supportP2P = $options['supportP2P'];
$alwaystRTMP = $options['alwaystRTMP'];
$alwaystP2P = $options['alwaystP2P'];
$disableBandwidthDetection = $options['disableBandwidthDetection'];

$camRes = explode('x',$options['camResolution']);





global $current_user;
get_currentuserinfo();

//username
if ($current_user->$userName) $username=urlencode($current_user->$userName);
$username=preg_replace("/[^0-9a-zA-Z]/","-",$username);

            //access keys
            $userkeys = $current_user->roles;
            $userkeys[] = $current_user->user_login;
            $userkeys[] = $current_user->ID;
            $userkeys[] = $current_user->user_email;
            $userkeys[] = $current_user->display_name;
            
$loggedin=0;
$msg="";

//access permissions
       function inList($keys, $data)
        {
            if (!$keys) return 0;

            $list=explode(",", strtolower(trim($data)));

            foreach ($keys as $key)
                foreach ($list as $listing)
                    if ( strtolower(trim($key)) == trim($listing) ) return 1;

                    return 0;
        }
        
        
switch ($canAccess)
{	
	case "all":
	$loggedin=1;
	if (!$username) 
	{
		$username="Guest".base_convert((time()-1224350000).rand(0,10),10,36);
		$visitor=1; //ask for username
	}
	break;
	case "members":
		if ($username) $loggedin=1;
		else $msg="<a href=\"/\">Please login first or register an account if you don't have one! Click here to return to website.</a>";
	break;
	case "list";
		if ($username)
			if (inList($userkeys, $accessList)) $loggedin=1;
			else $msg="<a href=\"/\">$username, you are not in the video conference access list.</a>";
		else $msg="<a href=\"/\">Please login first or register an account if you don't have one! Click here to return to website.</a>";
	break;
}

//configure a picture to show when this user is clicked
$userPicture=urlencode("defaultpicture.png");
$userLink=urlencode("http://www.videowhisper.com/");

//replace bad words or expression
$filterRegex=urlencode("(?i)(fuck|cunt)(?-i)");
$filterReplace=urlencode(" ** ");

//room access
if ($_GET['room_name']) $room = $_GET['room_name'];
$room = sanitize_file_name($room);
if ($room) setcookie('userRoom',$room);


if (!$room && !$visitor) 
{
	
	
	if ($options['landingRoom']=='username') 
	//can create	
	{
		
	$room=$username;
	$admin=1;
		
	}
	
	else $room = $options['lobbyRoom']; //or go to default

}
 else if (!$room) $room = $options['lobbyRoom'];  //visitor can't create room

if (!$options['anyRoom']) //room must exist
if ($room != $options['lobbyRoom'] || $options['landingRoom'] !='lobby') //not lobby
{
			global $wpdb;           
			$table_name = $wpdb->prefix . "vw_vcsessions";
			$table_name3 = $wpdb->prefix . "vw_vcrooms";
 
			$wpdb->flush();
 			$rm = $wpdb->get_row("SELECT count(id) as no FROM $table_name3 where name='$room'");
 			if (!$rm->no)
 			{
	 			$msg="Room $room does not exist!";
	 			$loggedin=0;
 			}
}
 	
//configure a picture to show when this user is clicked
$userPicture = urlencode("uploads/_sessions/${username}_240.jpg");
$avatarPicture = urlencode("uploads/_sessions/${username}_64.jpg");
$userLink=urlencode("http://www.videowhisper.com/");
$profileDetails = "Profile details for <i>$username</i><BR>Some html tags are supported (B I FONT IMG ...).";


if (!$welcome) $welcome=html_entity_decode(stripslashes($options['welcome']));

?>firstParam=fix&server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&serverRTMFP=<?=urlencode($serverRTMFP)?>&p2pGroup=<?=$p2pGroup?>&supportRTMP=<?=$supportRTMP?>&supportP2P=<?=$supportP2P?>&alwaysRTMP=<?=$alwaysRTMP?>&alwaysP2P=<?=$alwaysP2P?>&disableBandwidthDetection=<?=$disableBandwidthDetection?>&disableUploadDetection=<?=$disableBandwidthDetection?>&username=<?=urlencode($username)?>&loggedin=<?=$loggedin?>&userType=<?=$userType?>&administrator=<?=$admin?>&room=<?=urlencode($room)?>&welcome=<?=urlencode($welcome)?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&webserver=&msg=<?=urlencode($msg)?>&room_delete=0&room_create=0&camWidth=<?php echo $camRes[0];?>&camHeight=<?php echo $camRes[1];?>&camFPS=<?php echo $options['camFPS']?>&camBandwidth=<?php echo $camBandwidth?>&videoCodec=<?=$options['videoCodec']?>&codecProfile=<?=$options['codecProfile']?>&codecLevel=<?=$options['codecLevel']?>&soundCodec=<?=$options['soundCodec']?>&soundQuality=<?=$options['soundQuality']?>&micRate=<?=$options['micRate']?>&camMaxBandwidth=<?php echo $camMaxBandwidth; ?>&layoutCode=<?=urlencode(html_entity_decode($options['layoutCode']))?>&fillWindow=0&filterRegex=<?=$filterRegex?>&filterReplace=<?=$filterReplace?>&avatarPicture=<?=$avatarPicture?>&profileDetails=<?=urlencode($profileDetails)?><?php echo html_entity_decode($options['parameters']); ?>&visitor=<?php echo $visitor;?>&loadstatus=1