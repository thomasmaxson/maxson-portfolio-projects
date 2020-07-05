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


if( ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Testimonial_Field' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Testimonial_Field { 

		/**
		 * Prints the details meta box content
		 * 
		 * @param 		$post The object for the current post/page
		 * @return      void
		 */

		public static function output( $post )
		{ 
			$testimonial_meta = get_post_meta( $post->ID, '_testimonial', true );

			$testimonial_title = ( ! empty( $testimonial_meta ) ) ? get_the_title( $testimonial_meta ) : '';

			?>
				<tr valign="top">
					<th scope="row"><label for="portfolio_testimonial_search"><?php _e( 'Testimonial', 'maxson' ); ?></label></th>
					<td><?php printf( '<input type="hidden" id="portfolio_testimonial" name="portfolio_testimonial" value="%1$s">', esc_attr( $testimonial_meta ) ); ?>
						<?php printf( '<input type="text" class="large-text" id="portfolio_testimonial_search" name="portfolio_testimonial_search" value="%1$s">', esc_attr( $testimonial_title ) ); ?>

						<?php printf( '<p class="description">%1$s</p>', __( 'Select a testimonial that references this project. (Optional)', 'maxson' ) ); ?>

						<?php wp_nonce_field( MAXSON_PORTFOLIO_BASENAME, 'portfolio_projects_testimonial_nonce' ); ?>
					</td>
				</tr>
			<?php
		}


		/**
		 * Save meta box data
		 */

		public static function save( $post_id, $post )
		{ 
			if( isset( $_REQUEST['portfolio_testimonial'] ) )
			{ 
				update_post_meta( $post_id, '_testimonial', $_REQUEST['portfolio_testimonial'] );

			} // endif

			do_action( 'maxson_portfolio_meta_box_save_testimonial', $post_id, $post );
		}

	} // endclass
} // endif

?>