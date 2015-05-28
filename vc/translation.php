<?php
header('Content-Type: application/xml; charset=utf-8');
include("../../../../wp-config.php");
?><translations>
<?php
$options = get_option('VWvideoConferenceOptions');
echo html_entity_decode(stripslashes($options['translationCode']));
?>
</translations>