<?php
/**
 * Plugin-specific query functions
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
 * When the_post is called, put project data into global var
 *
 * @param       mixed $post
 * @return      Project data
 */

function maxson_portfolio_setup_settings_project_data( $post_object )
{ 
	unset( $GLOBALS['project'] );

	if( is_int( $post_object ) )
	{
		$post_object = get_post( $post_object );

	} // endif

	if( empty( $post_object->post_type ) || 
		! in_array( $post_object->post_type, array( 'portfolio_project' ) ) )
	{ 
		return;

	} // endif

	$GLOBALS['project'] = maxson_portfolio_get_project( $post_object );

	return $GLOBALS['project'];
}
add_action( 'the_post', 'maxson_portfolio_setup_settings_project_data' );


/**
 * Main function for returning projects, uses the Maxson_Portfolio_Projects_Factory class
 * 
 * @param       mixed $the_project Post object or post ID of the package
 * @param       array $args        Contains all arguments to be used to get this product
 * @return      BGP_Package
 */

function maxson_portfolio_get_project( $the_project = false, $args = array() )
{ 
	return Portfolio_Projects()->project_factory->get_project( $the_project, $args );
}


if( ! function_exists( 'maxson_portfolio_query_args' ) )
{ 
	/**
	 * Main function for returning plugin-specific posts
	 * 
	 * @since       1.0
	 * 
	 * @param       $args array An array of arguments
	 * @return      array
	 */

	function maxson_portfolio_query_args( $args = array() )
	{ 
		$defaults = array( 
			'post_status'    => array( 'publish' ), 
			'posts_per_page' => maxson_portfolio_get_archive_limit(), 
			'orderby'        => maxson_portfolio_get_option( 'archive_orderby', 'DESC' ), 
			'order'          => maxson_portfolio_get_option( 'archive_order', 'post_date' ), 
		//	'meta_query'     => array(), 
		//	'tax_query'      => array()
		);

		$args = wp_parse_args( $args, $defaults );

		// Cannot override, sorry
		$args['post_type'] = 'portfolio_project';


		if( isset( $args['orderby_meta_key'] ) && ! empty( $args['orderby_meta_key'] ) )
		{ 
			$args['meta_key'] = $args['orderby_meta_key'];

			unset( $args['orderby_meta_key'] );

		} // endif


		if( isset( $args['in'] ) && ! empty( $args['in'] ) )
		{ 
			if( ! is_array( $args['in'] ) )
			{
				$args['in'] = array( $args['in'] );

			} // endif

			$args['post__in'] = $args['in'];

			unset( $args['in'] );

		} // endif


		if( isset( $args['not_in'] ) && ! empty( $args['not_in'] ) )
		{ 
			if( ! is_array( $args['not_in'] ) )
			{
				$args['not_in'] = array( $args['not_in'] );

			} // endif

			$args['post__not_in'] = $args['not_in'];

			unset( $args['not_in'] );

		} // endif


		$taxonomies = maxson_portfolio_get_taxonomy_types();

		if( ! empty( $taxonomies ) )
		{ 
			foreach( $taxonomies as $key => $label )
			{ 
				if( isset( $args[ $key ] ) && ! empty( $args[ $key ] ) )
				{ 
					$taxonomy = "portfolio_{$key}";
					$field = ( isset( $args["{$key}_field"] ) ) ? $args["{$key}_field"] : 'slug';

					if( ! is_array( $args[ $key ] ) )
						$args[ $key ] = array( $args[ $key ] );

					$tax_query = array( 
						'taxonomy' => $taxonomy, 
						'field'    => $field, 
						'terms'    => $args[ $key ]
					);

					$args['tax_query'][] = $tax_query;

					unset( $args[ $key ] );

				} // endif
			} // endforeach
		} // endif


		if( isset( $args['promoted'] ) && 
			true == filter_var( $args['promoted'], FILTER_VALIDATE_BOOLEAN ) )
		{ 
			$meta_query = array( 
				'key' 	=> '_project_promoted', 
				'value' => true
			);

			$args['meta_query'][] = $meta_query;

			unset( $args['promoted'] );

		} // endif


		if( isset( $args['require_thumbnail'] ) && 
			true == filter_var( $args['require_thumbnail'], FILTER_VALIDATE_BOOLEAN ) )
		{ 
			$meta_query = array( 
				'key'     => '_thumbnail_id', 
				'compare' => 'EXISTS'
			);

			$args['meta_query'][] = $meta_query;

			unset( $args['require_thumbnail'] );

		} // endif

		return apply_filters( 'maxson_portfolio_query_args', $args );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_template_part' ) )
{ 
	/**
	 * Get template part (for templates)
	 * 
	 * @param       mixed  $slug (required) File slug (prefix)
	 * @param       string $name (optional) File name (type)
	 * @return      void
	 */

	function maxson_portfolio_template_part( $slug, $name = '' )
	{ 
		$template = '';
		$debug    = maxson_portfolio_in_debug_mode();

		$template_path = Portfolio_Projects()->template_path();
		$plugin_path   = MAXSON_PORTFOLIO_PLUGIN_PATH;

		// Look in yourtheme/slug-name.php and yourtheme/portfolio/slug-name.php
		if( $name && ! $debug )
		{ 
			$files = array( "{$slug}-{$name}.php", "{$template_path}/{$slug}-{$name}.php" );

			$template = locate_template( $files );

		} // endif


		// Get default slug-name.php
		if( ! $template && $name && file_exists( "{$plugin_path}/templates/{$slug}-{$name}.php" ) )
		{ 
			$template = "{$plugin_path}/templates/{$slug}-{$name}.php";

		} // endif


		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/templates/slug.php
		if( ! $template && ! $debug )
		{ 
			$files = array( "{$slug}.php", "{$template_path}/{$slug}.php", "{$plugin_path}/templates/{$slug}.php" );

			$template = locate_template( $files );

		} // endif


		if( ! $template && $debug || $template )
		{ 
			// Allow 3rd party plugin filter template file from their plugin
			$template = apply_filters( 'maxson_portfolio_template_part', $template, $slug, $name );

		} // endif

		load_template( $template, false );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_template' ) )
{ 
	/**
	 * Get other templates (e.g. widget) passing attributes and including the file.
	 * 
	 * @param       string $template_name
	 * @param       array  $args          (optional)
	 * @param       string $template_path (optional)
	 * @param       string $default_path  (optional)
	 * @return      void
	 */

	function maxson_portfolio_template( $template_name, $args = array(), $template_path = '', $default_path = '' )
	{ 
		extract( $args );

		$located = maxson_portfolio_locate_template( $template_name, $template_path, $default_path );

		if( ! file_exists( $located ) )
		{ 
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%1$s</code> does not exist.', $located ), '1.0' );
			return;

		} // endif

		// Allow 3rd party plugin filter template file from their plugin
		$located = apply_filters( 'maxson_portfolio_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'maxson_portfolio_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'maxson_portfolio_after_template_part', $template_name, $template_path, $located, $args );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_locate_template' ) )
{ 
	/**
	 * Locate a template and return the path for inclusion
	 * 
	 * This is the load order: 
	 * yourtheme / $template_path / $template_name
	 * yourtheme / $template_name
	 * $default_path / $template_name
	 * 
	 * @param       string $template_name
	 * @param       string $template_path (optional)
	 * @param       string $default_path  (optional)
	 * @return      string
	 */

	function maxson_portfolio_locate_template( $template_name, $template_path = '', $default_path = '' )
	{ 
		if( empty( $template_path ) )
		{
			$template_path = Portfolio_Projects()->template_path();

		} // endif
		
		if( empty( $default_path ) )
		{
			$default_path = MAXSON_PORTFOLIO_PLUGIN_PATH . '/templates/';

		} // endif

		// Look within passed path within the theme - this is priority
		$template = locate_template( array( 
			trailingslashit( $template_path ) . $template_name,
			$template_name
		) );

		if( ! $template || maxson_portfolio_in_template_debug_mode() )
		{
			$template = $default_path . $template_name;

		} // endif

		return apply_filters( 'maxson_portfolio_locate_template', $template, $template_name, $template_path );
	}
} // endif

?>