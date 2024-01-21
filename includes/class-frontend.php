<?php
/**
 * Plugin-specific template loader
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Frontend' ) )
{ 
	class Maxson_Portfolio_Projects_Frontend { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'template_redirect', array( &$this, 'template_redirect' ) );

			add_action( 'get_the_generator_html', array( &$this, 'generator_tag' ), 10, 2 );
			add_action( 'get_the_generator_xhtml', array( &$this, 'generator_tag' ), 10, 2 );

			add_filter( 'body_class', array( &$this, 'body_class' ) );
			add_filter( 'post_class', array( &$this, 'post_class' ), 20, 3 );

			add_filter( 'wp_nav_menu_objects', array( &$this, 'nav_menu_classes' ), 2, 20 );
		}


		/** 
		 * Handle archive template redirect
		 * 
		 * @return      void
		 */

		public function template_redirect()
		{ 
			global $wp_query;

			// When default permalinks are enabled, redirect portfolio page to post type archive url
			if( '' == get_option( 'permalink_structure' ) && ! empty( $_GET['page_id'] ) && $_GET['page_id'] == maxson_portfolio_get_archive_page_id() )
			{ 
				wp_safe_redirect( get_post_type_archive_link( self::POST_TYPE ) );
				exit();

			} // endif
		}
			

		/**
		 * Output generator tag to aid debugging
		 * 
		 * @param       string $gen  The generator meta tag
		 * @param       string $type The type of page to generate the tag for
		 * @return      void
		 */

		public function generator_tag( $gen, $type )
		{ 
			if( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$plugin_data = get_plugin_data( MAXSON_PORTFOLIO_FILE );
				$plugin_name = $plugin_data['Name'];

			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			$plugin_information = ( apply_filters( 'maxson_portfolio_generator_hide_version', false ) ? $plugin_name : "{$plugin_name} {$plugin_version}" );

			switch( $type )
			{ 
				case 'xhtml': 
					$gen .= "\n" . sprintf( '<meta name="generator" content="%1$s" />', esc_attr( $plugin_information ) );
					break;

				case 'html': 
				default: 
					$gen .= "\n" . sprintf( '<meta name="generator" content="%1$s">', esc_attr( $plugin_information ) );
					break;

			} // endswitch

			return $gen;
		}


		/**
		 * Add body classes for plugin-specific pages
		 * 
		 * @param       array $classes
		 * @return      array
		 */

		public function body_class( $classes )
		{ 
			if( is_portfolio() )
			{ 
				$exclude_classes = array();
				$include_classes = array();

				$classes[] = 'page-portfolio-project';

				if( is_portfolio_taxonomy() )
				{ 
					$term_obj = get_queried_object();

					$taxonomy  = $term_obj->taxonomy;
					$term_slug = $term_obj->slug;

					$exclude_classes[] = "tax-{$taxonomy}";
					$exclude_classes[] = "term-{$term_slug}";

					$include_classes[] = "taxonomy-{$taxonomy}-portfolio-project";
					$include_classes[] = "term-{$term_slug}-portfolio-project";

					$include_classes[] = "{$taxonomy}-portfolio";
					$include_classes[] = "{$term_slug}-portfolio";

				} elseif( maxson_portfolio_is_archive_page() || is_post_type_archive( self::POST_TYPE ) )
				{ 
					$exclude_classes[] = 'post-type-archive-portfolio_project';

					$include_classes[] = 'post-type-archive-portfolio';
					$include_classes[] = 'archive-portfolio';

				} elseif( is_project() )
				{ 
					$exclude_classes[] = 'single-portfolio_project';

					$include_classes[] = 'single-project';

				} // endif

				$classes = array_merge( $classes, apply_filters( 'maxson_portfolio_body_classes_include', $include_classes ) );

				$classes = array_diff( $classes, apply_filters( 'maxson_portfolio_body_classes_exclude', $exclude_classes ) );

				return apply_filters( 'maxson_portfolio_body_classes', array_unique( $classes ) );

			} else
			{ 
				return $classes;

			} // endif
		}


		/**
		 * Add extra post classes for plugin-specific posts
		 * 
		 * @param       array        $classes
		 * @param       string|array $class
		 * @param       int          $post_id
		 * @return      array
		 */

		public function post_class( $classes, $class = '', $post_id = '' )
		{ 
			if( ! $post_id || self::POST_TYPE !== get_post_type( $post_id ) )
			{ 
				return $classes;

			} // endif

			$teaser_count = get_theme_mod( 'maxson_project_teaser_column_count', '3' );

			$classes[] = apply_filters( 'maxson_portfolio_project_column_class', sanitize_html_class( "column-count-{$teaser_count}" ), $post_id );

			$project_type = str_replace( '_', '-', maxson_project_data_type( $post_id, 'slug' ) );

			$classes[] = sanitize_html_class( "project-type-{$project_type}" );


			if( maxson_project_data_has_callout( $post_id ) )
			{ 
				$classes[] = sanitize_html_class( 'project-has-callout' );

			} // endif


			$include = apply_filters( 'maxson_portfolio_post_classes_include', array(), $post_id );

			$classes = array_merge( $classes, $include );


			$exclude = apply_filters( 'maxson_portfolio_post_classes_exclude', array(), $post_id );
			$exclude[] = 'hentry';

			$classes = array_diff( $classes, $exclude );

			return apply_filters( 'maxson_portfolio_project_classes', array_unique( array_filter( $classes ) ), $post_id );
		}


		/**
		 * Fix active class in nav for portfolio page
		 * 
		 * @param       array $menu_items
		 * @param       array $args
		 * @return      array
		 */

		public function nav_menu_classes( $menu_items, $args )
		{ 
			if( ! is_portfolio() )
			{ 
				return $menu_items;

			} // endif

			$archive_page 	= (int) maxson_portfolio_get_archive_page_id();
			$page_for_posts = (int) get_option( 'page_for_posts' );

			foreach( (array) $menu_items as $key => $menu_item )
			{ 
				$classes = (array) $menu_item->classes;

				// Unset active class for home archive page
				if( $page_for_posts == $menu_item->object_id )
				{ 
					$menu_items[$key]->current = false;

					if( in_array( 'current_page_parent', $classes ) )
					{
						unset( $classes[array_search( 'current_page_parent', $classes )] );

					} // endif

					if( in_array( 'current-menu-item', $classes ) )
					{
						unset( $classes[array_search( 'current-menu-item', $classes )] );

					} // endif
				// Set active state if this is the archive page link
				} elseif( is_portfolio_archive() && $archive_page == $menu_item->object_id )
				{ 
					$menu_items[$key]->current = true;

					$classes[] = 'current-menu-item';
					$classes[] = 'current_page_item';

				// Set parent state if this is a archive page
				} elseif( is_project() && $archive_page == $menu_item->object_id )
				{ 
					$classes[] = 'current-menu-item';

				} // endif

				$menu_items[$key]->classes = array_unique( $classes );

			}

			return $menu_items;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Frontend();

?>