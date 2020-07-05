<?php 
/**
 * Plugin-specific admin hooks
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


/**
 * Reset plugin capabilities
 * 
 * @return      void
 */

function maxson_portfolio_process_tools()
{ 
	if( ! isset( $_GET['maxson_portfolio_action'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], $_GET['maxson_portfolio_action'] ) )
	{ 
		wp_die( _x( 'You do not have sufficient permissions to do this action.', 'Portfolio error message', 'maxson' ) );

	} else
	{ 
		switch( $_GET['maxson_portfolio_action'] )
		{ 
			case 'reset_capabilities': 
				Maxson_Portfolio_Projects_Install::remove_capabilities();
				Maxson_Portfolio_Projects_Install::create_capabilities();

				$message = __( 'Plugin-specific capabilities have been reset.', 'maxson' );

				Maxson_Portfolio_Projects_Admin_Notices::add_success( 'reset_plugin_capabilities', $message, true );
				break;


			case 'install_pages': 
				$pages = array( 
					'archive' => array( 
						'option_name' => 'maxson_portfolio_archive_settings', 
						'option_key'  => 'page_id', 
						'name'    => _x( 'portfolio', 'Page slug', 'maxson' ), 
						'title'   => _x( 'Portfolio', 'Page title', 'maxson' ), 
						'content' => ''
					)
				);

				foreach( $pages as $key => $data )
				{ 
					$options = get_option( $data['option_name'], array() );

					$existing_value = ( isset( $options[$data['option_key']] ) ) ? $options[$data['option_key']] : false;

					if( $existing_value && get_post( $existing_value ) )
						continue;

					$existing_page = get_page_by_path( $data['name'] );

					if( ! $existing_page )
						$existing_page = get_page_by_title( $data['title'] );

					if( $existing_page && get_post( $existing_page->ID ) )
					{ 
						$options[$data['option_key']] = $existing_page->ID;

						update_option( $data['option_name'], $options );
						continue;

					} // endif


					$page_data = array( 
						'post_status'    => 'publish', 
						'post_type'      => 'page', 
						'post_author'    => 1, 
						'post_name'      => $data['name'], 
						'post_title'     => $data['title'], 
						'post_content'   => $data['content'], 
						'comment_status' => 'closed'
					);

					$page_id = wp_insert_post( $page_data );

					// Ensure unique post ID
					$page_name = wp_unique_post_slug( $page_data['post_name'], $page_id, $page_data['post_status'], $page_data['post_type'], $page_data['post_parent'] );

					wp_update_post( array( 
						'ID'        => $page_id, 
						'post_name' => $page_name
					) );


					$options[ $data['option_key'] ] = $page_id;

					update_option( $data['option_name'], $options );

					do_action( 'maxson_portfolio_add_page', $page_id, $key, $data );

				} // endforeach

				$message = _x( 'Portfolio pages have been installed.', 'Portfolio success message', 'maxson' );

				Maxson_Portfolio_Projects_Admin_Notices::add_success( 'pages_installed', $message, true );

				update_option( 'maxson_portfolio_install_pages_notice', true );
				break;


			case 'skip_install_pages': 
				update_option( 'maxson_portfolio_install_pages_notice', false );
				break;


			case 'delete_transients':
				$transients = maxson_portfolio_get_transients( 'all' );
				$result     = maxson_portfolio_delete_transients( $transients );

				$message = __( 'Plugin-specific transients have been deleted.', 'maxson' );

				Maxson_Portfolio_Projects_Admin_Notices::add_success( 'delete_plugin_transients', $message, true );
				break;


			case 'delete_expired_transients': 
				$transients = maxson_portfolio_get_transients( 'expired' );
				$result     = maxson_portfolio_delete_transients( $transients );

				$message = __( 'Plugin-specific expired transients have been deleted.', 'maxson' );

				Maxson_Portfolio_Projects_Admin_Notices::add_success( 'delete_plugin_expired_transients', $message, true );
				break;

		} // endswitch
	} // endif

	wp_safe_redirect( maxson_portfolio_get_referer() );
	exit;
}
add_action( 'maxson_portfolio_reset_pointers',            'maxson_portfolio_process_tools' );
add_action( 'maxson_portfolio_reset_capabilities',        'maxson_portfolio_process_tools' );
add_action( 'maxson_portfolio_install_pages',             'maxson_portfolio_process_tools' );
add_action( 'maxson_portfolio_skip_install_pages',        'maxson_portfolio_process_tools' );
add_action( 'maxson_portfolio_delete_transients',         'maxson_portfolio_process_tools' );
add_action( 'maxson_portfolio_delete_expired_transients', 'maxson_portfolio_process_tools' );


/**
 * Process export that generates a .json file of your site settings
 * 
 * @return      void
 */

function maxson_portfolio_process_system_report_export()
{ 
	if( empty( $_POST['maxson_portfolio_export_system_report_nonce'] ) || ! wp_verify_nonce( $_POST['maxson_portfolio_export_system_report_nonce'], 'maxson_portfolio_export_system_report_nonce' ) )
	{ 
		wp_die( _x( 'You do not have sufficient permissions to do this action.', 'Portfolio error message', 'maxson' ) );

	} else
	{ 
		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename=maxson-portfolio-system-report-' . date( 'm-d-Y' ) . '.txt' );

		echo wp_strip_all_tags( $_POST['maxson-portfolio-system-report'] );

	} // endif

	exit;
}
add_action( 'maxson_portfolio_export_system_report', 'maxson_portfolio_process_system_report_export' );


/**
 * Process export that generates a .json file of your site settings
 * 
 * @return      void
 */

function maxson_portfolio_process_settings_export()
{ 
	if( empty( $_POST['maxson_portfolio_export_settings_nonce'] ) || ! wp_verify_nonce( $_POST['maxson_portfolio_export_settings_nonce'], 'maxson_portfolio_export_settings_nonce' ) )
	{ 
		wp_die( _x( 'You do not have sufficient permissions to do this action.', 'Portfolio error message', 'maxson' ) );

	} else
	{ 
		$setting_keys = array( 
			// Global
		//	'version', 
		//	'version_prev', 
		//	'install_pages_notice', 

			// Media
			'media_thumbnail_width', 
			'media_thumbnail_height', 
			'media_thumbnail_crop', 
			'media_medium_width', 
			'media_medium_height', 
			'media_medium_crop', 
			'media_large_width', 
			'media_large_height', 
			'media_large_crop', 

			// Permalinks
			'permalink_project', 
			'permalink_category', 
			'permalink_role', 
			'permalink_tag', 
			'permalink_type', 

			// Archive (Portfolio)
			'archive_page_id', 
			'archive_limit', 
			'archive_order', 
			'archive_orderby', 
			'archive_thumbnail', 

			// Setup
			'setup_promoted', 
			'setup_taxonomy_category', 
			'setup_taxonomy_role', 
			'setup_taxonomy_tag', 
			'setup_taxonomy_type'
		);

		$settings = array();

		foreach( $setting_keys as $setting_key )
		{ 
			$settings[ $setting_key ] = maxson_portfolio_get_option( $setting_key );

		} // endforeach

		ignore_user_abort( true );

		if( ! maxson_portfolio_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) )
			set_time_limit( 0 );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=maxson-portfolio-settings-' . date( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo json_encode( $settings );

	} // endif

	exit;
}
add_action( 'maxson_portfolio_export_settings', 'maxson_portfolio_process_settings_export' );


/**
 * Process import from a .json file of your site settings
 * 
 * @return      void
 */

function maxson_portfolio_process_settings_import()
{ 
	if( empty( $_POST['maxson_portfolio_import_settings_nonce'] ) || ! wp_verify_nonce( $_POST['maxson_portfolio_import_settings_nonce'], 'maxson_portfolio_import_settings_nonce' ) )
		return;

	$import_file = $_FILES['import_file']['tmp_name'];

	if( empty( $import_file ) )
	{ 
		$message = __( 'Import failed: select a file to import', 'maxson' );

		Maxson_Portfolio_Projects_Admin_Notices::add_error( 'import_no_file', $message, true );

	} elseif( 'json' != maxson_portfolio_get_file_extension( $_FILES['import_file']['name'] ) )
	{ 
		$message = __( 'Please upload a valid .json file', 'maxson' );

		Maxson_Portfolio_Projects_Admin_Notices::add_error( 'import_invalid_format', $message, true );

	} else
	{ 
		$settings = maxson_portfolio_object_to_array( json_decode( file_get_contents( $import_file ) ) );

		foreach( $settings as $key => $value )
		{ 
			update_option( "maxson_portfolio_{$key}", $value );

		} // endforeach

		Maxson_Portfolio_Projects_Admin_Notices::add_success( 'import_success', __( 'Settings have been imported', 'maxson' ), true );

	} // endif

	wp_safe_redirect( maxson_portfolio_get_referer() );
	exit;
}
add_action( 'maxson_portfolio_import_settings', 'maxson_portfolio_process_settings_import' );

?>