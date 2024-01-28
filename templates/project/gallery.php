<?php
/**
 * Single Project gallery
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

var_dump( $images );

$attachment_size = apply_filters( 'maxson_portfolio_project_gallery_image_size', 'project_large' );

$attachment_link_type = $attributes['linkTo'];

?><ul class="project-gallery--carousel">

	<?php foreach( $images as $image )
	{ 
		$attachment_id = $image['id'];

		$attachment_link    = '';
		$attachment_target  = ( $attributes['target'] ) ? '_blank' : '_self';
		//$attachment_src = wp_get_attachment_image_src( $attachment_id, 'full' );
		//$attachment_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		$attachment_caption = wp_get_attachment_caption( $attachment_id );

		$attachment_attrs   = apply_filters( 'maxson_portfolio_project_gallery_image_attrs', array( 
			'class' => 'project-gallery--slide-image'
		) );

		if( 'attachment' === $attachment_link_type )
		{ 
			$attachment_link = wp_get_attachment_url( $attachment_id );

		} else if( 'media' === $attachment_link_type )
		{ 
			$attachment_link = wp_get_attachment_image_src( $attachment_id, 'full')[0];

		} else if( 'url' === $attachment_link_type )
		{ 
			$attachment_link = '';

		} // endif


		printf( '<li id="attachment-%1$s" class="project-gallery--slide">',$attachment_id );

			echo '<figure>';

			if( $attachment_link )
			{ 
				printf( '<a href="%1$s" target="%2$s" class="project-gallery--link" rel="noopener">',$attachment_link, $attachment_target );

			} // endif

			echo wp_get_attachment_image( $attachment_id, $attachment_size, false, $attachment_attrs );

			if( ! empty( $attachment_caption ) )
			{ 
				printf( '<figcaption>%1$s</figcaption>', $attachment_caption );

			} // endif

			if( $attachment_link )
			{ 
				echo '</a>';

			} // endif

			echo '</figure>';

			/**
			 * maxson_portfolio_project_gallery_slide_after_image hook
			 */

			do_action( 'maxson_portfolio_project_gallery_slide_after_image' ); ?>
		</li>

		<?php } // endwhile ?>
</ul>