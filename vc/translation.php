<?php
include("../../../../wp-config.php");
?>
<translations>
<?php
$options = get_option('VWvideoConferenceOptions');
echo html_entity_decode(stripslashes($options['translationCode']));
?>
</translations>