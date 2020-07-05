<?php
/**
 * Plugin-specific admin functions
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin' ) )
{ 
	class Maxson_Portfolio_Projects_Admin {

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_filter( 'plugin_action_links_' . MAXSON_PORTFOLIO_BASENAME, array( &$this, 'plugin_action_links' ) );

			add_action( 'admin_footer', 'maxson_portfolio_print_js', 25 );
			add_filter( 'admin_footer_text', array( &$this, 'admin_footer_text' ), 1 );
		}


		/**
		 * Change the admin footer text
		 * 
		 * @param       string $footer_text
		 * @return	    string
		 */

		public function admin_footer_text( $footer_text )
		{  
			global $typenow;

			if( $typenow == 'portfolio_project' && apply_filters( 'maxson_portfolio_display_admin_footer_text', true ) )
			{ 
				$footer_text = str_replace( '</span>', '', $footer_text );

				if( ! function_exists( 'get_plugin_data' ) )
				{
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				} // endif

				$plugin_data = get_plugin_data( MAXSON_PORTFOLIO_FILE );
					$plugin_name = sprintf( '<strong>%1$s</strong>', $plugin_data['Name'] );

				$plugin_text = sprintf( __( 'Thank you for using %1$s.', 'maxson' ), $plugin_name );

				return "{$footer_text} | {$plugin_text}" . '</span>';

			} // endif

			return $footer_text;
		}


		/**
		 * Show action links on the plugin screen.
		 * 
		 * @param	    mixed $links Plugin Action links
		 * @return	    array
		 */

		public function plugin_action_links( $links )
		{ 
			if( current_user_can( 'manage_options' ) )
			{ 
				$permalink_href = admin_url( 'options-permalink.php#maxson_portfolio_permalink_settings' );

				$permalink = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', esc_url( $permalink_href ), __( 'Change Portfolio Permalink Structure', 'maxson' ), __( 'Permalinks', 'maxson' ) );

			//	array_unshift( $links, $permalink );


				$media_href = admin_url( 'options-media.php' );

				$media = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', esc_url( $media_href ), __( 'Change Portfolio Media Sizes', 'maxson' ), __( 'Media', 'maxson' ) );

			//	array_unshift( $links, $media );

			} // endif


			if( current_user_can( 'manage_portfolio_tools' ) )
			{ 
				$tools_href = add_query_arg( array( 
					'post_type' => 'portfolio_project', 
					'page'      => 'portfolio_tools'
				), admin_url( 'edit.php' ) );

				$tools = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', esc_url( $tools_href ), __( 'Portfolio Tools', 'maxson' ), __( 'Tools', 'maxson' ) );

				array_unshift( $links, $tools );

			} // endif


			if( current_user_can( 'manage_portfolio_settings' ) )
			{ 
				$settings_href = add_query_arg( array( 
					'post_type' => 'portfolio_project', 
					'page'      => 'portfolio_settings'
				), admin_url( 'edit.php' ) );

				$settings = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', esc_url( $settings_href ), __( 'Portfolio Settings', 'maxson' ), __( 'Settings', 'maxson' ) );

				array_unshift( $links, $settings );

			} // endif

			return $links;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin();

?>