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


		/**
		 * Array of notices
		 */

		private static $notices = array();


		/**
		 * Array of core notices
		 */

		private static $core_notices = array( 
			'install' => 'install_pages'
		);


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			// Error handling (for showing errors from meta boxes on next page load)
			add_action( 'admin_print_styles', array( &$this, 'output_notices' ) );
			add_action( 'shutdown', array( &$this, 'save_custom_notices' ) );
		}


		/**
		 * Get notices
		 * 
		 * @return      array
		 */

		public static function get_notices()
		{ 
			return self::$notices;
		}


		/**
		 * See if a notice is being shown
		 * 
		 * @param  string  $name
		 * @return boolean
		 */

		public static function has_notice( $key )
		{ 
			return array_key_exists( $key, self::get_notices() );
		}


		/**
		 * Remove a notice from being displayed
		 * 
		 * @param  string $name
		 */

		public static function remove_notice( $key )
		{ 
			$notices = self::$notices;

			if( self::has_notice( $key ) )
			{
				unset( $notices[ $key ] );

			} // endif

			self::$notices = $notices;
		}


		/**
		 * Remove all notices
		 * 
		 * @return      array
		 */

		public static function remove_all_notices()
		{ 
			self::$notices = array();
		}


		/**
		 * Add a notice for output
		 * 
		 * @param       string (required) $type           Type of notification
		 * @param       string (required) $key            Key for notice array. Helps prevent duplicates
		 * @param       string (required) $message        Notice message
		 * @param       string (required) $is_dismissable Make notice dismissable
		 * @return      void
		 */

		public static function add_custom_notice( $type = null, $key = null, $message = '', $is_dismissable = false )
		{ 
			if( is_null( $type ) || ! in_array( $type, array( 'success', 'warning', 'error' ) ) )
			{
				return false;

			} // endif

			if( is_null( $key ) || empty( $message ) )
			{
				return false;

			} // endif

			$notices = self::get_notices();

			if( ! self::has_notice( $key ) )
			{ 
				$notices[ $key ] = array( 
					'type'    => $type, 
					'message' => $message, 
					'is_dismissable' => $is_dismissable
				);

			} else
			{ 
				// TODO: Pick from existing array

			} // endif

			self::$notices = $notices;
		}


		/**
		 * Add success notice for output
		 * 
		 * @param       string (required) $key            Key for notice array. Helps prevent duplicates
		 * @param       string (required) $message        Notice message
		 * @param       string (required) $is_dismissable Make notice dismissable
		 * @return      void
		 */

		public static function add_success( $key = null, $message = '', $is_dismissable = false )
		{ 
			self::add_custom_notice( 'success', $key, $message, $is_dismissable );
		}


		/**
		 * Add warning notice for output
		 * 
		 * @param       string (required) $key            Key for notice array. Helps prevent duplicates
		 * @param       string (required) $message        Notice message
		 * @param       string (required) $is_dismissable Make notice dismissable
		 * @return      void
		 */

		public static function add_warning( $key = null, $message = '', $is_dismissable = false )
		{ 
			self::add_custom_notice( 'warning', $key, $message, $is_dismissable );
		}


		/**
		 * Add error notice for output
		 * 
		 * @param       string (required) $key            Key for notice array. Helps prevent duplicates
		 * @param       string (required) $message        Notice message
		 * @param       string (required) $is_dismissable Make notice dismissable
		 * @return      void
		 */

		public static function add_error( $key = null, $message = '', $is_dismissable = false )
		{ 
			self::add_custom_notice( 'error', $key, $message, $is_dismissable );
		}


		/**
		 * Save errors to an option
		 * 
		 * @return      void
		 */

		public function save_custom_notices()
		{ 
			if( ! empty( self::$notices ) )
			{
				update_option( 'maxson_portfolio_admin_notices', self::$notices );

			} // endif
		}


		/**
		 * Show any stored notices.
		 * 
		 * @return      void
		 */

		public function output_notices()
		{ 
			$notices = maybe_unserialize( get_option( 'maxson_portfolio_admin_notices' ) );

			if( ! empty( $notices ) )
			{ 
				foreach( $notices as $key => $data )
				{ 
					$dissmissable_class = ( $data['is_dismissable'] ) ? 'is-dismissible' : 'is-not-dismissible';
					$message_html = wpautop( wp_kses_post( $data['message'] ) );

					printf( '<div id="%1$s" class="maxson-portfolio-message %2$s notice-%2$s notice %3$s">%4$s</div>', $key, $data['type'], $dissmissable_class, $message_html );

				} // endforeach

				delete_option( 'maxson_portfolio_admin_notices' );

			} elseif( apply_filters( 'maxson_portfolio_show_admin_core_notices', true ) )
			{ 
				foreach( self::$core_notices as $key => $callback )
				{ 
					add_action( 'admin_notices', array( __CLASS__, $callback ) );

				} // endforeach
			} // endif
		}


		/**
		 * If missing page(s), show a message with the install buttons
		 * 
		 * @return      void
		 */

		public static function install_pages() 
		{ 
			$screen = get_current_screen();

			$installed_pages = get_option( 'maxson_portfolio_install_pages_notice' );

			if( ( false == $installed_pages ) && 
				in_array( $screen->id, maxson_portfolio_get_admin_screen_ids() ) )
			{ 
				include( MAXSON_PORTFOLIO_DIRNAME . '/includes/admin/views/html-admin-notice-install-pages.php' );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Notices();

?>