<?php
if ( strstr($_GET["room"],"/") || strstr($_GET["room"],"..") || strstr($_GET["filename"],"/") || strstr($_GET["filename"],"..") ) exit;
if (!$_GET["room"]||!$_GET["filename"]) exit;
chmod("uploads/".$_GET["room"]."/".$_GET["filename"], 0766);
unlink("uploads/".$_GET["room"]."/".$_GET["filename"]);
?>loadstatus=1
