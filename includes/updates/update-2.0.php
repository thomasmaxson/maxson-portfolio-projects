<?php
/**
 * Update Portfolio Projects Plugin to v2.0
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


// http://wordpress.stackexchange.com/questions/97026/how-do-i-safely-change-the-name-of-a-custom-post-type


global $wpdb;


$args = array( 
	'post_parent'    => null, 
	'post_status'    => 'any', 
	'post_type'      => 'project', 
	'posts_per_page' => -1, 
	'fields'         => 'ids'
);

$project_ids = get_posts( $args );

if( $project_ids )
{ 
	foreach( $project_ids as $project_id )
	{ 
		set_post_type( $project_id, 'portfolio_project' );

	} // endforeach
} // endif


$post_meta_fields = array( 
	'maxson_project_gallery' => '_project_gallery', 
	'maxson_project_url'     => '_project_url', 
	'maxson_project_client'  => '_project_client'
);

// Rename post meta fields
foreach( $post_meta_fields as $from => $to )
{ 
	$wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->postmeta}` SET `meta_key` = %s WHERE `meta_key` = %s", $to, $from ) );

} // endforeach


$taxonomies = array( 
	'project_type'    => 'portfolio_category', 
	'project_service' => 'portfolio_tag'
);

// Rename taxonomies
foreach( $taxonomies as $from => $to )
{ 
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = REPLACE(taxonomy, %s, %s) 
	WHERE taxonomy = %s", $from, $to, $from ) );

} // endforeach

?>