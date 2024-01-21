<?php
/**
 * Plugin-specific installation related functions and actions
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Install' ) )
{ 
	class Maxson_Portfolio_Projects_Install {

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'init', array( &$this, 'support_jetpack_omnisearch' ) );

			add_action( 'trashed_post', array( &$this, 'trashed_archive_page' ) );
		}


		/**
		 * Install plugin
		 * 
		 * @return      void
		 */

		public static function install()
		{ 
			self::create_options();

			$current_version = get_option( 'maxson_portfolio_version' );

			if( $current_version )
			{ 
				update_option( 'maxson_portfolio_version_prev', $current_version );

			} // endif

			do_action( 'maxson_portfolio_installed' );

			flush_rewrite_rules();
		}


		/**
		 * Uninstall plugin
		 * 
		 * @return      void
		 */

		public static function uninstall()
		{ 
			if( false === maxson_portfolio_get_archive_page_id() )
			{ 
				delete_option( 'maxson_portfolio_install_pages_notice' );

			} // endif
		}


		/**
		 * Setup plugin-specific default option(s)
		 * 
		 * @return      void
		 */

		private static function create_options()
		{ 
			// Global
			add_option( 'maxson_portfolio_version', MAXSON_PORTFOLIO_VERSION );
			add_option( 'maxson_portfolio_version_prev', false );

			add_option( 'maxson_portfolio_install_pages_notice', false );


			// Debug
			$debug_site     = ( defined( 'MAXSON_PORTFOLIO_DEBUG' ) ) ? MAXSON_PORTFOLIO_DEBUG : false;
			$debug_template = ( defined( 'MAXSON_PORTFOLIO_DEBUG_TEMPLATE' ) ) ? MAXSON_PORTFOLIO_DEBUG_TEMPLATE : false;

			add_option( 'maxson_portfolio_debug_site', $debug_site );
			add_option( 'maxson_portfolio_debug_template', $debug_template );


			// Media
			$default_thumbnail_w = get_option( 'thumbnail_size_width' );
			$default_thumbnail_h = get_option( 'thumbnail_size_height' );
			$default_thumbnail_c = get_option( 'thumbnail_crop' );

			add_option( 'maxson_portfolio_media_size_thumbnail_width',  $default_thumbnail_w );
			add_option( 'maxson_portfolio_media_size_thumbnail_height', $default_thumbnail_h );
			add_option( 'maxson_portfolio_media_thumbnail_crop',   $default_thumbnail_c );

			$default_medium_w = get_option( 'medium_size_width' );
			$default_medium_h = get_option( 'medium_size_height' );
			$default_medium_c = get_option( 'medium_crop' );

			add_option( 'maxson_portfolio_media_size_medium_width',  $default_medium_w );
			add_option( 'maxson_portfolio_media_size_medium_height', $default_medium_h );
			add_option( 'maxson_portfolio_media_medium_crop',   $default_medium_c );

			$default_large_w = get_option( 'large_size_width' );
			$default_large_h = get_option( 'large_size_height' );
			$default_large_c = get_option( 'large_crop' );

			add_option( 'maxson_portfolio_media_size_large_width',  $default_large_w );
			add_option( 'maxson_portfolio_media_size_large_height', $default_large_h );
			add_option( 'maxson_portfolio_media_large_crop',   $default_large_c );


			// Permalinks
			$default_permalink_category = _x( 'project-category', 'Taxonomy default permalink slug', 'maxson' );
			$default_permalink_role     = _x( 'project-role', 'Taxonomy default permalink slug', 'maxson' );
			$default_permalink_tag      = _x( 'project-tag', 'Taxonomy default permalink slug', 'maxson' );
			$default_permalink_type     = _x( 'project-type', 'Taxonomy default permalink slug', 'maxson' );

			add_option( 'maxson_portfolio_permalink_project',  false );
			add_option( 'maxson_portfolio_permalink_category', $default_permalink_category );
			add_option( 'maxson_portfolio_permalink_role',     $default_permalink_role );
			add_option( 'maxson_portfolio_permalink_tag',      $default_permalink_tag );
			add_option( 'maxson_portfolio_permalink_type',     $default_permalink_type );

			flush_rewrite_rules();
		}


		/**
		 * Add custom post-type support to Jetpack Omnisearch
		 * 
		 * @return      void
		 */

		public function support_jetpack_omnisearch()
		{ 
			if( class_exists( 'Jetpack_Omnisearch_Posts' ) )
			{ 
				new Jetpack_Omnisearch_Posts( self::POST_TYPE );

			} // endif
		}


		/**
		 * When a page is trashed, check if is used by plugin
		 * 
		 * @return      void
		 */

		public function trashed_archive_page( $post_id )
		{ 
			if( 'page' == get_post_type( $post_id ) )
			{ 
				if( $post_id == maxson_portfolio_get_archive_page_id() )
				{ 
					delete_option( 'maxson_portfolio_archive_page_id' );
					delete_option( 'maxson_portfolio_install_pages_notice' );

				} // endif
			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Install();

?>