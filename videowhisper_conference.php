<?php
/*
Plugin Name: VideoWhisper Video Conference
Plugin URI: http://www.videowhisper.com/?p=WordPress+Video+Conference
Description: Video Conference
Version: 4.91	
Author: VideoWhisper.com
Author URI: http://www.videowhisper.com/
Contributors: videowhisper, VideoWhisper.com
*/

if (!class_exists("VWvideoConference")) 
{
	
 class VWvideoConference 
 {
        
	function VWvideoConference() 
	{ //constructor	
    }
	
	function settings_link($links) {
	  $settings_link = '<a href="options-general.php?page=videowhisper_conference.php">'.__("Settings").'</a>';
	  array_unshift($links, $settings_link);
	  return $links;
	}
	
	function init()
	{
	    $plugin = plugin_basename(__FILE__);
	    add_filter("plugin_action_links_$plugin",  array('VWvideoConference','settings_link') );
	  
	    wp_register_sidebar_widget('videoConferenceWidget','VideoWhisper Conference', array('VWvideoConference', 'widget') );


		//shortcodes
		add_shortcode('videowhisper_conference_manage',array( 'VWvideoConference', 'shortcode_manage'));
		add_shortcode('videowhisper_conference',array( 'VWvideoConference', 'shortcode_conference'));
	  
	  
	        //update page if not exists or deleted
            $page_id = get_option("vw_vc_page_manage");
            $page_id2 = get_option("vw_vc_page_landing");

            if (!$page_id || $page_id == "-1" || !$page_id2 || $page_id2 == "-1")
            add_action('wp_loaded', array('VWvideoConference','updatePages'));
            
	    //check db
	  	$vw_dbvc_version = "2.0";

		global $wpdb;
		$table_name = $wpdb->prefix . "vw_vcsessions";
		$table_name3 = $wpdb->prefix . "vw_vcrooms";

			
		$installed_ver = get_option( "vw_dbvc_version" );

		if( $installed_ver != $vw_dbvc_version ) 
		{
		$wpdb->flush();
		
		$sql = "DROP TABLE IF EXISTS `$table_name`;
		CREATE TABLE `$table_name` (
		  `id` int(11) NOT NULL auto_increment,
		  `session` varchar(64) NOT NULL,
		  `username` varchar(64) NOT NULL,
		  `room` varchar(64) NOT NULL,
		  `message` text NOT NULL,
		  `sdate` int(11) NOT NULL,
		  `edate` int(11) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `type` tinyint(4) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `status` (`status`),
		  KEY `type` (`type`),
		  KEY `room` (`room`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Video Whisper: Sessions - 2009@videowhisper.com' AUTO_INCREMENT=1 ;
		
		DROP TABLE IF EXISTS `$table_name3`;
		CREATE TABLE `$table_name3` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(64) NOT NULL,
		  `owner` int(11) NOT NULL,
		  `sdate` int(11) NOT NULL,
		  `edate` int(11) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `type` tinyint(4) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `name` (`name`),
		  KEY `status` (`status`),
		  KEY `type` (`type`),
		  KEY `owner` (`owner`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Video Whisper: Rooms - 2014@videowhisper.com' AUTO_INCREMENT=1 ;
		";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		if (!$installed_ver) add_option("vw_dbvc_version", $vw_dbvc_version);
		else update_option( "vw_dbvc_version", $vw_dbvc_version );
			
		$wpdb->flush();
		}			
		
	}
		



        function updatePages()
        {


            $options = get_option('VWvideoConferenceOptions');

            //if not disabled create
            if ($options['disablePage']=='0')
            {
                global $user_ID;
                $page = array();
                $page['post_type']    = 'page';
                $page['post_content'] = '[videowhisper_conference_manage]';
                $page['post_parent']  = 0;
                $page['post_author']  = $user_ID;
                $page['post_status']  = 'publish';
                $page['post_title']   = 'Setup Conference';

                $page_id = get_option("vw_vc_page_manage");
                if ($page_id>0) $page['ID'] = $page_id;

                $pageid = wp_insert_post ($page);
                update_option( "vw_vc_page_manage", $pageid);
            }

            if ($options['disablePageC']=='0')
            {
                global $user_ID;
                $page = array();
                $page['post_type']    = 'page';
                $page['post_content'] = '[videowhisper_conference]';
                $page['post_parent']  = 0;
                $page['post_author']  = $user_ID;
                $page['post_status']  = 'publish';
                $page['post_title']   = 'Video Conference';

                $page_id = get_option("vw_vc_page_landing");
                if ($page_id>0) $page['ID'] = $page_id;

                $pageid = wp_insert_post ($page);
                update_option( "vw_vc_page_landing", $pageid);
            }

        }

        function deletePages()
        {
            $options = get_option('VWvideoConferenceOptions');

            if ($options['disablePage'])
            {
                $page_id = get_option("vw_vc_page_manage");
                if ($page_id > 0)
                {
                    wp_delete_post($page_id);
                    update_option( "vw_vc_page_manage", -1);
                }
            }

            if ($options['disablePageC'])
            {
                $page_id = get_option("vw_vc_page_landing");
                if ($page_id > 0)
                {
                    wp_delete_post($page_id);
                    update_option( "vw_vc_page_landing", -1);
                }
            }

        }



        //if any key matches any listing
        function inList($keys, $data)
        {
            if (!$keys) return 0;

            $list=explode(",", strtolower(trim($data)));

            foreach ($keys as $key)
                foreach ($list as $listing)
                    if ( strtolower(trim($key)) == trim($listing) ) return 1;

                    return 0;
        }

			function getCurrentURL()
			{
				$currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
				$currentURL .= $_SERVER["SERVER_NAME"];
			
				if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
				{
			    	$currentURL .= ":".$_SERVER["SERVER_PORT"];
				} 
			
			        $currentURL .= $_SERVER["REQUEST_URI"];
				return $currentURL;
			}
			
			function roomURL($room)
			{
			    $options = get_option('VWvideoConferenceOptions');
			            
				if ($options['accessLink']=='site')
				{ 
				 $page_id = get_option("vw_vc_page_landing"); 
				 if ($page_id>0)
				 {
				 $permalink = get_permalink($page_id);
				 if ($permalink)
				 return add_query_arg(array('r'=>sanitize_file_name($room)),$permalink);
				 }

				}
				
				//else just load full page
				return plugin_dir_url(__FILE__) ."vc/?r=" . urlencode(sanitize_file_name($room));
			}

function path2url($file, $Protocol='http://') {
    return $Protocol.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
}

        function shortcode_conference($atts)
        {
        
        $roomname = sanitize_file_name($_GET['r']);
		if ($atts['room']) $roomname = sanitize_file_name($atts['room']);
		
		$baseurl = plugin_dir_url(__FILE__) .'vc/';
			
	$swfurl = $baseurl . "videowhisper_conference.swf?room=" . $roomname;
	$bgcolor="#051e43";
	
	$pagecode=<<<ENDCODE
	<div id="videoconference_container" style="height:650px">
	<object width="100%" height="100%">
	<param name="movie" value="$swfurl" /><param name="bgcolor" value="$bgcolor" /><param name="salign" value="lt" /><param name="scale" value="noscale" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> <param name="base" value="$baseurl" /> <embed width="100%" height="100%" scale="noscale" salign="lt" src="$swfurl" bgcolor="$bgcolor" base="$baseurl" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"></embed>
	</object>
	<noscript>
	<p align=center><a href="http://www.videowhisper.com/?p=Video+Conference"><strong>VideoWhisper Video Conference Software</strong></a></p>
	<p align="center"><strong>This content requires the Adobe Flash Player:
	<a href="http://www.macromedia.com/go/getflash/">Get Flash</a></strong>!</p>
	</noscript>
	</div>
	<p><a href="$baseurl">Click here to video conference is a full page!</a></p>
ENDCODE;

return $pagecode;
        }
        			
        function shortcode_manage()
        {

            //can user create room?
            $options = get_option('VWvideoConferenceOptions');


            $canBroadcast = $options['canBroadcast'];
            $broadcastList = $options['broadcastList'];
            $userName =  $options['userName']; if (!$userName) $userName='user_nicename';

            $loggedin=0;

            global $current_user;
            get_currentuserinfo();
            if ($current_user->$userName) $username = $current_user->$userName;

            //access keys
            $userkeys = $current_user->roles;
            $userkeys[] = $current_user->user_login;
            $userkeys[] = $current_user->ID;
            $userkeys[] = $current_user->user_email;
            $userkeys[] = $current_user->display_name;

            switch ($canBroadcast)
            {
            case "members":
                if ($username) $loggedin=1;
                else $htmlCode .= "<a href=\"/\">Please login first or register an account if you don't have one!</a>";
                break;
            case "list";
                if ($username)
                    if (VWvideoConference::inList($userkeys, $broadcastList)) $loggedin=1;
                    else $htmlCode .= "<a href=\"/\">$username, you are not allowed to setup rooms.</a>";
                    else $htmlCode .= "<a href=\"/\">Please login first or register an account if you don't have one!</a>";
                    break;
            }

            if (!$loggedin)
            {
                $htmlCode .='<p>This pages allows creating and managing conferencing rooms for register members that have this feature enabled.</p>' . $canBroadcast;
                return $htmlCode;
            }

            $this_page    =   VWvideoConference::getCurrentURL();
 
 			if ($loggedin)
			{
			global $wpdb;           
			$table_name = $wpdb->prefix . "vw_vcsessions";
			$table_name3 = $wpdb->prefix . "vw_vcrooms";
 
			$wpdb->flush();
 			$rmn = $wpdb->get_row("SELECT count(id) as no FROM $table_name3 where owner='".$current_user->ID."'");
 					 				
  				//delete
				if ($delid=(int) $_GET['delete'])
				{
					$sql = $wpdb->prepare("DELETE FROM $table_name3 where owner='".$current_user->ID."' AND id='%d'", array($delid));
					$wpdb->query($sql);
					$wpdb->flush();
					$htmlCode .=  "<div class='update'>Room #$delid was deleted.</div>";
					 
					$rmn = $wpdb->get_row("SELECT count(id) as no FROM $table_name3 where owner='".$current_user->ID."'");
				}

				//create
				$room = sanitize_file_name($_POST['room']);
				if ($room)
				{
					
					$ztime=time();

					$sql = $wpdb->prepare("SELECT owner FROM $table_name3 where name='%s'", array($room));
					$rdata = $wpdb->get_row($sql);
					if (!$rdata)
					{
						if ($rmn->no < $options['maxRooms'])
						{
							$sql=$wpdb->prepare("INSERT INTO `$table_name3` ( `name`, `owner`, `sdate`, `edate`, `status`, `type`) VALUES ('%s', '".$current_user->ID."', '$ztime', '0', 1, '%d')",array($room, $_POST['type']));
							$wpdb->query($sql);
							$wpdb->flush();
							$htmlCode .=  "<div class='update'>Room '$room' was created.</div>";
							 
							 $rmn = $wpdb->get_row("SELECT count(id) as no FROM $table_name3 where owner='".$current_user->ID."'");
							 			
						}else $htmlCode .=  "<div class='error'>Room limit reached!</div>";
					}
					else
					{
						$htmlCode .=  "<div class='error'>Room name '$room' is already in use. Please choose another name!</div>";
						$room="";
					}
				}

				//list
				$wpdb->flush();

				$sql = "SELECT * FROM $table_name3 where owner='".$current_user->ID."'";
				$rooms=$wpdb->get_results($sql);

				$htmlCode .=  "<H3>My Rooms (" . $rmn->no . '/' . $options['maxRooms'].")</H3>";
				if (count($rooms))
				{
					$htmlCode .=  "<table>";
					$htmlCode .=  "<tr><th>Room</th><th>Link (use to invite)</th><th>Online</th><th>Type</th><th>Manage</th></tr>";
					$root_url = plugins_url() . "/";
					foreach ($rooms as $rd)
					{
						$rm=$wpdb->get_row("SELECT count(*) as no, group_concat(username separator ' <BR> ') as users, room as room FROM `$table_name` where status='1' and type='1' AND room='".$rd->name."' GROUP BY room");

						$htmlCode .=  "<tr><td><a href='" . VWvideoConference::roomURL($rd->name)."'><B>".$rd->name."</B></a></td> <td>" . VWvideoConference::roomURL($rd->name) ."</td> <td>".($rm->no>0?$rm->users:'0')."</td><td>".($rd->type==1?'Public':($rd->type==2?"Private":$rd->type))."</td> <td><a href='".$this_page.(strstr($this_page,'?')?'&':'?')."delete=".$rd->id."'>Delete</a></td> </tr>";
					}
					$htmlCode .=  "</table>";

				}
				else $htmlCode .=  "You don't currently have any rooms.";


				//create form
				if (!$room) 
				if ($rmn->no < $options['maxRooms'])
				$htmlCode .=  '<h3>Setup a New Room</h3><form method="post" action="' . $this_page .'"  name="adminForm">
		  <input name="room" type="text" id="room" value="Room_'.base_convert((time()-1225000000),10,36).'" size="20" maxlength="64" />
		  <select id="type" name="type">
		  <option value="2">Private</option>		  
		  <option value="1">Public</option>
		   </select>
		  <input type="submit" name="button" id="button" value="Create" />
		</form>All your rooms will be accessible for you in conference room list. Public rooms will be listed for everybody.
		'; else $htmlCode .= "You can't setup new rooms because you reached room limit (".$options['maxRooms'].").";
			}   
			
			return $htmlCode;
    }
     	
	function widgetContent()
	{

		global $wpdb;
		$table_name = $wpdb->prefix . "vw_vcsessions";
		
		$root_url = get_bloginfo( "url" ) . "/";
		
		$page_id = get_option("vw_vc_page_landing");
		if ($page_id > 0) $permalink = get_permalink( $page_id );		
		else $permalink = $root_url . "wp-content/plugins/videowhisper-video-conference-integration/vc/?";
			 
		//clean recordings
		$exptime=time()-30;
		$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
		$wpdb->query($sql);
			
		$wpdb->flush();
		
		$items =  $wpdb->get_results("SELECT room, count(*) as users FROM `$table_name` where status='1' and type='1' GROUP BY room ORDER BY users DESC");

		echo "<ul>";
		if ($items)	foreach ($items as $item) echo "<li><a href='".$root_url . "wp-content/plugins/videowhisper-video-conference-integration/vc/?roomname=" . $item->room . "'><B>" . $item->room . "</B></a> (" . $item->users .")</a></li>";
		else echo "<li>No active conference rooms.</li>";
		echo "</ul>";

	?><a href="<?php echo $permalink; ?>"><img src="<?php echo $root_url; ?>wp-content/plugins/videowhisper-video-conference-integration/vc/templates/conference/i_webcam.png" align="absmiddle" border="0">Enter Conference</a>
	<?
		$options = get_option('VWvideoConferenceOptions');
		$state = 'block' ;
		if (!$options['videowhisper']) $state = 'none';	
		echo '<div id="VideoWhisper" style="display: ' . $state . ';"><p>Powered by VideoWhisper <a href="http://www.videowhisper.com/?p=WordPress+Video+Conference">Video Conference Software</a>.</p></div>';
	}
	
	function widget($args) 
	{
	  extract($args);
	  echo $before_widget;
	  echo $before_title;?>Video Conference<?php echo $after_title;
	  VWvideoConference::widgetContent();
	  echo $after_widget;
	}

	function menu() {
	  add_options_page('Video Conference Options', 'Video Conference', 9, basename(__FILE__), array('VWvideoConference', 'options'));
	}
	
	function getAdminOptions() //also updates
	{			
				$adminOptions = array(
                'disablePage' => '0',
                'disablePageC' => '0',
                
				'userName' => 'display_name',
				'rtmp_server' => 'rtmp://localhost/videowhisper',
				'rtmp_amf' => 'AMF3',
				'canAccess' => 'all',
				'accessList' => 'Super Admin, Administrator, Editor, Author, Contributor, Subscriber',
				
				'canBroadcast' => 'members',
				'broadcastList' => 'Super Admin, Administrator, Editor, Author',
				'maxRooms' => '3',
				'accessLink' => 'site',
				'anyRoom' => '1',
		
			    'landingRoom' => 'lobby',
 			    'lobbyRoom' => 'Lobby',
 			    
 			    'welcome' => htmlentities('Welcome to video conference room! <BR><font color="#3CA2DE">&#187;</font> Click top left preview panel for more options including selecting different camera and microphone. <BR><font color="#3CA2DE">&#187;</font> Click any participant from users list for more options including extra video panels. <BR><font color="#3CA2DE">&#187;</font> Try pasting urls, youtube movie urls, picture urls, emails, twitter accounts as @videowhisper in your text chat. <BR><font color="#3CA2DE">&#187;</font> Download daily chat logs from file list.'),
 			    'layoutCode' => '',
 			    'parameters' => htmlentities('&generateSnapshots=1&pushToTalk=1&publicVideosN=0&publicVideosW=225&publicVideosH=217&publicVideosX=2&publicVideosY=560&publicVideosColumns=4&publicVideosRows=0&avatarList=1&infoMenu=0&bufferLive=0.1&bufferFull=0.1&bufferLivePlayback=0.1&bufferFullPlayback=0.1&showCamSettings=1&advancedCamSettings=1&configureSource=0&disableVideo=0&disableSound=0&background_url=&autoViewCams=1&tutorial=0&file_upload=1&file_delete=1&panelFiles=1&showTimer=1&showCredit=1&disconnectOnTimeout=0&writeText=1&floodProtection=3&regularWatch=1&newWatch=1&privateTextchat=1&ws_ads=ads.php&adsTimeout=15000&adsInterval=0&statusInterval=10000&verboseLevel=2'),

                'camResolution' => '320x240',
                'camFPS' => '15',

                'camBandwidth' => '40960',
                'camMaxBandwidth' => '81920',
                
				'videoCodec'=>'H264',
				'codecProfile' => 'main',
				'codecLevel' => '3.1',
				
				'soundCodec'=> 'Speex',
				'soundQuality' => '9',
				'micRate' => '22',
				
				'serverRTMFP' => 'rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/',
				
				'p2pGroup' => 'VideoWhisper',
				'supportRTMP' => '1',
				'supportP2P' => '0',
				'alwaysRTMP' => '1',
				'alwaysP2P' => '0',
				'disableBandwidthDetection' => '0',
				'videowhisper' => 0
				);
			
				$options = get_option('VWvideoConferenceOptions');
				if (!empty($options)) {
					foreach ($options as $key => $option)
						$adminOptions[$key] = $option;
				} 
				
				 
				update_option('VWvideoConferenceOptions', $adminOptions);
				return $adminOptions;
	}
	
	//production: use $options = get_option('VWvideoConferenceOptions');          
	
	function options() 
	{

		 //save form
		 $options = VWvideoConference::getAdminOptions();		 
         if (isset($_POST))
            {

                foreach ($options as $key => $value)
                    if (isset($_POST[$key])) $options[$key] = $_POST[$key];
                    
                    update_option('VWvideoConferenceOptions', $options);
            }
					
		    $page_id = get_option("vw_vc_page_manage");
            if ($page_id != '-1' && $options['disablePage']!='0') VWvideoConference::deletePages();

            $page_idC = get_option("vw_vc_page_landing");
            if ($page_idC != '-1' && $options['disablePageC']!='0') VWvideoConference::deletePages();
            
         $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'server';

				
	  ?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>VideoWhisper Video Conference Settings</h2>


<h2 class="nav-tab-wrapper">
	<a href="options-general.php?page=videowhisper_conference.php&tab=server" class="nav-tab <?php echo $active_tab=='server'?'nav-tab-active':'';?>">Server</a>
	<a href="options-general.php?page=videowhisper_conference.php&tab=setup" class="nav-tab <?php echo $active_tab=='setup'?'nav-tab-active':'';?>">Room Setup</a>
	<a href="options-general.php?page=videowhisper_conference.php&tab=access" class="nav-tab <?php echo $active_tab=='access'?'nav-tab-active':'';?>">Access</a>    
    <a href="options-general.php?page=videowhisper_conference.php&tab=video" class="nav-tab <?php echo $active_tab=='video'?'nav-tab-active':'';?>">Video</a>
	<a href="options-general.php?page=videowhisper_conference.php&tab=integration" class="nav-tab <?php echo $active_tab=='integration'?'nav-tab-active':'';?>">Integration</a>
</h2>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

<?php
            switch ($active_tab)
            {
            case 'server':
            ?>
            
<h3>Server Settings</h3>
<h4>RTMP Address</h4>
<p>To run this, make sure your hosting environment meets all <a href="http://www.videowhisper.com/?p=Requirements" target="_blank">requirements</a>.  If you don't have a videowhisper rtmp address yet (from a managed rtmp host), go to <a href="http://www.videowhisper.com/?p=RTMP+Applications" target="_blank">RTMP Application Setup</a> for  installation details.</p>
<input name="rtmp_server" type="text" id="rtmp_server" size="64" maxlength="256" value="<?=$options['rtmp_server']?>"/>

<h4>Disable Bandwidth Detection</h4>
<p>Required on some rtmp servers that don't support bandwidth detection and return a Connection.Call.Fail error.</p>
<select name="disableBandwidthDetection" id="disableBandwidthDetection">
  <option value="0" <?=$options['disableBandwidthDetection']?"":"selected"?>>No</option>
  <option value="1" <?=$options['disableBandwidthDetection']?"selected":""?>>Yes</option>
</select>

<h4>RTMFP Address</h4>
<p> Get your own independent RTMFP address by registering for a free <a href="https://www.adobe.com/cfusion/entitlement/index.cfm?e=cirrus" target="_blank">Adobe Cirrus developer key</a>. This is required for P2P support.</p>
<input name="serverRTMFP" type="text" id="serverRTMFP" size="80" maxlength="256" value="<?=$options['serverRTMFP']?>"/>

<h4>P2P Group</h4>
<input name="p2pGroup" type="text" id="p2pGroup" size="32" maxlength="64" value="<?=$options['p2pGroup']?>"/>

<h4>Support RTMP Streaming</h4>
<select name="supportRTMP" id="supportRTMP">
  <option value="0" <?=$options['supportRTMP']?"":"selected"?>>No</option>
  <option value="1" <?=$options['supportRTMP']?"selected":""?>>Yes</option>
</select>

<h4>Always do RTMP Streaming</h4>
<p>Enable this if you want all streams to be published to server, no matter if there are registered subscribers or not (in example if you're using server side video archiving and need all streams published for recording).</p>
<select name="alwaystRTMP" id="alwaystRTMP">
  <option value="0" <?=$options['alwaystRTMP']?"":"selected"?>>No</option>
  <option value="1" <?=$options['alwaystRTMP']?"selected":""?>>Yes</option>
</select>

<h4>Support P2P Streaming</h4>
<select name="supportP2P" id="supportP2P">
  <option value="0" <?=$options['supportP2P']?"":"selected"?>>No</option>
  <option value="1" <?=$options['supportP2P']?"selected":""?>>Yes</option>
</select>

<h4>Always do P2P Streaming</h4>
<select name="alwaysP2P" id="alwaysP2P">
  <option value="0" <?=$options['alwaysP2P']?"":"selected"?>>No</option>
  <option value="1" <?=$options['alwaysP2P']?"selected":""?>>Yes</option>
</select>
<?php
                break;
                
            case 'setup':
?>
<h3>Room Setup</h3>
<h5>Who can create rooms</h5>
<select name="canBroadcast" id="canBroadcast">
  <option value="members" <?php echo $options['canBroadcast']=='members'?"selected":""?>>All Members</option>
  <option value="list" <?php echo $options['canBroadcast']=='list'?"selected":""?>>Members in List *</option>
</select>

<h5>* Members in List: allowed to broadcast video (comma separated user names, roles, emails, IDs)</h5>
<textarea name="broadcastList" cols="64" rows="3" id="broadcastList"><?php echo $options['broadcastList']?>
</textarea>

<h4>Room limit</h4>
<input name="maxRooms" type="text" id="maxRooms" size="3" maxlength="3" value="<?=$options['maxRooms']?>"/>
<br>Maximum number of rooms each user can have.

<h4>Page for Management</h4>
<p>Add room management page (Page ID <a href='post.php?post=<?php echo get_option("vw_vc_page_manage"); ?>&action=edit'><?php echo get_option("vw_vc_page_manage"); ?></a>) with shortcode [videowhisper_conference_manage]</p>
<select name="disablePage" id="disablePage">
  <option value="0" <?php echo $options['disablePage']=='0'?"selected":""?>>Yes</option>
  <option value="1" <?php echo $options['disablePage']=='1'?"selected":""?>>No</option>
</select>
<?php
                break;
                
            case 'access':
?>
<h3>Room Access</h3>
<h4>Who can access video conference</h4>
<select name="canAccess" id="canAccess">
  <option value="all" <?=$options['canAccess']=='all'?"selected":""?>>Anybody</option>
  <option value="members" <?=$options['canAccess']=='members'?"selected":""?>>All Members</option>
  <option value="list" <?=$options['canAccess']=='list'?"selected":""?>>Members in List</option>  
</select>

<h4>Members allowed to access video conference</h4>
<textarea name="accessList" cols="64" rows="3" id="accessList"><?=$options['accessList']?>
</textarea>
<br>Roles, usernames, user IDs, user emails.


<h4>Page for Conference</h4>
<p>Add landing conference page (Page ID <a href='post.php?post=<?php echo get_option("vw_vc_page_landing"); ?>&action=edit'><?php echo get_option("vw_vc_page_landing"); ?></a>) with shortcode [videowhisper_conference]</p>
<select name="disablePageC" id="disablePageC">
  <option value="0" <?php echo $options['disablePageC']=='0'?"selected":""?>>Yes</option>
  <option value="1" <?php echo $options['disablePageC']=='1'?"selected":""?>>No</option>
</select>

<h4>Access Link</h4>
<select name="accessLink" id="accessLink">
  <option value="site" <?=$options['accessLink']=='site'?"selected":""?>>Site Page</option>
  <option value="full" <?=$options['accessLink']=='full'?"selected":""?>>Full Page</option> 
</select>
<br>Full page will load conference room in a full page without site template (useful when template does not provide enough space to load room layout).

<h4>Default landing room</h4>
<select name="landingRoom" id="landingRoom">
  <option value="lobby" <?=$options['landingRoom']=='lobby'?"selected":""?>>Lobby</option>
  <option value="username" <?=$options['landingRoom']=='username'?"selected":""?>>Username</option>

</select>
<br>Username will allow registered users to start their own rooms, without room setup, as each user will land in room with own room name.
  
<h4>Lobby room name</h4>
<input name="lobbyRoom" type="text" id="lobbyRoom" size="16" maxlength="16" value="<?=$options['lobbyRoom']?>"/>
<br>Ex: Lobby

<h4>Allow Any Room</h4>
<select name="anyRoom" id="anyRoom">
  <option value="1" <?=$options['anyRoom']=='1'?"selected":""?>>Yes</option>
  <option value="0" <?=$options['anyRoom']=='0'?"selected":""?>>No</option> 
</select>
<br>Any room name will be accessible if this is enabled (required by username rooms). Disable to allow accessing only previously setup rooms and landing room.

<h4>Room Shortcode</h4>
<h5>[videowhisper_conference room="room-name"]</h5>
This shortcode will display video conference room room-name. If room parameter is not provided it will use parameter 'r' from link. If that's also not available it will display default landing room as configured above. 
<?php
                break;
                
            case 'integration':
            
   			$options['welcome'] = htmlentities(stripslashes($options['welcome']));
			$options['layoutCode'] = htmlentities($options['layoutCode']);
   			$options['parameters'] = htmlentities($options['parameters']);


?>


<h3>Integration Settings</h3>
<h4>Username</h4>
<select name="userName" id="userName">
  <option value="display_name" <?=$options['userName']=='display_name'?"selected":""?>>Display Name</option>
  <option value="user_login" <?=$options['userName']=='user_login'?"selected":""?>>Login (Username)</option>
  <option value="user_nicename" <?=$options['userName']=='user_nicename'?"selected":""?>>Nicename</option>  
</select>

<h4>Welcome Message</h4>
<textarea name="welcome" id="welcome" cols="64" rows="8"><?=$options['welcome']?></textarea>
<br>Shows in chatbox when entering video conference.

<h4>Custom Layout Code</h4>
<textarea name="layoutCode" id="layoutCode" cols="64" rows="8"><?=$options['layoutCode']?></textarea>
<br>Generate by writing and sending "/videowhisper layout" in chat (contains panel positions, sizes, move and resize toggles).

<h4>Parameters</h4>
<textarea name="parameters" id="parameters" cols="64" rows="8"><?=$options['parameters']?></textarea>
<br>Documented on <a href="http://www.videowhisper.com/?p=php+video+conference#customize">PHP Video Conference</a> edition page.

<h4>Show VideoWhisper Powered by</h4>
<select name="videowhisper" id="videowhisper">
  <option value="0" <?=$options['videowhisper']?"":"selected"?>>No</option>
  <option value="1" <?=$options['videowhisper']?"selected":""?>>Yes</option>
</select>
<?php
                break;
                
            case 'video':
?>
<h3>Video Settings</h3>
<h4>Default Webcam Resolution</h4>
<select name="camResolution" id="camResolution">
<?php
                foreach (array('160x120','320x240','480x360', '640x480', '720x480', '720x576', '1280x720', '1440x1080', '1920x1080') as $optItm)
                {
?>
  <option value="<?php echo $optItm;?>" <?php echo $options['camResolution']==$optItm?"selected":""?>> <?php echo $optItm;?> </option>
  <?php
                }
?>
 </select>

<h4>Default Webcam Frames Per Second</h4>
<select name="camFPS" id="camFPS">
<?php
                foreach (array('1','8','10','12','15','29','30','60') as $optItm)
                {
?>
  <option value="<?php echo $optItm;?>" <?php echo $options['camFPS']==$optItm?"selected":""?>> <?php echo $optItm;?> </option>
  <?php
                }
?>
 </select>


<h4>Video Stream Bandwidth</h4>
<input name="camBandwidth" type="text" id="camBandwidth" size="7" maxlength="7" value="<?php echo $options['camBandwidth']?>"/> (bytes/s)

<h4>Maximum Video Stream Bandwidth (at runtime)</h4>
<input name="camMaxBandwidth" type="text" id="camMaxBandwidth" size="7" maxlength="7" value="<?php echo $options['camMaxBandwidth']?>"/> (bytes/s)


<h4>Video Codec</h4>
<select name="videoCodec" id="videoCodec">
  <option value="H264" <?=$options['videoCodec']=='H264'?"selected":""?>>H264</option>
  <option value="H263" <?=$options['videoCodec']=='H263'?"selected":""?>>H263</option>  
</select>

<h4>H264 Video Codec Profile</h4>
<select name="codecProfile" id="codecProfile">
  <option value="main" <?=$options['codecProfile']=='main'?"selected":""?>>main</option>
  <option value="baseline" <?=$options['codecProfile']=='baseline'?"selected":""?>>baseline</option>  
</select>

<h4>H264 Video Codec Level</h4>
<input name="codecLevel" type="text" id="codecLevel" size="32" maxlength="64" value="<?=$options['codecLevel']?>"/> (1, 1b, 1.1, 1.2, 1.3, 2, 2.1, 2.2, 3, 3.1, 3.2, 4, 4.1, 4.2, 5, 5.1)

<h4>Sound Codec</h4>
<select name="soundCodec" id="soundCodec">
  <option value="Speex" <?=$options['soundCodec']=='Speex'?"selected":""?>>Speex</option>
  <option value="Nellymoser" <?=$options['soundCodec']=='Nellymoser'?"selected":""?>>Nellymoser</option>  
</select>

<h4>Speex Sound Quality</h4>
<input name="soundQuality" type="text" id="soundQuality" size="3" maxlength="3" value="<?=$options['soundQuality']?>"/> (0-10)

<h4>Nellymoser Sound Rate</h4>
<input name="micRate" type="text" id="micRate" size="3" maxlength="3" value="<?=$options['micRate']?>"/> (11/22/44)
<?php
                break;
                
            }
            
submit_button(); 
?>


</form>
</div>
	 <?
	}
	
 }
}

//instantiate
if (class_exists("VWvideoConference")) {
    $videoConference = new VWvideoConference();
}

//Actions and Filters   
if (isset($videoConference)) 
{
	add_action("plugins_loaded", array(&$videoConference , 'init'));
	add_action('admin_menu', array(&$videoConference , 'menu'));
	
	/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
	function videoConferenceBP_init() 
	{
		 if (class_exists('BP_Group_Extension'))  require( dirname( __FILE__ ) . '/bp.php' );
	}

	add_action( 'bp_init', 'videoConferenceBP_init' );

}
?>
