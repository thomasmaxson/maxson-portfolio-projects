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
			add_action( 'admin_menu', array( &$this, 'sanitize_settings' ) );

			add_action( 'admin_menu', array( &$this, 'tools_menu' ) );
		//	add_action( 'admin_menu', array( &$this, 'extensions_menu' ) );
		}


		/**
		 * Highlights the correct top level admin menu item
		 * 
		 * @return      void
		 */

		public static function menu_highlight()
		{ 
			global $parent_file, $submenu_file;

			if( in_array( self::POST_TYPE, array( self::POST_TYPE ) )  )
			{ 
				$screen = get_current_screen();

			//	$taxonomies = get_object_taxonomies( self::POST_TYPE );
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

		public static function tools_menu()
		{ 
			$capability = apply_filters( 'maxson_portfolio_tools_menu_capability', 'manage_portfolio_tools' );

			if( current_user_can( 'manage_portfolio_tools' ) )
			{ 
				$tools_page = add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE, __( 'Portfolio Tools', 'maxson' ), __( 'Tools', 'maxson' ), 
						$capability, 'portfolio_tools', 'maxson_portfolio_tools_page' );

			} // endif
		}


		/**
		 * Add menu item
		 */

		public static function extensions_menu()
		{ 
			$capability = apply_filters( 'maxson_portfolio_extensions_menu_capability', 'manage_options' );

			if( current_user_can( 'manage_portfolio_extensions' ) )
			{ 
				$extensions_page = add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE, __( 'Portfolio Extensions', 'maxson' ), __( 'Extensions', 'maxson' ), 
						$capability, 'portfolio_extensions', 'maxson_portfolio_extensions_page' );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Menus();

?>