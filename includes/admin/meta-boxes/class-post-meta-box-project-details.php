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
			$project = maxson_portfolio_get_project( $post );

			$date_format = maxson_portfolio_get_date_format();

			$project_is_promoted    = $project->is_promoted();
			$project_promoted_label = $project->get_promoted_label();

			$project_client         = $project->get_client();
			$project_url            = $project->get_url();

			$project_start_date     = $project->get_start_date();
			$project_start_date_raw = $project->get_start_date( 'raw' );

			$project_end_date       = $project->get_end_date();
			$project_end_date_raw   = $project->get_end_date( 'raw' );

			$project_taxonomy_args = array( 
				'hide_empty' => false, 
				'orderby'    => 'name'
			);

			$all_project_types = get_terms( 'portfolio_type', $project_taxonomy_args );

			do_action( 'maxson_portfolio_meta_box_before_details', $post, $post->ID );

			?><table class="form-table project-table"><tbody>
				<?php do_action( 'maxson_portfolio_meta_box_before_details_row', $post, $post->ID ); ?>

				<tr valign="top">
					<th scope="row"><label for="project-promoted"><?php _e( 'Promote', 'maxson' ); ?></label></th>
					<td><label for="project-promoted"><?php printf( '<input type="checkbox" id="project-promoted" name="project_promoted" value="1"%1$s>', checked( $project_is_promoted, true, false ) ); ?> <?php _e( 'Promote this project', 'maxson' ); ?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-promoted-label"><?php _e( 'Promoted Label', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="text" class="regular-text" id="project-promoted-label" name="project_promoted_label" value="%1$s">', esc_attr( $project_promoted_label ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter the promoted label for this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-cilent"><?php _e( 'Client', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="text" class="large-text" id="project-cilent" name="project_client" value="%1$s">', esc_attr( $project_client ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter the client name for this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-url"><abbr title="Uniform Resource Locator"><?php _e( 'URL', 'maxson' ); ?></abbr></label></th>
					<td><?php printf( '<input type="text" class="large-text" id="project-url" name="project_url" value="%1$s">', esc_attr( $project_url ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter a <abbr title="Uniform Resource Locator">URL</abbr> that applies to this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-start-date"><?php _e( 'Start Date', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="text" class="large-text hide-if-no-js" id="project-start-date" value="%1$s">', esc_attr( $project_start_date ) ); ?>
						<?php printf( '<input type="text" class="large-text hide-if-js" id="project-start-date-raw" name="project_start_date_raw" value="%1$s" placeholder="YYYY-MM-DD">', esc_attr( $project_start_date_raw ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter a start date that applies to this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="project-end-date"><?php _e( 'End Date', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="text" class="large-text hide-if-no-js" id="project-end-date" value="%1$s">', esc_attr( $project_end_date ) ); ?>
						<?php printf( '<input type="text" class="large-text hide-if-js" id="project-end-date-raw" name="project_end_date_raw" value="%1$s" placeholder="YYYY-MM-DD">', esc_attr( $project_end_date_raw ) ); ?>
						<?php printf( '<p class="description">%1$s</p>', __( 'Enter a end date that applies to this project. (optional)', 'maxson' ) ); ?>
					</td>
				</tr>

				<?php do_action( 'maxson_portfolio_meta_box_after_details_row', $post, $post->ID ); ?>

				<?php if( ! empty( $all_project_types ) && is_array( $all_project_types ) )
				{ 
					$project_types = wp_get_post_terms( $post_id, 'portfolio_type' );

					if( empty( $project_types ) )
					{ 
						$project_types   = $all_project_types;
						$project_type_id = apply_filters( 'maxson_portfolio_meta_box_default_project_type', current( $project_types )->term_id, $project_types );

					} else
					{ 
					//	$project_type = current( $project_types )->term_id;
						$project_type_id = $project->get_type( 'term_id' );

					} // endif

					?>
					<tr valign="top">
						<th scope="row"><label for="project-type"><?php _e( 'Media Type', 'maxson' ); ?></label></th>
						<td><fieldset class="project-radio-button-group" id="project-media-type">
							<legend class="screen-reader-text"><?php _e( 'Project Media Type', 'maxson' ); ?></legend>
							<?php foreach( $all_project_types as $type )
							{ 
								$term_meta_label = maxson_portfolio_get_term_type( $type->term_id );
								$term_meta_slug  = strtolower( str_replace( array( ' ', '-' ), '_', $term_meta_label ) );

								$is_checked = checked( $type->term_id, $project_type_id, false );

								printf( '<input type="radio" class="portfolio-format toggle-metabox" name="project_type" id="portfolio_type_%1$s" value="%2$s" data-metabox-type="meta_box_project_type_%1$s"%3$s>', $term_meta_slug, $type->term_id, $is_checked );

								printf( '<label for="portfolio_type_%1$s" class="portfolio-format-icon portfolio-format-%1$s"> %2$s</label><br>', $term_meta_slug, $type->name );
							} ?>
						</fieldset></td>
					</tr>

				<?php } // endif ?>
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

				$type = ( isset( $_POST['project_type'] ) ) ? wp_parse_id_list( array( $_POST['project_type'] ) ) : '';

				wp_set_object_terms( $post_id, $type, 'portfolio_type' );



				if( isset( $_POST['project_promoted'] ) && '1' == $_POST['project_promoted'] )
				{ 
					update_post_meta( $post_id, '_promoted', true );

				} else 
				{ 
					delete_post_meta( $post_id, '_promoted' );

				} // endif


				if( isset( $_POST['project_promoted_label'] ) )
				{ 
					$default = maxson_portfolio_get_default_promoted_label();
					$label   = trim( $_POST['project_promoted_label'] );

					if( empty( $label ) || $default == $label )
					{ 
					//	update_post_meta( $post_id, '_promoted_label', $default );
						delete_post_meta( $post_id, '_promoted_label' );

					} else
					{ 
						update_post_meta( $post_id, '_promoted_label', $label );

					} // endif
				} else
				{ 
					delete_post_meta( $post_id, '_promoted_label' );

				} // endif


				update_post_meta( $post_id, '_client', trim( $_POST['project_client'] ) );


				update_post_meta( $post_id, '_url', trim( $_POST['project_url'] ) );


				$start_date = ( ! empty( $_POST['project_start_date_raw'] ) ) ? trim( $_POST['project_start_date_raw'] ) : '';
				$start_part = explode( '-', $start_date );
					$start_year  = ( isset( $start_part[0] ) ) ? trim( $start_part[0] ) : '';
					$start_month = ( isset( $start_part[1] ) ) ? trim( $start_part[1] ) : '';
					$start_day   = ( isset( $start_part[2] ) ) ? trim( $start_part[2] ) : '';

				update_post_meta( $post_id, '_start_date', $start_date );
					update_post_meta( $post_id, '_start_year', $start_year );
					update_post_meta( $post_id, '_start_month', $start_month );
					update_post_meta( $post_id, '_start_day', $start_day );


				$end_date = ( ! empty( $_POST['project_end_date_raw'] ) ) ? trim( $_POST['project_end_date_raw'] ) : '';
				$end_part = explode( '-', $end_date );
					$end_year  = ( isset( $end_part[0] ) ) ? trim( $end_part[0] ) : '';
					$end_month = ( isset( $end_part[1] ) ) ? trim( $end_part[1] ) : '';
					$end_day   = ( isset( $end_part[2] ) ) ? trim( $end_part[2] ) : '';

				update_post_meta( $post_id, '_end_date', $end_date );
					update_post_meta( $post_id, '_end_year', $end_year );
					update_post_meta( $post_id, '_end_month', $end_month );
					update_post_meta( $post_id, '_end_day', $end_day );


				$archive_id = maxson_portfolio_get_archive_page_id( false );

				if( ! empty( $archive_id ) && 
					apply_filters( 'maxson_portfolio_save_archive_as_project_parent', true ) )
				{ 
					wp_update_post( array( 
						'ID'          => $post_id, 
						'post_parent' => $archive_id
					) );

				} // endif

				do_action( 'maxson_portfolio_meta_box_save_details', $post_id, $post );

			} // endif
		}

	} // endclass
} // endif

?>