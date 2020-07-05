<?php
/**
 * Plugin-specific update functions
 * 
 * @author 		Thomas Maxson
 * @package 	Maxson_Portfolio/includes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


/**
 * Perform required v1.1 updates
 * 
 * @return      void
 */

function maxson_portfolio_v20_update()
{ 
	global $wpdb;

	$update_key = 'v1.1';

	if( maxson_portfolio_has_update_completed( $update_key ) )
		return false;

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

	maxson_portfolio_set_update_complete( $update_key );
}


/**
 * Perform required v2.0 updates
 * 
 * @return      void
 */

function maxson_portfolio_v20_update()
{ 
	global $wpdb;

	$update_key = 'v2.0';

	if( maxson_portfolio_has_update_completed( $update_key ) )
		return false;

	$projects = get_posts( array( 
		'post_parent'    => null, 
		'post_status'    => 'any', 
		'post_type'      => 'project', 
		'posts_per_page' => -1
	) );

	$post_meta_fields = array( 
		'maxson_project_gallery' => '_project_gallery', 
		'maxson_project_url'     => '_project_url', 
		'maxson_project_client'  => '_project_client'
	);

	$taxonomies = array( 
		'project_type'    => 'portfolio_category', 
		'project_service' => 'portfolio_tag'
	);

	if( $projects )
	{ 
		foreach( $projects as $project )
		{ 
			set_post_type( $project->ID, 'portfolio_project' );

		} // endforeach
	} // endif

	// Rename post meta fields
	foreach( $post_meta_fields as $from => $to )
	{ 
		$wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->postmeta}` SET `meta_key` = %s WHERE `meta_key` = %s", $to, $from ) );

	} // endforeach

	// Rename taxonomies
	foreach( $taxonomies as $from => $to )
	{ 
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = REPLACE(taxonomy, %s, %s) 
		WHERE taxonomy = %s", $from, $to, $from ) );

	} // endforeach

	maxson_portfolio_set_update_complete( $update_key );
}

?>