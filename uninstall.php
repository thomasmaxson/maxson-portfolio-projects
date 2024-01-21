<?php 
/**
 * Fired when the plugin is uninstalled (not just deactivated but actively deleted through the WordPress Admin).
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) || 
	! WP_UNINSTALL_PLUGIN || dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) )
{ 
	exit();

} // endif


function maxson_portfolio_uninstall_plugin()
{ 
	global $wp_taxonomies;

	$post_types = array( 'portfolio_project' );

	$taxonomies = array( 
		'portfolio_category', 
		'portfolio_role', 
		'portfolio_tag', 
		'portfolio_type'
	);

	$options = array( 
		'maxson_portfolio_admin_notices', 
		'maxson_portfolio_version', 
		'maxson_portfolio_version_prev', 
		'maxson_portfolio_install_pages_notice', 
		'maxson_portfolio_remove_data', 

		'maxson_portfolio_tracking_allow', 
		'maxson_portfolio_tracking_notice', 
		'maxson_portfolio_tracking_last_send', 

		'maxson_portfolio_debug_site', 
		'maxson_portfolio_debug_template', 

		'maxson_portfolio_media_thumbnail_width', 
		'maxson_portfolio_media_thumbnail_height', 
		'maxson_portfolio_media_thumbnail_crop', 
		'maxson_portfolio_media_medium_width', 
		'maxson_portfolio_media_medium_height', 
		'maxson_portfolio_media_medium_crop', 
		'maxson_portfolio_media_large_width', 
		'maxson_portfolio_media_large_height', 
		'maxson_portfolio_media_large_crop', 

		'maxson_portfolio_permalink_project', 
		'maxson_portfolio_permalink_category', 
		'maxson_portfolio_permalink_role', 
		'maxson_portfolio_permalink_tag', 
		'maxson_portfolio_permalink_type', 

		'maxson_portfolio_archive_page_id', 
		'maxson_portfolio_archive_limit', 
		'maxson_portfolio_archive_order', 
		'maxson_portfolio_archive_orderby', 
		'maxson_portfolio_archive_thumbnail'
	);


	$archive_page_id = get_option( 'maxson_portfolio_archive_page_id', false );

	if( ! empty( $archive_page_id ) && get_post( $archive_page_id ) )
	{
		wp_trash_post( $archive_page_id );

	} // endif


	if( $post_types )
	{ 
		foreach( $post_types as $post_type )
		{ 
			$post_args = array( 
				'post_type'   => $post_type, 
				'numberposts' => -1, 
				'post_status' => 'any'
			);

			$posts = get_posts( $post_args );

			foreach( $posts as $post )
			{ 
				wp_trash_post( $post->ID );

			} // endforeach
		} // endforeach
	} // endif


	if( $taxonomies )
	{ 
		foreach( $taxonomies as $taxonomy )
		{ 
			// Plugin has been deactivated. Must re-register taxonomies to delete terms
			register_taxonomy( $taxonomy );

			$terms = get_terms( $taxonomy, array( 
				'hide_empty' => false
			) );

			foreach( $terms as $term )
			{ 
				wp_delete_term( $term->term_id, $taxonomy );

			} // endforeach

			unset( $wp_taxonomies[$taxonomy] );

		} // endforeach
	} // endif


	if( $options )
	{ 
		foreach( $options as $option )
		{ 
			delete_option( $option );

		} // endforeach
	} // endif
}


if( ! function_exists( 'maxson_portfolio_run_uninstall' ) )
{ 
	function maxson_portfolio_run_uninstall()
	{ 
		global $wpdb;

		$do_uninstall = ( defined( 'MAXSON_PORTFOLIO_REMOVE_ALL_DATA' ) ) ? ( true == filter_var( MAXSON_PORTFOLIO_REMOVE_ALL_DATA, FILTER_VALIDATE_BOOLEAN ) ) : get_option( 'maxson_portfolio_remove_data' );

		if( true == $do_uninstall )
		{ 
			if( is_multisite() ) 
			{ 
				global $wpdb;

				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

				foreach( $blog_ids as $blog_id )
				{ 
					switch_to_blog( $blog_id );

					maxson_portfolio_uninstall_plugin();

					restore_current_blog();

				} // endforeach

			} else 
			{ 
				maxson_portfolio_uninstall_plugin();

			} // endif
		} // endif
	}
} // endif

maxson_portfolio_run_uninstall();

flush_rewrite_rules();

?>