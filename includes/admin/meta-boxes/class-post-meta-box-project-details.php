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


if( ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Project_Details' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Project_Details { 

		/**
		 * Prints the details meta box content
		 * 
		 * @param 		$post The object for the current post/page
		 * @return      void
		 */

		public static function output( $post )
		{ 
			$post_id = $post->ID;

			$meta_client     = maxson_project_get_client( $post_id );
			$meta_url        = maxson_project_get_url( $post_id );
			$meta_callout    = maxson_project_get_callout_label( $post_id );
			$meta_start_date = maxson_project_get_start_date( $post_id, 'raw' );
			$meta_end_date   = maxson_project_get_end_date( $post_id, 'raw' );

			do_action( 'maxson_portfolio_meta_box_before_details', $post, $post->ID );

			?><table class="form-table project-table"><tbody>
				<?php do_action( 'maxson_portfolio_meta_box_before_details_row', $post, $post->ID ); ?>

				<tr valign="top">
					<th scope="row"><label for="project-cilent"><?php _e( 'Client', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="text" class="large-text" id="project-cilent" name="project_client" value="%1$s">', esc_attr( $meta_client ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter the client name for this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-url"><abbr title="Uniform Resource Locator"><?php _e( 'URL', 'maxson' ); ?></abbr></label></th>
					<td><?php printf( '<input type="text" class="large-text" id="project-url" name="project_url" value="%1$s">', esc_attr( $meta_url ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter a <abbr title="Uniform Resource Locator">URL</abbr> that applies to this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-start-date"><?php _e( 'Start Date', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="date" class="large-text" id="project-start-date" name="project_start_date" value="%1$s" placeholder="mm-DD-YYYY">', esc_attr( $meta_start_date ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter a start date that applies to this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-end-date"><?php _e( 'End Date', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="date" class="large-text" id="project-end-date" name="project_end_date" value="%1$s" placeholder="MM-DD-YYYY">', esc_attr( $meta_end_date ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter a end date that applies to this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-callout"><?php _e( 'Callout Label', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="text" class="regular-text" id="project-callout" name="project_callout" value="%1$s">', esc_attr( $meta_callout ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter the callout for this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>

				<?php do_action( 'maxson_portfolio_meta_box_after_details_row', $post, $post->ID ); ?>
			</tbody></table>

			<?php do_action( 'maxson_portfolio_meta_box_after_details', $post, $post->ID ); ?>

			<?php wp_nonce_field( MAXSON_PORTFOLIO_BASENAME, 'portfolio_projects_details_meta_nonce' );
		}


		/**
		 * Save meta box data
		 */

		public static function save( $post_id, $post )
		{ 
			if( isset( $_POST['portfolio_projects_details_meta_nonce'] ) )
			{ 
				if( ! wp_verify_nonce( $_POST['portfolio_projects_details_meta_nonce'], MAXSON_PORTFOLIO_BASENAME ) )
				{
					return;

				} // endif


				update_post_meta( $post_id, '_client', trim( $_POST['project_client'] ) );

				update_post_meta( $post_id, '_url', trim( $_POST['project_url'] ) );

				update_post_meta( $post_id, '_callout', trim( $_POST['project_callout'] ) );


				$start_date = ( ! empty( $_POST['project_start_date'] ) ) ? trim( $_POST['project_start_date'] ) : '';
				$start_part = explode( '-', $start_date );
					$start_month  = ( isset( $start_part[0] ) ) ? trim( $start_part[0] ) : '';
					$start_day = ( isset( $start_part[1] ) ) ? trim( $start_part[1] ) : '';
					$start_year   = ( isset( $start_part[2] ) ) ? trim( $start_part[2] ) : '';

				update_post_meta( $post_id, '_start_date', $start_date );
					update_post_meta( $post_id, '_start_year', $start_year );
					update_post_meta( $post_id, '_start_month', $start_month );
					update_post_meta( $post_id, '_start_day', $start_day );


				$end_date = ( ! empty( $_POST['project_end_date'] ) ) ? trim( $_POST['project_end_date'] ) : '';
				$end_part = explode( '-', $end_date );
					$end_year  = ( isset( $end_part[0] ) ) ? trim( $end_part[0] ) : '';
					$end_month = ( isset( $end_part[1] ) ) ? trim( $end_part[1] ) : '';
					$end_day   = ( isset( $end_part[2] ) ) ? trim( $end_part[2] ) : '';

				update_post_meta( $post_id, '_end_date', $end_date );
					update_post_meta( $post_id, '_end_year', $end_year );
					update_post_meta( $post_id, '_end_month', $end_month );
					update_post_meta( $post_id, '_end_day', $end_day );

				do_action( 'maxson_portfolio_meta_box_save_details', $post_id, $post );

			} // endif
		}

	} // endclass
} // endif

?>