<?php
/**
 * Plugin-specific meta box
 *
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio_Projects/includes/admin/meta-boxes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Project_Promoted' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Project_Promoted { 

		/**
		 * Determines if setting is active
		 * 
		 * @return      void
		 */

		public static function is_active()
		{ 
			if( maxson_portfolio_get_option( 'setup_promoted' ) && 
				current_user_can( 'edit_posts' ) )
			{ 
				return true;

			} else
			{ 
				return false;

			} // endif
		}


		/**
		 * Update promoted meta via ajax
		 * 
		 * @param      object $post
		 * @return     void|int
		 */

		public static function ajax_save( $post )
		{ 
			if( ! self::is_active() || ! current_user_can( 'edit_posts' ) )
			{ 
				wp_send_json_error( array( 
					'message' => _x( 'You do not have sufficient permissions to do this action.', 'Portfolio AJAX error message', 'maxson' )
				) );

			} // endif


			if( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'portfolio_projects_admin_column_promoted_nonce', 'nonce' ) )
			{ 
				wp_send_json_error( array( 
					'message' => _x( 'WordPress AJAX validation failed.', 'Portfolio AJAX error message', 'maxson' )
				) );

			} // endif

			$post_id  = sanitize_key( $_GET['post_id'] );
			$promoted = get_post_meta( $post_id, '_promoted', true );
			$label    = get_post_meta( $post_id, '_promoted_label', true );

			if( $promoted )
			{ 
				delete_post_meta( $post_id, '_promoted' );

				$response = array( 
					'id'      => $post_id,  
					'type'    => 'not-promoted', 
					'text'    => __( 'Project is not promoted', 'maxson' ), 
					'label'   => $label, 
					'message' => _x( 'Project has been unpromoted.', 'Project Featured AJAX success message', 'maxson' )
				);

			} else 
			{ 
				update_post_meta( $post_id, '_promoted', true );

				$response = array( 
					'id'      => $post_id, 
					'type'    => 'promoted', 
					'text'    => __( 'Project is promoted', 'maxson' ), 
					'label'   => $label, 
					'message' => _x( 'Project has been promoted.', 'Project Featured AJAX success message', 'maxson' )
				);

			} // endif

			wp_send_json_success( $response );
		}

	} // endclass
} // endif

?>