=== VideoWhisper Video Conference Integration ===
Contributors: videowhisper, VideoWhisper.com
Author: VideoWhisper.com
Author URI: http://www.videowhisper.com
Plugin Name: VideoWhisper Video Conference
Plugin URI: http://www.videowhisper.com/?p=WordPress+Video+Conference
Donate link: http://www.videowhisper.com/?p=Invest
Tags: video, conference, chat, webcam, BuddyPress, live, on air, flash, fms, red5, wowza, audio, video chat, videochat, widget, plugin, media, av, channel, sidebar, cam, group, groups, tab, P2P, videoconference, communication, collaboration, community, meeting, remote, telepresence, videopresence, presence, online
Requires at least: 2.7
Tested up to: 3.2.1
Stable tag: trunk

This Video Conferencing web based software can easily be used with WordPress sites to implement video conference for site users.

== Description ==
VideoWhisper Video Conference is a modern web based multiple way video chat and real time file sharing tool.

Latest version includes H264 and Speex support, acoustic echo cancellation, P2P groups support for better, faster video streaming and lower rtmp server bandwidth usage.

This plugin uses the WordPress username to login existing users. If the user is not logged into WordPress a warning message is shown and the visitor can click that to get back to the main WordPress website for registration/login. 

Includes a widget that displays active rooms (with number of participants) and conference access link.
A Video Conference page is added to the website and can be disabled from settings.

There is a settings page with multiple parameters and permissions (what users can access - all, only members, predefined list).

BuddyPress integration: If BuddyPress is installed this will add a Video Conference tab to the group, where users can video chat realtime in group room.

This software is great for meetings, trainings, conferences, live events, recruiting, consultations, coaching and of course casual community chat. This brings people together instantly and without travel costs. These benefits open a wide range of new business opportunities and bring the extra value needed by established sites struggling to go ahead of their competitors.

Special requirements: This plugin has requirements beyond regular WordPress hosting specifications: a RTMP host is needed for persistent connections to manage live interactions and streaming. More details about this, including solutions are provided on the Installation section pages.

== Installation ==
* Before installing this make sure all hosting requirements are met: http://www.videowhisper.com/?p=Requirements
* Install the RTMP application using these instructions: http://www.videowhisper.com/?p=RTMP+Applications
* Copy this plugin folder to your wordpress installation in your plugins folder. You should obtain wp-content/plugins/videowhisper-video-conference-integration .
* Enable the plugin from Wordpress admin area and fill the "Settings", including rtmp address there.
* Enable the widget if you want to display active rooms (with number of participants) and conference access link. 

== Screenshots ==
1. Video Conference
2. Video Conference Media Support
3. Video Conference Menu

== Desktop Sharing / Screen Broadcasting ==
If your users want to broadcast their screen (when playing a game, using a program, tutoring various computer skills) they can do that easily just by using a screen sharing driver that simulates a webcam from desktop contents. Read more on http://www.videochat-scripts.com/screen-sharing-with-flash-video-chat-software/ . 

== Documentation ==
* Plugin Homepage : http://www.videowhisper.com/?p=WordPress+Video+Conference
* Application Homepage : http://www.videowhisper.com/?p=Video+Conference
* Forum : http://www.videowhisper.com/forum.php?ftid=1&t=Video-Conference-software-videochat-chat

== Demo ==
* See BuddyPress integration live on http://livon.tv/
* See it live on http://www.videochat-scripts.com/video-conference/

== Extra ==
More information, the latest updates, other plugins and non-WordPress editions can be found at http://www.videowhisper.com/ .

== Changelog ==

= 4.72 =
* Integrated latest application version (v4.72)
* Codec settings

= 4.51 =
* Integrated latest application version (v4.51) that includes P2P. 
* Added more settings to control P2P / RTMP streaming, bandwidth detection.
* Fixed some possible security vulnerabilites for hosts with magic_quotes Off.

= 3.1 =
* BuddyPress integration: If BuddyPress is installed this will add a Video Conference tab to the group where users can video chat realtime in group room.

= 3.0 =
* Everything is in the plugin folder to allow automated updates.
* Settings page to fill rtmp address.
* Choose name to use in application (display name, login, nice name).
* Access permissions (all, members, list).
* List number of participants for each room.

= 2.0 =
* Compatibility updates.

= 1.0.2 =
* Compatibility updates and widget.

= 1.0 =
* Plugin to integrate video conference installed in a videowhisper_conference folder on site root.