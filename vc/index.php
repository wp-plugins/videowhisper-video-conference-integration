<?php
  
  $roomname = urlencode($_GET['r']);
  if (!$roomname) $roomname = urlencode($_GET['roomname']);
  
  include_once("incsan.php");
  sanV($roomname);
   
  $baseurl="";
  $swfurl=$baseurl."videowhisper_conference.swf?room=".$roomname;
  $bgcolor="#051e43";
  
  include("flash_detect.php");
?>
<style type="text/css">
<!--
BODY
{
	margin:0px;
	background-color: #333;
}
-->
</style>
<object width="100%" height="100%">
<param name="movie" value="<?=$swfurl?>" /><param name="bgcolor" value="<?=$bgcolor?>" /><param name="salign" value="lt" /><param name="scale" value="noscale" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> <param name="base" value="<?=$baseurl?>" /> <param name="wmode" value="transparent" /> <embed width="100%" height="100%" scale="noscale" salign="lt" src="<?=$swfurl?>" bgcolor="<?=$bgcolor?>" base="<?=$baseurl?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>
</object>
<noscript>
<p align=center><a href="http://www.videowhisper.com/?p=Video+Conference"><strong>VideoWhisper Video Conference Software</strong></a></p>
<p align="center"><strong>This content requires the Adobe Flash Player:
<a href="http://www.macromedia.com/go/getflash/">Get Flash</a></strong>!</p>
</noscript>
