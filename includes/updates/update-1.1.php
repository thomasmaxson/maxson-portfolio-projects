<?php
/**
 * Update Portfolio Projects Plugin to v2.1
 * 
 * @author 		Thomas Maxson
 * @package 	Maxson_Portfolio/includes/Updates
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif

global $wpdb;

$post_meta_fields = array( 
	'_project_audio'      => '_audio', 
	'_project_gallery'    => '_gallery', 
	'_project_video'      => '_video', 
	'_project_url'        => '_url', 
	'_project_client'     => '_client', 
	'_project_start_date' => '_start_date', 
	'_project_end_date'   => '_end_date'
);

// Rename post meta fields
foreach( $post_meta_fields as $from => $to )
{ 
	$wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->postmeta}` SET `meta_key` = %s WHERE `meta_key` = %s", $to, $from ) );

} // endforeach

?>