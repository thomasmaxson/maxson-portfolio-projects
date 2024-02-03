<?php
/**
 * Single Project carousel
 *
 * @author      Thomas Maxson
 * @package     Portfolio_Projects/templates
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif

if( count( $attributes['ids'] ) > 0 )
{ 
	/**
	 * maxson_portfolio_project_carousel_before hook
	 */

	do_action( 'maxson_portfolio_project_carousel_before', $attributes );

	?><ul class="project-carousel">

		<?php foreach( $attributes['ids'] as $attachment_id )
		{ 
			$attachment_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			$attachment_caption = wp_get_attachment_caption( $attachment_id );

			$attachment_size = apply_filters( 'maxson_portfolio_project_carousel_image_size', $attributes['imageResolution'], $attachment_id );

			$attachment_attrs = apply_filters( 'maxson_portfolio_project_carousel_image_attrs', array( 
				'id'    => sprintf( 'attachment-%1$s', $attachment_id ), 
				'class' => 'project-carousel--slide-image', 
				'alt'   => $attachment_alt
			), $attachment_id );

			printf( '<li %1$s>', join( ' ', $attachment_attrs ) );

				/**
				 * maxson_portfolio_project_carousel_slide_before hook
				 */

				do_action( 'maxson_portfolio_project_carousel_slide_before', $attachment_id, $attributes );

				echo '<figure>';
					/**
					 * maxson_portfolio_project_carousel_slide_before_image hook
					 */

					do_action( 'maxson_portfolio_project_carousel_slide_before_image', $attachment_id, $attributes );
					
					echo wp_get_attachment_image( $attachment_id, $attachment_size, false, $attachment_attrs );

					if( ! empty( $attachment_caption ) )
					{ 
						printf( '<figcaption>%1$s</figcaption>', $attachment_caption );

					} // endif

					/**
					 * maxson_portfolio_project_carousel_slide_after hook
					 */

					do_action( 'maxson_portfolio_project_carousel_slide_after', $attachment_id, $attributes );

				echo '</figure>';

				/**
				 * maxson_portfolio_project_carousel_slide_after_image hook
				 */

				do_action( 'maxson_portfolio_project_carousel_slide_after_image', $attachment_id, $attributes );
				
			?></li>

		<?php } // endforeach ?>

	</ul><?php 

	/**
	 * maxson_portfolio_project_carousel_after hook
	 */

	do_action( 'maxson_portfolio_project_carousel_after', $attributes );

} // endif ?>