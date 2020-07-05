<?php
/**
 * Admin Notices
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Notices' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Notices { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		private static $notice_successes = array();
		private static $notice_warnings  = array();
		private static $notice_errors    = array();


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'admin_print_styles', array( &$this, 'add_notices' ) );

			// Error handling (for showing errors from meta boxes on next page load)
			add_action( 'admin_notices', array( &$this, 'output_notices' ) );
			add_action( 'shutdown', array( &$this, 'save_notices' ) );
		}


		/**
		 * Add an warning message
		 * 
		 * @param       string $text
		 * @return      void
		 */

		public static function add_success( $text = '' )
		{ 
			self::$notice_successes[] = $text;
		}


		/**
		 * Add an warning message
		 * 
		 * @param       string $text
		 * @return      void
		 */

		public static function add_warning( $text = '' )
		{ 
			self::$notice_warnings[] = $text;
		}


		/**
		 * Add an error message
		 * 
		 * @param       string $text
		 * @return      void
		 */

		public static function add_error( $text = '' )
		{ 
			self::$notice_errors[] = $text;
		}


		/**
		 * Save errors to an option
		 * 
		 * @return      void
		 */

		public function save_notices()
		{
			update_option( 'maxson_portfolio_admin_notice_successes', self::$notice_successes );
			update_option( 'maxson_portfolio_admin_notice_warnings', self::$notice_warnings );
			update_option( 'maxson_portfolio_admin_notice_errors', self::$notice_errors );
		}


		/**
		 * Show any stored notices.
		 * 
		 * @return      void
		 */

		public function output_notices()
		{ 
			$successes = maybe_unserialize( get_option( 'maxson_portfolio_admin_notice_successes' ) );
			$warnings  = maybe_unserialize( get_option( 'maxson_portfolio_admin_notice_warnings' ) );
			$errors    = maybe_unserialize( get_option( 'maxson_portfolio_admin_notice_errors' ) );

			if( ! empty( $successes ) )
			{ 
				include( MAXSON_PORTFOLIO_DIRNAME . '/includes/admin/views/html-admin-notice-post-successes.php' );

				delete_option( 'maxson_portfolio_admin_notice_successes' );

			} // endif


			if( ! empty( $warnings ) )
			{ 
				include( MAXSON_PORTFOLIO_DIRNAME . '/includes/admin/views/html-admin-notice-post-warnings.php' );

				delete_option( 'maxson_portfolio_admin_notice_warnings' );

			} // endif


			if( ! empty( $errors ) )
			{ 
				include( MAXSON_PORTFOLIO_DIRNAME . '/includes/admin/views/html-admin-notice-post-errors.php' );

				delete_option( 'maxson_portfolio_admin_notice_errors' );

			} // endif
		}


		/**
		 * 
		 * 
		 * @return      void
		 */

		public function add_notices()
		{ 
			global $pagenow;

			$screen = get_current_screen();

			$installed_pages = get_option( 'maxson_portfolio_install_pages_notice' );

			if( in_array( $screen->id, maxson_portfolio_get_admin_screen_ids() ) && ( false === $installed_pages ) )
			{ 
				add_action( 'admin_notices', array( $this, 'install_pages' ) );

			} // endif
		}


		/**
		 * 
		 * 
		 * @return      void
		 */

		public function install_pages() 
		{ 
			include( MAXSON_PORTFOLIO_DIRNAME . '/includes/admin/views/html-admin-notice-install-pages.php' );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Notices();

?>