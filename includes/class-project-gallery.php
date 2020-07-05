<?php
/**
 * Gallery Project Class
 * 
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio/includes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Project_Gallery_Data' ) )
{ 
	final class Maxson_Portfolio_Projects_Project_Gallery_Data extends Maxson_Portfolio_Projects_Project_Data { 

		/**
		 * __construct function
		 * 
		 * @param       mixed $project
		 */

		public function __construct( $project )
		{ 
			parent::__construct( $project );
		}


		/**
		 * Gets the project gallery
		 * 
		 * @param 		array       $args (optional) An array of arguments
		 * @return      bool|string
		 */

		public function get_media_src( $args = array() )
		{ 
			$defaults = array( 
				'exclude' => false, 
				'include' => false, 
				'size'    => 'project_large'
			);

			$args = wp_parse_args( $args, $defaults );

			$image_ids = $this->get_gallery_ids( array( 
				'exclude' => $args['exclude'], 
				'include' => $args['include']
			) );

			if( $image_ids )
			{ 
				$images = array();

				foreach( $image_ids as $image_id )
				{ 
					$image = wp_get_attachment_image_src( $image_id, $args['size'] );

					$images[] = $image[0];

				} // endforeach

				return apply_filters( 'maxson_portfolio_media_gallery_images', $images );

			} else
			{ 
				return false;

			} // endif
		}


		/**
		 * Gets the project media
		 * 
		 * @param 		array       $args (optional) An array of arguments
		 * @return      bool|string
		 */

		public function get_media( $args = array() )
		{ 
			return $this->get_gallery( $args );
		}


		/**
		 * Gets the project gallery IDs
		 * 
		 * @param 		array       $args (optional) An array of arguments
		 * @return      bool|string
		 */

		public function get_gallery_ids( $args = array() )
		{ 
			$ids = $this->gallery_images;

			$defaults = array( 
				'exclude' => false, 
				'include' => false
			);

			$args = wp_parse_args( $args, $defaults );

			if( empty( $ids ) )
			{
				$ids = array();

			} // endif

			if( ! is_array( $ids ) )
			{
				$ids = explode( ',', $ids );

			} // endif


			if( ! empty( $args['include'] ) )
			{ 
				if( ! is_array( $args['include'] ) )
				{
					$args['include'] = array( $args['include'] );

				} // endif

				$ids = array_unique( array_merge( $ids, $args['include'] ) );

			} // endif


			if( ! empty( $args['exclude'] ) )
			{ 
				if( ! is_array( $args['exclude'] ) )
				{
					$args['exclude'] = array( $args['exclude'] );

				} // endif

				$ids = array_diff( $ids, $args['exclude'] );

			} // endif
			
			return apply_filters( 'maxson_portfolio_media_gallery_image_ids', $ids, $this, $args );
		}


		/**
		 * Gets the project gallery
		 * 
		 * @param 		string      $size (optional) Image size
		 * @param 		array       $args (optional) An array of arguments
		 * @return      bool|string
		 */

		public function get_gallery( $size = 'project_large', $args = array() )
		{ 
			$defaults = array( 
				'exclude'      => false, 
				'include'      => false, 
				'before'       => '<div class="project-gallery slick-slider">', 
				'after'        => '</div>', 
				'before_image' => '<div class="slick-slide">', 
				'after_image'  => '</div>', 
				'image_class'  => false
			);

			$args = wp_parse_args( $args, $defaults );

			$image_ids = $this->get_gallery_ids( array( 
				'exclude' => $args['exclude'], 
				'include' => $args['include']
			) );

			if( $image_ids )
			{ 
				$images = array();

				$image_args = $args;
					$image_args['before'] = $args['before_image'];
					$image_args['after']  = $args['after_image'];

				if( ! empty( $args['image_class'] ) )
				{ 
					$image_args['class'] = $args['image_class'];

				} // endif

			//	unset( $args['exclude'], $args['include'], $args['before_image'], $args['after_image'] );

				foreach( $image_ids as $image_id )
				{ 
					$images[] = $this->get_gallery_image( $image_id, $size, $image_args );

				} // endforeach

				$output = $args['before'] . join( "\n", $images ) . $args['after'];

				return apply_filters( 'maxson_portfolio_media_gallery_html', $output );

			} else
			{ 
				return false;

			} // endif
		}


		/**
		 * Gets the project gallery
		 * 
		 * @param 		int         $attachment_id (optional) Attachment media ID to query
		 * @param 		string      $size          (optional) Image size
		 * @param 		array       $args          (optional) An array of arguments
		 * @return      bool|string
		 */

		public function get_gallery_image( $attachment_id = null, $size = 'project_large', $args = array() )
		{ 
			if( is_null( $attachment_id ) || ! maxson_portfolio_attachment_exists( $attachment_id ) )
				return false;

			$defaults = array( 
				'link'   => false, 
				'class'  => apply_filters( 'maxson_portfolio_media_gallery_image_class', 'img-responsive', $attachment_id, $size ), 
				'alt'    => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ), 
				'before' => '<li>', 
				'after'  => '</li>'
			);

			$args = wp_parse_args( $args, $defaults );

			$img_args = array( 
				'class' => $args['class'], 
				'alt'   => $args['alt']
			);

			$image = apply_filters( 'maxson_portfolio_media_gallery_image', wp_get_attachment_image( $attachment_id, $size, false, $img_args ), $attachment_id, $img_args );


			if( $args['link'] )
			{ 
				$image_link    = wp_get_attachment_url( $attachment_id );
				$image_caption = get_post_field( 'post_excerpt', $attachment_id );
				$image_class   = apply_filters( 'maxson_portfolio_media_gallery_image_link_class', 'no-ajax zoom', $attachment_id );

				$attachment_output = sprintf( '%1$s<a href="%2$s" title="%3$s" class="%4$s" data-rel="media[project-gallery]">%5$s</a>%6$s', $args['before'], $image_link, $image_caption, $image_class, $image, $args['after'] );

			} else 
			{ 
				$attachment_output = $args['before'] . $image . $args['after'];

			} // endif

			return apply_filters( 'maxson_portfolio_media_gallery_image_html', $attachment_output, $attachment_id );
		}

	}
} // endif

?>