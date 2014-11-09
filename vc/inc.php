<?php
//do not load plugins
define('WP_INSTALLING', true);

include("../../../../wp-config.php");

$plugin = 'videowhisper-video-presentation/videowhisper_presentation.php';
// Validate plugin filename
if ( !validate_file($plugin) && '.php' == substr($plugin, -4) && file_exists(WP_PLUGIN_DIR . '/' . $plugin)) {
	include_once(WP_PLUGIN_DIR . '/' . $plugin);
}
unset($plugin);
?>