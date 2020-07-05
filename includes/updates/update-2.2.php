<?php
/**
 * Update Portfolio Projects Plugin to v2.2
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


$taxonomies = array( 
	'project_type' => 'portfolio_media'
);

// Rename taxonomies
foreach( $taxonomies as $from => $to )
{ 
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = REPLACE(taxonomy, %s, %s) 
	WHERE taxonomy = %s", $from, $to, $from ) );

} // endforeach



$term_meta_fields = array( 
	'_project_type' => '_project_media'
);

// Rename post meta fields
foreach( $term_meta_fields as $from => $to )
{ 
	$wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->termmeta}` SET `meta_key` = %s WHERE `meta_key` = %s", $to, $from ) );

} // endforeach

?>