<?php 
/**
 * Plugin-specific admin functions
 * 
 * @author      Thomas Maxson
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'maxson_portfolio_get_actions' ) )
{ 
	/**
	 * Plugin-specific actions hooks
	 * 
	 * @return      void
	*/

	function maxson_portfolio_get_actions()
	{ 
		$action = filter_input( INPUT_GET, 'maxson_portfolio_action' );

		if( isset( $action ) )
		{ 
			do_action( "maxson_portfolio_{$action}", $_GET );

		} // endif
	}
} // endif
add_action( 'init', 'maxson_portfolio_get_actions' );


if( ! function_exists( 'maxson_portfolio_post_actions' ) )
{ 
	/**
	 * Plugin-specific actions hooks
	 * 
	 * @return      void
	*/

	function maxson_portfolio_post_actions()
	{ 
		$action = filter_input( INPUT_POST, 'maxson_portfolio_action' );

		if( ! empty( $action ) )
		{ 
			do_action( "maxson_portfolio_{$action}", $_POST );

		} // endif
	}
} // endif
add_action( 'init', 'maxson_portfolio_post_actions' );


/**
 * Get plugin-specific screen ids
 * 
 * @return      array
 */

function maxson_portfolio_get_admin_screen_ids()
{ 
	$all_taxonomies = get_object_taxonomies( array( 'portfolio_project' ) );

	$taxonomies = array();

	foreach( $all_taxonomies as $taxonomy )
	{ 
		$taxonomies[] = "edit-{$taxonomy}";

	} // endforeach

	$page_ids = array_merge( array( 
		'portfolio_project_page_portfolio_tools', 
		'portfolio_project', 
		'edit-portfolio_project', 
		'profile', 'user-edit'
	), $taxonomies );

	return apply_filters( 'maxson_portfolio_admin_screen_ids', $page_ids );
}


/**
 * Delete all transients
 * 
 * @return      bool
 */

function maxson_portfolio_delete_transients( $transients = null )
{ 
	if( ! is_null( $transients ) && ! empty( $transients ) && is_array( $transients ) )
	{ 
		global $wpdb;

		$transient_names = array();

		foreach( $transients as $transient )
		{ 
			$transient_names[] = "_transient_maxson_portfolio_{$transient}";
			$transient_names[] = "_transient_timeout_maxson_portfolio_{$transient}";

		} // endforeach

		$transient_name_query = implode( ', ', array_fill( 0, count( $transient_names ), '%s' ) );

		$delete_sql = $wpdb->prepare( "DELETE FROM {$wpdb->options} 
			WHERE option_name IN ($transient_name_query)", 
		$transient_names );

		$result = $wpdb->query( $delete_sql );

		return true;

	} else
	{ 
		return false;

	} // endif
}


/**
 * Get all plugin-specific transients
 * 
 * @return      bool
 */

function maxson_portfolio_get_transients( $type = null, $older_than = '1 minute' )
{ 
	if( is_null( $type ) )
		return false;

	global $wpdb;

	switch( $type )
	{ 
		case 'expired': 
			$query_time = strtotime( "-{$older_than}" );

			if( $query_time > time() || $query_time < 1 )
			{
				return false;

			} // endif

			$query = $wpdb->prepare( "SELECT REPLACE( option_name, '_transient_timeout_maxson_portfolio_', '' ) 
				AS transient_name FROM {$wpdb->options} 
				WHERE option_name LIKE '\_transient\_timeout\_maxson\_portfolio\__%%' 
				AND option_value < %d
			", $query_time );
			break;


		default: 
			$query = "SELECT REPLACE( option_name, '_transient_timeout_maxson_portfolio_', '' ) 
				AS transient_name FROM {$wpdb->options} 
				WHERE option_name LIKE '\_transient\_timeout\_maxson\_portfolio\__%%'";
			break;

	} // endswitch

	$sql = $wpdb->get_col( $query );

	return $sql;
}


/**
 * Get referer URL
 * 
 * @return      string
 */

function maxson_portfolio_get_referer()
{ 
	$wp_referer = wp_get_referer();

	return ( ! empty( $wp_referer ) ) ? $wp_referer : $_SERVER['HTTP_REFERER'];
}


if( ! function_exists( 'maxson_portfolio_object_to_array' ) )
{ 
	/**
	 * Convert object to array
	 * 
	 * @return      array
	 */

	function maxson_portfolio_object_to_array( $object )
	{ 
		if( ! is_object( $object ) && ! is_array( $object ) )
			return $object;

		return array_map( 'maxson_portfolio_object_to_array', (array) $object );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_sort_array' ) )
{ 
	/**
	 * Sort array by priority
	 * 
	 * @return      array
	 */

	function maxson_portfolio_sort_array( $a, $b )
	{ 
		if( $a == $b )
		{ 
			return 0;

		} // endif

		return ( $a < $b ) ? -1 : 1;
	}
} // endif


if( ! function_exists( 'date_format_php_to_js' ) )
{ 
	/**
	 * Convert the php date format string to a js date format
	 * @see         http://snipplr.com/view/41329/convert-php-date-style-dateformat-to-the-equivalent-jquery-ui-datepicker-string/
	 * 
	 * @return      string Formatted date
	 */

	function date_format_php_to_js( $format )
	{ 
		switch( $format )
		{ 
			case 'F j, Y': 
				return 'MM d, yy';
				break;

			case 'Y-m-d': 
				return 'yy-mm-dd';
				break;

			case 'm/d/Y': 
				return 'mm/dd/yy';
				break;

			case 'd/m/Y': 
				return 'dd/mm/yy';
				break;

			default: 
				$pattern = array( 
					'd', 'j', 'l', 'z', // Day format
					'F', 'M', 'n', 'm', // Month format
					'Y', 'y'            // Year format
				);

				$replace = array( 
					'dd', 'd', 'DD', 'o', // Day format
					'MM', 'M', 'm', 'mm', // Month format
					'yy', 'y'             // Year format
				);

				foreach( $pattern as &$p )
				{
					$p = '/' . $p . '/';

				} // endforeach

				return preg_replace( $pattern, $replace, $format );
				break;

		} // endswitch
	}
} // endif


if( ! function_exists( 'time_format_php_to_js' ) )
{ 
	/**
	 * Convert the php time format string to a js time format
	 * @see         http://trentrichardson.com/examples/timepicker/
	 * 
	 * @return      string Formatted time
	 */

	function time_format_php_to_js( $format )
	{ 
		switch( $format )
		{ 
			case 'g:i a': 
				return 'h:m tt';
				break;

			case 'g:i A': 
				return 'h:m TT';
				break;

			case 'H:i': 
				return 'H:m';
				break;

			default: 
				$pattern = array( 
					'g', 'G', 'h', 'H', // Hour format
					'i',                // Minute format
					's',                // Second format
					'u',                // Microseconds format
					'a', 'A'            // Meridiem format
				);

				$replace = array( 
					'h', 'h', 'h', 'H', // Hour format
					'mm',                // Minute format
					'ss',               // Second format
					'c',                // Microseconds format
					'tt', 'TT',         // Meridiem format
				);

				foreach( $pattern as &$p )
				{
					$p = '/' . $p . '/';

				} // endforeach

				return preg_replace( $pattern, $replace, $format );
				break;

		} // endswitch
	}
} // endif

?>