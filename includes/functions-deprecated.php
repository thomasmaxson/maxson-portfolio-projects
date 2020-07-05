<?php
/**
 * Plugin-specific deprecated functions
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
 * Marks a function as deprecated.
 * The current behavior is to trigger a user error if MAXSON_PORTFOLIO_DEBUG is true.
 * 
 * @param       string  $function    (required) The function that was called
 * @param       string  $version     (required) The version of WordPress that deprecated the function
 * @param       string  $replacement (optional) The function that should have been called
 * @return      void
 */

function maxson_portfolio_deprecated_function( $function, $version, $replacement = null )
{ 
	$show_errors = current_user_can( 'manage_options' );

	if( maxson_portfolio_in_debug_mode() 
		&& apply_filters( 'maxson_portfolio_show_deprecated_function_error', $show_errors ) )
	{ 
		if( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( MAXSON_PORTFOLIO_FILE );
			$plugin_name = $plugin_data['Name'];

		if( ! is_null( $replacement ) )
		{ 
			trigger_error( sprintf( __( '%1$s is deprecated in %2$s since version %1$s! Use %4$s instead.', 'maxson' ), $function, $plugin_name, $version, $replacement ) );

		} else
		{ 
			trigger_error( sprintf( __( '%1$s is deprecated in %2$s since version %3$s with no alternative available.', 'maxson' ), $function, $plugin_name, $version ) );

		} // endif
	} // endif
}


/**
 * @deprecated
 * 
 * @since       1.0
 */

function portfolio_show_messages()
{ 
	maxson_portfolio_deprecated_function( 'portfolio_show_messages', '2.0', 'portfolio_print_notices' );
}

?>