<?php
/**
 * Plugin-specific core functions
 * 
 * @author      Thomas Maxson
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


/**
 * Filter project short description (excerpt)
 * 
 * @return      void
 */

add_filter( 'maxson_portfolio_the_excerpt', 'wptexturize' );
add_filter( 'maxson_portfolio_the_excerpt', 'convert_smilies' );
add_filter( 'maxson_portfolio_the_excerpt', 'convert_chars' );
add_filter( 'maxson_portfolio_the_excerpt', 'wpautop' );
add_filter( 'maxson_portfolio_the_excerpt', 'shortcode_unautop' );
add_filter( 'maxson_portfolio_the_excerpt', 'prepend_attachment' );
add_filter( 'maxson_portfolio_the_excerpt', 'do_shortcode', 11 ); // AFTER wpautop()


/**
 * Filter project content
 * 
 * @return      void
 */

add_filter( 'maxson_portfolio_the_content', 'wptexturize' );
add_filter( 'maxson_portfolio_the_content', 'convert_smilies' );
add_filter( 'maxson_portfolio_the_content', 'convert_chars' );
add_filter( 'maxson_portfolio_the_content', 'wpautop' );
add_filter( 'maxson_portfolio_the_content', 'shortcode_unautop' );
add_filter( 'maxson_portfolio_the_content', 'prepend_attachment' );
add_filter( 'maxson_portfolio_the_content', 'do_shortcode', 11 ); // AFTER wpautop()


/**
 * Get plugin-specific supported taxonomy types
 * 
 * @return      array Array of taxonomy types
 */

function maxson_portfolio_get_taxonomy_types()
{ 
	$default_types = array( 
		'category' => __( 'Category', 'maxson' ), 
		'role'     => __( 'Role', 'maxson' ), 
		'tag'      => __( 'Tag', 'maxson' ), 
		'type'     => __( 'Type', 'maxson' )
	);

	$types = array_unique( apply_filters( 'maxson_portfolio_taxonomy_types', $default_types ) );

	return ( empty( $types ) || ! is_array( $types ) ) ? $default_types : $types;
}


/**
 * Get plugin-specific supported media types
 * 
 * @return      array Array of media types
 */

function maxson_portfolio_get_project_types()
{ 
	$defaults = array( 
	//	'none'    => __( 'None', 'maxson' ), 
		'audio'   => __( 'Audio', 'maxson' ), 
		'gallery' => __( 'Gallery', 'maxson' ), 
		'image'   => __( 'Image', 'maxson' ), 
		'video'   => __( 'Video', 'maxson' )
	);

	$types = array_unique( apply_filters( 'maxson_portfolio_project_types', $defaults ) );

	return ( empty( $types ) || ! is_array( $types ) ) ? $defaults : $types;
}


/**
 * Get plugin-specific supported audio formats
 * 
 * @return      array Array of audio formats
 */

function maxson_portfolio_get_audio_formats()
{ 
	$defaults = wp_get_audio_extensions();

	$formats = array_unique( apply_filters( 'maxson_portfolio_audio_formats', $defaults ) );

	return ( empty( $formats ) || ! is_array( $formats ) ) ? $defaults : $formats;
}


/**
 * Get plugin-specific supported video formats
 * 
 * @return      array Array of video formats
 */

function maxson_portfolio_get_video_formats()
{ 
	$defaults = wp_get_video_extensions();

	$formats = array_unique( apply_filters( 'maxson_portfolio_video_formats', $defaults ) );

	return ( empty( $formats ) || ! is_array( $formats ) ) ? $defaults : $formats;
}


/**
 * Store JavaScript in GLOBAL parameter for output
 * 
 * @return      void
 */

function maxson_portfolio_enqueue_js( $code )
{ 
	global $maxson_portfolio_js;

	if( empty( $maxson_portfolio_js ) )
	{
		$maxson_portfolio_js = '';

	} // endif

	$maxson_portfolio_js .= "\n{$code}\n";
}


/**
 * Output custom inline JavaScript in the footer
 * 
 * @return      void
 */

function maxson_portfolio_print_js()
{ 
	global $maxson_portfolio_js;

	if( ! empty( $maxson_portfolio_js ) )
	{ 
		$maxson_portfolio_js = wp_check_invalid_utf8( $maxson_portfolio_js, true );

		if( '' == $maxson_portfolio_js )
		{
			return false;

		} // endif

		$maxson_portfolio_js = preg_replace( '/[ ]{2,}|[\t]/', ' ', trim( $maxson_portfolio_js ) );

		$maxson_portfolio_js = str_replace( "\r", '', $maxson_portfolio_js );
	//	$maxson_portfolio_js = str_replace( "\n", '', $maxson_portfolio_js );
	//	$maxson_portfolio_js = str_replace( "\t", '', $maxson_portfolio_js );

		$output  = '<!-- Portfolio Projects by Maxson JavaScript -->' . "\n";
		$output .= '<script type="text/javascript">';
		$output .= $maxson_portfolio_js;
		$output .= '</script>' . "\n";

		echo apply_filters( 'maxson_portfolio_enqueued_javascript', $output );

		unset( $maxson_portfolio_js );

	} // endif
}


/**
 * Get the correct filename suffix for minified assets
 * 
 * @return      string|bool
 */

function maxson_portfolio_get_minified_suffix()
{ 
//	return ( maxson_portfolio_in_debug_mode() ) ? '.min' : false;
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : false;
}


/**
 * Create array of blog ids in the network if multisite setting is on
 * 
 * @return      array Array of blog IDs
 */

function maxson_portfolio_get_network_blog_ids()
{ 
	if( is_multisite() )
	{ 
		global $wpdb;

		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	} else
	{ 
		$blog_ids = array( get_current_blog_id() );

	} // endif

	return $blog_ids;
}


/**
 * Get an option value
 * 
 * @param       string      $key     (required) Setting to return
 * @param       string|bool $default (optional) Value to return if no option value exists
 * @return 		int|bool
 */

function maxson_portfolio_get_option( $key = null, $default = false )
{ 
	if( is_null( $key ) )
	{ 
		return $default;

	} // endif

	$value = get_option( "maxson_portfolio_{$key}", $default );

	if( empty( $value ) )
	{
		$value = $default;

	} // endif

	return apply_filters( "maxson_portfolio_get_option_{$key}", $value, $key, $default );
}


/**
 * Return plugin-specific archive limit
 * 
 * @param       bool     $default (optional) Value to return if no option value exists
 * @return 		int|bool
 */

function maxson_portfolio_get_archive_limit( $default = null )
{ 
	if( ! is_admin() && is_null( $default ) )
	{ 
		$default = get_option( 'posts_per_page' );

	} // endif

	$archive_limit = maxson_portfolio_get_option( 'archive_limit', $default );

	return $archive_limit;
}


/**
 * Return plugin-specific archive/page ID
 * 
 * @return 		int|bool
 */

function maxson_portfolio_has_archive_page_id()
{ 
	$archive_id = maxson_portfolio_get_archive_page_id();

	return ( ! empty( $archive_id ) ) ? true : false;
}


/**
 * Return plugin-specific archive/page ID
 * 
 * @return 		int|bool
 */

function maxson_portfolio_get_archive_page_id()
{ 
	$archive_id = maxson_portfolio_get_option( 'archive_page_id' );

	return ( ! empty( $archive_id ) && ( -1 != $archive_id ) ) ? absint( $archive_id ) : false;
}


/**
 * Return if plugin-specific page is being displayed
 * 
 * @param       int      $page_id
 * @return      int|bool
 */

function maxson_portfolio_is_archive_page( $page_id = null )
{ 
	$archive_id = maxson_portfolio_get_archive_page_id();

	if( ! is_null( $page_id ) )
	{ 
		return ( absint( $page_id ) == absint( $archive_id ) );

	} else
	{ 
		return ( is_post_type_archive( 'portfolio_project' ) || is_page( $archive_id ) );

	} // endif
}


/**
 * Return plugin-specific page URL
 * 
 * @return 		int|bool
 */

function maxson_portfolio_get_archive_page_url()
{ 
	$archive_id = maxson_portfolio_get_archive_page_id();

	if( ! empty( $archive_id ) && ( -1 != $archive_id ) )
	{ 
		return get_permalink( $archive_id );

	} else
	{ 
		return get_post_type_archive_link( 'portfolio_project' );

	} // endif
}


/**
 * Get plugin-specific default label
 * 
 * @return      string
 */

function maxson_portfolio_get_default_promoted_label()
{ 
	$label = _x( 'Promoted', 'Portfolio project default promoted label', 'maxson' );

	return apply_filters( 'maxson_portfolio_default_promoted_label', $label );
}


/**
 * Get plugin-specific date format
 * 
 * @return      string
 */

function maxson_portfolio_get_date_format()
{ 
	return apply_filters( 'maxson_portfolio_date_format', get_option( 'date_format' ) );
}


/**
 * Get plugin-specific time format
 * 
 * @return      string
 */

function maxson_portfolio_get_time_format()
{ 
	return apply_filters( 'maxson_portfolio_time_format', get_option( 'time_format' ) );
}


/**
 * Checks whether function is disabled
 * 
 * @param       string Name of the function
 * @return 		bool
 */

function maxson_portfolio_is_func_disabled( $function )
{ 
	$disabled = explode( ',', ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}


/**
 * Returns the file extension of a filename
 * 
 * @param       unknown $path (required) File name
 * @return 		mixed   File extension
 */

function maxson_portfolio_get_file_extension( $path )
{ 
	if( function_exists( 'pathinfo' ) )
	{ 
		$extension = pathinfo( $path, PATHINFO_EXTENSION );

	} else
	{ 
		$parts     = explode( '.', $path );
		$extension = end( $parts );

	} // endif

	return $extension;
}

?>