<?php
/**
 * Plugin-specific conditional functions
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


if( ! function_exists( 'is_portfolio' ) )
{ 
	/**
	 * Returns true if on a page that uses plugin-specific post type
	 * 
	 * @return 		bool
	 */

	function is_portfolio()
	{ 
		return (bool) ( is_portfolio_archive() || is_project() ) ? true : false;
	}
} // endif


if( ! function_exists( 'is_portfolio_archive' ) )
{ 
	/**
	 * Returns true when viewing the plugin-specific type archive.
	 * 
	 * @return 		bool
	 */

	function is_portfolio_archive()
	{ 
		return (bool) ( maxson_portfolio_is_archive_page() || is_post_type_archive( 'portfolio_project' ) || is_portfolio_taxonomy() ) ? true : false;
	}
} // endif


if( ! function_exists( 'is_portfolio_taxonomy' ) )
{ 
	/**
	 * Returns true when viewing a plugin-specific taxonomy archive.
	 * 
	 * @return 		bool
	 */

	function is_portfolio_taxonomy()
	{ 
		return (bool) is_tax( get_object_taxonomies( array( 'portfolio_project' ) ) );
	}
} // endif


if( ! function_exists( 'is_project_category' ) )
{ 
	/**
	 * Returns true when viewing a plugin-specific category.
	 * 
	 * @param 		string $term The term slug your checking for. Leave blank to return true on any
	 * @return 		bool
	 */

	function is_project_category( $term = '' )
	{ 
		return (bool) is_tax( 'portfolio_project_category', $term );
	}
} // endif


if( ! function_exists( 'is_project_tag' ) )
{ 
	/**
	 * Returns true when viewing a plugin-specific tag.
	 * 
	 * @param 		string $term The term slug your checking for. Leave blank to return true on any
	 * @return 		bool
	 */

	function is_project_tag( $term = '' )
	{ 
		return (bool) is_tax( 'portfolio_project_tag', $term );
	}
} // endif


if( ! function_exists( 'is_project_role' ) )
{ 
	/**
	 * Returns true when viewing a plugin-specific role.
	 * 
	 * @param 		string $term The term slug your checking for. Leave blank to return true on any
	 * @return 		bool
	 */

	function is_project_role( $term = '' )
	{ 
		return (bool) is_tax( 'portfolio_project_role', $term );
	}
} // endif


if( ! function_exists( 'is_project' ) )
{ 
	/**
	 * Returns true when viewing a single plugin-specific post.
	 * 
	 * @return 		bool
	 */

	function is_project()
	{ 
		return (bool) is_singular( 'portfolio_project' );
	}
} // endif


/**
 * Return if plugin-specific taxonomy is active
 * 
 * @return      bool
 */

function maxson_portfolio_taxonomy_exists( $type = 'category' )
{ 
	$taxonomies = maxson_portfolio_get_taxonomy_types();

	if( ! empty( $taxonomies ) )
	{ 
		$tax_allowed = array_key_exists( $type, $taxonomies );
		$tax_exists  = taxonomy_exists( "portfolio_{$type}" );

		return ( $tax_allowed && $tax_exists ) ? true : false;

	} else
	{ 
		return false;

	} // endif
}


if( ! function_exists( 'maxson_portfolio_is_request' ) )
{ 
	/**
	 * Returns true when the request is acurate.
	 * 
	 * @param 		string          $type  (required) Request type to determine
	 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
	 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
	 * @return 		bool
	 */

	function maxson_portfolio_is_request( $type = null, $true = true, $false = false )
	{ 
		if( ! is_null( $type ) )
		{ 
			switch( strtolower( $type ) )
			{ 
				case 'ajax': 
					if( defined( 'DOING_AJAX' ) )
					{
						return $true;

					} // endif

					if( isset( $_SERVER['HTTP_X_POSTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_POSTED_WITH'] ) == 'xmlhttprequest' ) 
					{
						return $true;

					} // endif

					return $false;
					break;

				case 'api': 
				case 'rest': 
					return ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ? $true : $false;
					break;

				case 'json': 
					return ( defined( 'JSON_REQUEST' ) && JSON_REQUEST ) ? $true : $false;
					break;

				default: 
					return $false;
					break;

			} // endswitch
		} else
		{ 
			return $false;

		} // endif
	}
} // endif


if( ! function_exists( 'maxson_portfolio_is_ajax_request' ) )
{ 
	/**
	 * Returns true when the page is loaded via ajax.
	 * 
	 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
	 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
	 * @return 		bool
	 */

	function maxson_portfolio_is_ajax_request( $true = true, $false = false )
	{ 
		return maxson_portfolio_is_request( 'ajax', $true, $false );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_is_json_request' ) )
{ 
	/**
	 * Returns true when the page is performing JSON request.
	 * 
	 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
	 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
	 * @return 		bool
	 */

	function maxson_portfolio_is_json_request( $true = true, $false = false )
	{ 
		return maxson_portfolio_is_request( 'json', $true, $false );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_is_rest_request' ) )
{ 
	/**
	 * Returns true when the page is performing REST API request.
	 * 
	 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
	 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
	 * @return 		bool
	 */

	function maxson_portfolio_is_rest_request( $true = true, $false = false )
	{ 
		return maxson_portfolio_is_request( 'rest', $true, $false );
	}
} // endif


/**
 * Check if the home URL is https
 * 
 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
 * @return 		bool
 */

function maxson_portfolio_site_is_https( $true = true, $false = false )
{ 
	$is_ssl = ( strstr( get_option( 'home' ), 'https:' ) ) ? $true : $false;

	return apply_filters( 'maxson_portfolio_site_is_https', $is_ssl );
}


/**
 * Check if the home URL is a test environment
 * 
 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
 * @return      bool
 */

function maxson_portfolio_site_is_test_environment( $true = true, $false = false )
{ 
//	$server_name = strtolower( $_SERVER['SERVER_NAME'] );
	$site_url = network_site_url( '/' );

	if( stristr( $site_url, 'dev'       ) !== false || 
		stristr( $site_url, 'stg'       ) !== false || 
		stristr( $site_url, '127.0.0.1' ) !== false || 
		stristr( $site_url, 'localhost' ) !== false || 
		stristr( $site_url, ':8888'     ) !== false )
	{ 
		$is_dev = $true;

	} else
	{ 
		$is_dev = $false;

	} // endif

	return apply_filters( 'maxson_portfolio_site_is_test_environment', $is_dev );
}


/**
 * Determine if debug mode is active
 * 
 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
 * @return 		bool
 */

function maxson_portfolio_in_debug_mode( $true = true, $false = false )
{ 
	if( defined( 'MAXSON_PORTFOLIO_DEBUG' ) )
	{ 
		$debug = ( true === MAXSON_PORTFOLIO_DEBUG ) ? true : false;

	} else
	{ 
		$debug = maxson_portfolio_get_option( 'debug', 'site' );

	} // endif

	$debug_mode = ( true == $debug ) ? $true : $false;

	return apply_filters( 'maxson_portfolio_in_debug_mode', $debug_mode, $true, $false );
}


/**
 * Determine if template debug mode is active
 * 
 * @param 		string|int|bool $true  (optional) Value to return if condition is "true"
 * @param 		string|int|bool $false (optional) Value to return if condition is "false"
 * @return 		bool
 */

function maxson_portfolio_in_template_debug_mode( $true = true, $false = false )
{ 
	if( defined( 'MAXSON_PORTFOLIO_DEBUG_TEMPLATE' ) )
	{ 
		$debug = ( MAXSON_PORTFOLIO_DEBUG_TEMPLATE ) ? true : false;

	} else
	{ 
		$debug = maxson_portfolio_get_option( 'debug', 'template_debug' );

	} // endif

	$debug_mode = ( $debug ) ? $true : $false;

	return apply_filters( 'maxson_portfolio_in_template_debug_mode', $debug_mode, $true, $false );
}

?>