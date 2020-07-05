<?php
/**
 * Plugin-specific Admin Menus
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Menus' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Menus { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'admin_head', array( &$this, 'menu_highlight' ) );

			add_action( 'admin_menu', array( &$this, 'settings_menu' ) );
			add_action( 'admin_menu', array( &$this, 'tools_menu' ) );

			add_action( 'admin_menu', array( &$this, 'sanitize_settings' ) );
		}


		/**
		 * Highlights the correct top level admin menu item
		 * 
		 * @return      void
		 */

		public static function menu_highlight()
		{ 
			global $parent_file, $submenu_file, $post_type;

			if( in_array( $post_type, array( self::POST_TYPE ) ) )
			{ 
				$screen = get_current_screen();

				$taxonomies = array( 'portfolio_type' );

				if( in_array( $screen->taxonomy, $taxonomies ) )
				{ 
					$url = add_query_arg( array( 
						'post_type' => self::POST_TYPE
					), 'edit.php' );

					$parent_file = $submenu_file = $url;

				} // endif
			} // endif
		}


		/**
		 * Sanitize setting on save
		 */

		public static function sanitize_settings()
		{ 
			add_filter( 'pre_update_option_maxson_portfolio_archive_page_id', array( 'Maxson_Portfolio_Projects_Admin_Settings', 'update_maxson_portfolio_archive_page_id' ), 10, 2 );
		}


		/**
		 * Add menu item
		 */

		public static function settings_menu()
		{ 
			$menu_label    = __( 'Settings', 'maxson' );
			$menu_title    = __( 'Portfolio Settings', 'maxson' );
			$menu_location = 'edit.php?post_type=' . self::POST_TYPE;

			add_submenu_page( $menu_location, $menu_title, $menu_label, 'manage_options', 
				'portfolio_settings', 'maxson_portfolio_settings_page' );
		}


		/**
		 * Add menu item
		 */

		public static function tools_menu()
		{ 
			$menu_label    = __( 'Tools', 'maxson' );
			$menu_title    = __( 'Portfolio Tools', 'maxson' );
			$menu_location = 'edit.php?post_type=' . self::POST_TYPE;

			add_submenu_page( $menu_location, $menu_title, $menu_label, 'manage_options', 
				'portfolio_tools', 'maxson_portfolio_tools_page' );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Menus();

?>