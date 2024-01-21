<?php
/**
 * Plugin-specific Admin Status
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Tools' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Tools { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'admin_menu', array( &$this, 'tools_register_settings' ) );
			add_action( 'admin_menu', array( &$this, 'taxonomy_register_settings' ) );

			add_action( 'maxson_portfolio_tools_tools', array( &$this, 'tools_settings_page' ), 10, 1 );
			add_action( 'maxson_portfolio_tools_import_export_settings', array( &$this, 'import_export_settings_page' ), 10, 1 );
		}


		/**
		 * Plugin-specific admin settings page
		 * 
		 * @return      void
		 */

		public function tools_settings_page( $active_tab = null )
		{ 
			maxson_portfolio_the_tools_form( $active_tab );
		}


		/**
		 * Plugin-specific admin settings page
		 * 
		 * @return      void
		 */

		public function import_export_settings_page( $active_tab = null )
		{ 
		//	if( ! function_exists( 'get_plugin_data' ) )
		//		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$plugin_data = get_plugin_data( MAXSON_PORTFOLIO_FILE );
				$plugin_name = $plugin_data['Name'];

			$import_export_action = add_query_arg( array( 
				'post_type' => 'portfolio_project', 
				'page'      => 'portfolio_tools', 
				'tab'       => 'import_export'
			), admin_url( 'edit.php' ) );

			if( current_user_can( 'export' ) )
			{ ?>
				<div class="postbox">
					<h3><?php _e( 'Export Settings', 'maxson' ); ?></h3>
					<div class="inside">
						<p><?php printf( __( 'Export %1$s settings for this site as a .json file. This allows you to easily import your settings into another site.', 'maxson' ), $plugin_name ); ?></p>
						<form method="post" action="<?php echo esc_attr( $import_export_action ); ?>" enctype="multipart/form-data">
							<input type="hidden" name="maxson_portfolio_action" value="export_settings" >
							<?php wp_nonce_field( 'maxson_portfolio_export_settings_nonce', 'maxson_portfolio_export_settings_nonce' ); ?>
							<?php submit_button( __( 'Export Plugin Settings', 'maxson' ), 'secondary', 'submit', false ); ?>
						</form>
					</div><!-- .inside -->
				</div><!-- .postbox -->

			<?php } // endif

			if( current_user_can( 'import' ) )
			{ ?>
				<div class="postbox">
					<h3><?php _e( 'Import Settings', 'maxson' ); ?></h3>
					<div class="inside">
						<p><?php printf( __( 'Import the %1$s .json settings file. This file can be obtained by exporting the settings on another site using the form above.', 'maxson' ), $plugin_name ); ?></p>
						<form method="post" action="<?php echo esc_attr( $import_export_action ); ?>" enctype="multipart/form-data">
							<p><input type="file" name="import_file"></p>
							<input type="hidden" name="maxson_portfolio_action" value="import_settings">
							<?php wp_nonce_field( 'maxson_portfolio_import_settings_nonce', 'maxson_portfolio_import_settings_nonce' ); ?>
							<?php submit_button( __( 'Import Plugin Settings', 'maxson' ), 'secondary', 'submit', false ); ?>
						</form>
					</div><!-- .inside -->
				</div><!-- .postbox -->

			<?php } // endif
		}


		/**
		 * Plugin-specific admin options
		 * 
		 * @return      void
		 */

		public function tools_register_settings()
		{ 
			$tab     = 'tools';
			$section = 'general';

			add_settings_section( "maxson_portfolio_{$tab}_{$section}", 
				'', '__return_FALSE', "maxson_portfolio_{$tab}" );

			/*add_settings_field( "maxson_portfolio_setting_{$tab}_install_pages", __( 'Install Pages', 'maxson' ),
				array( &$this, 'setting_tools_button_control' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}", array( 
						'key'   => 'install_pages', 
						'label' => __( 'Install Pages', 'maxson' ), 
						'desc'  => __( 'Install portfolio pages. Pages already defined and set up will not be replaced.', 'maxson' )
				) );*/

			/*add_settings_field( "maxson_portfolio_setting_{$tab}_reset_capabilities", __( 'Reset Capabilities', 'maxson' ),
				array( &$this, 'setting_tools_button_control' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}", array( 
						'key'   => 'reset_capabilities', 
						'label' => __( 'Reset Capabilities', 'maxson' ), 
						'desc'  => __( 'Reset the user capabilities to the default values. Use this if your users cannot access all of the admin pages.', 'maxson' )
				) );*/

			/*add_settings_field( "maxson_portfolio_setting_{$tab}_delete_transients", __( 'Delete Transients', 'maxson' ),
				array( &$this, 'setting_tools_button_control' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}", array( 
						'key'   => 'delete_transients', 
						'label' => __( 'Delete transients', 'maxson' ), 
						'desc'  => __( 'Delete portfolio transients.', 'maxson' )
				) );*/

			/*add_settings_field( "maxson_portfolio_setting_{$tab}_delete_expired_transients", __( 'Delete Expired Transients', 'maxson' ),
				array( &$this, 'setting_tools_button_control' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}", array( 
						'key'   => 'delete_expired_transients', 
						'label' => __( 'Delete expired transients', 'maxson' ), 
						'desc'  => __( 'Delete expired portfolio transients.', 'maxson' )
				) );*/


			register_setting( "maxson_portfolio_{$tab}", ' maxson_portfolio_debug_site' );

			add_settings_field( "maxson_portfolio_setting_{$tab}_site_debug", __( 'Plugin Debug', 'maxson' ),
				array( &$this, 'setting_tools_debug_site_mode' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );


			register_setting( "maxson_portfolio_{$tab}", ' maxson_portfolio_debug_template' );

			add_settings_field( "maxson_portfolio_setting_{$tab}_template_debug", __( 'Plugin Template Debug', 'maxson' ),
				array( &$this, 'setting_tools_debug_template_mode' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );


			register_setting( "maxson_portfolio_{$tab}", ' maxson_portfolio_remove_data' );

			add_settings_field( "maxson_portfolio_setting_{$tab}_remove_data", __( 'Remove Data on Uninstall', 'maxson' ),
				array( &$this, 'setting_tools_uninstall_remove_data' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );
		}


		/**
		 * Plugin-specific admin settings
		 * 
		 * @return      void
		 */

		public function taxonomy_register_settings()
		{ 
			$tab     = 'tools';
			$section = 'taxonomies';

			add_settings_section( "maxson_portfolio_{$tab}_{$section}", 
				__( 'Taxonomies', 'maxson' ), '__return_FALSE', "maxson_portfolio_{$tab}" );

			$taxonomies = get_object_taxonomies( 'portfolio_project' );

			foreach( $taxonomies as $taxonomy )
			{ 
				$taxonomy_obj = get_taxonomy( $taxonomy );

			// Show all active taxonomies in Tools area
			//	if( ! maxson_portfolio_in_debug_mode() && ! $taxonomy_obj->public )
			//	{ 
			//		continue;

			//	} // endif

				$taxonomy_labels = get_taxonomy_labels( $taxonomy_obj );

				$link = add_query_arg( array( 
					'taxonomy'  => $taxonomy, 
					'post_type' => 'portfolio_project'
				), admin_url( 'edit-tags.php' ) );

				add_settings_field( "maxson_portfolio_setting_{$tab}_{$taxonomy}_link", $taxonomy_labels->singular_name, 
					array( &$this, 'setting_taxonomy_link' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}", array( 
						'label' => sprintf( 'View all %1$s', $taxonomy_labels->name ), 
						'link'  => $link, 
					)
				);

			} // endforeach
		}


		/**
		 * Plugin-specific admin settings
		 * 
		 * @return      void
		 */

		public static function setting_taxonomy_link( $args )
		{ 
			printf( '<a href="%1$s" class="button button-secondary">%2$s</a>', esc_url( $args['link'] ), $args['label'] );
		}


		/** 
		 * Remove plugin-specific transients button
		 * 
		 * @return      string
		 */

		public function setting_tools_button_control( $args = array() )
		{ 
			$url = add_query_arg( array( 
				'maxson_portfolio_action' => $args['key']
			), admin_url( 'edit.php' ) );

			$nonce_url = wp_nonce_url( $url, $args['key'] );

			printf( '<a href="%1$s" class="button">%2$s</a>', esc_url( $nonce_url ), $args['label'] );
			printf( '<p class="description">%1$s</p>', $args['desc'] );
		}


		/** 
		 * Plugin-specific site option callback
		 * 
		 * @return      string
		 */

		public function setting_tools_debug_site_mode()
		{ 
			$attr = '';
			$name = 'maxson_portfolio_debug_site';
			$desc = __( 'Enable debug mode', 'maxson' );

			if( defined( 'MAXSON_PORTFOLIO_DEBUG' ) )
			{ 
				if( MAXSON_PORTFOLIO_DEBUG )
				{ 
					$attr .= ' checked="checked"';

				} // endif

				$attr .= ' disabled="disabled"';

			} else
			{ 
				$value = maxson_portfolio_get_option( 'debug_site' );

				$attr .= checked( $value, '1', false );

			} // endif

			printf( '<input type="checkbox" name="%1$s" id="%1$s" value="1"%2$s>', esc_attr( $name ), $attr );
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $name ), $desc );
		}


		/** 
		 * Plugin-specific site option callback
		 * 
		 * @return      string
		 */

		public function setting_tools_debug_template_mode()
		{ 
			$attr = '';
			$name = 'maxson_portfolio_debug_template';
			$desc = __( 'Enable template debug mode', 'maxson' );

			if( defined( 'MAXSON_PORTFOLIO_DEBUG_TEMPLATE' ) )
			{ 
				if( MAXSON_PORTFOLIO_DEBUG_TEMPLATE )
				{ 
					$attr .= ' checked="checked"';

				} // endif

				$attr .= ' disabled="disabled"';

			} else
			{ 
				$value = maxson_portfolio_get_option( 'debug_template' );

				$attr .= checked( $value, '1', false );

			} // endif

			printf( '<input type="checkbox" name="%1$s" id="%1$s" value="1"%2$s>', esc_attr( $name ), $attr );
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $name ), $desc );
		}


		/** 
		 * Plugin-specific site option callback
		 * 
		 * @return      string
		 */

		public function setting_tools_uninstall_remove_data()
		{ 
			$attr = '';
			$name = 'maxson_portfolio_remove_data';
			$desc = __( 'Permanently delete all plugin-specific data when using the "Delete" link on the plugin screen.', 'maxson' );

			$value = maxson_portfolio_get_option( 'remove_data' );

			if( defined( 'MAXSON_PORTFOLIO_REMOVE_ALL_DATA' ) )
			{ 
				if( true == filter_var( MAXSON_PORTFOLIO_REMOVE_ALL_DATA, FILTER_VALIDATE_BOOLEAN ) )
				{ 
					$attr .= ' checked="checked"';

				} // endif

				$attr .= ' disabled="disabled"';

			} else
			{ 
				$attr .= checked( $value, '1', false );

			} // endif

			printf( '<input type="checkbox" name="%1$s" id="%1$s" value="1"%2$s>', esc_attr( $name ), $attr );
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $name ), $desc );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Tools();

?>