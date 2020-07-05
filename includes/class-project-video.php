<?php
/**
 * Video Project Class
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Project_Video_Data' ) )
{ 
	class Maxson_Portfolio_Projects_Project_Video_Data extends Maxson_Portfolio_Projects_Project_Data { 

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
		 * Get the video autoplay settings
		 *
		 * @return      int
		 */

		public function get_video_autoplay()
		{ 
			$value = ( 'on' == $this->video_autoplay ) ? true : false;

			return apply_filters( 'maxson_portfolio_media_video_autoplay', $value, $this );
		}


		/**
		 * Get the video autoplay settings
		 *
		 * @return      int
		 */

		public function get_video_loop()
		{ 
			$value = ( 'on' == $this->video_loop ) ? true : false;

			return apply_filters( 'maxson_portfolio_media_video_loop', $value, $this );
		}


		/**
		 * Get the video poster ID
		 *
		 * @return      int|bool
		 */

		public function get_video_poster_id()
		{ 
			return apply_filters( 'maxson_portfolio_media_video_poster_id', $this->video_poster, $this );
		}


		/**
		 * Get the video poster source
		 * 
		 * @param       string      $size (optional) Size of WordPress thumbnail
		 * @return      string|bool
		 */

		public function get_video_poster_src( $size = 'project_thumbnail' )
		{ 
			if( $poster_id = $this->get_video_poster_id() )
			{ 
				$image = wp_get_attachment_image_src( $poster_id, $size, false );

				return ( is_array( $image ) ) ? $image[0] : $image;

			} // endif

			return false;
		}


		/**
		 * Get the video poster
		 *
		 * @param       string $size (optional) Size of WordPress thumbnail
		 * @param       array  $args (optional) An array of arguments
		 * @return      string
		 */

		public function get_video_poster( $size = 'project_thumbnail', $args = array() )
		{ 
			$image = false;

			if( $poster_id = $this->get_video_poster_id() )
			{ 
				$image = $this->get_image( $poster_id, $size, $args );

				$image = apply_filters( 'maxson_portfolio_media_video_poster', $image, $this );

			} // endif

			return $image;
		}


		/**
		 * Get video oembed source
		 * 
		 * @return      string|bool
		 */

		public function oembed_video_src()
		{ 
			return apply_filters( 'maxson_portfolio_media_video_oembed_src', $this->video_oembed, $this );
		}


		/**
		 * Get video oembed
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      string
		 */

		public function oembed_video( $args = array() )
		{ 
			$src = $this->oembed_video_src();

			return wp_oembed_get( esc_url( $src ), $args );
		}


		/**
		 * Get video embed source
		 * 
		 * @return      string|bool
		 */

		public function embed_video_src()
		{ 
			return apply_filters( 'maxson_portfolio_media_video_embed_src', $this->video_embed, $this );
		}


		/**
		 * Get video embed
		 * 
		 * @return      string
		 */

		public function embed_video()
		{ 
			$src = $this->embed_video_src();

			return html_entity_decode( esc_html( $src ) );
		}


		/**
		 * Get video advanced
		 * 
		 * @return      string
		 */

		public function advanced_video_src()
		{ 
			$output  = array();
			$formats = maxson_portfolio_get_video_formats();

			foreach( $formats as $format )
			{ 
				$value = get_post_meta( $this->id, "_video_{$format}", true );

				if( ! empty( $value ) )
					$output[$format] = wp_get_attachment_url( $value );

			} // endforeach

			return apply_filters( 'maxson_portfolio_media_video_advanced_src', $output, $this );
		}


		/**
		 * Get video advanced
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      string
		 */

		public function advanced_video( $args = array() )
		{ 
			$formats = maxson_portfolio_get_video_formats();

			$defaults = array( 
				'loop'     => ( 'on' == $this->get_video_loop() )     ? true : false, 
				'autoplay' => ( 'on' == $this->get_video_autoplay() ) ? true : false, 
				'poster'   => $this->get_video_poster_src( 'full' )
			);

			$meta_array = $this->advanced_video_src();

			$defaults = array_merge( $defaults, $meta_array );

			$args = apply_filters( 'maxson_portfolio_media_video_advanced_args', wp_parse_args( $args, $defaults ), $this );

			return wp_video_shortcode( $args );
		}


		/**
		 * Get plugin-specific media src
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      array
		 */

		public function get_media_src( $args = array() )
		{ 
			$type = $this->video_type;

			switch( $type )
			{ 
				case 'url': 
					$output = array( 
						'type' => $type, 
						'src'  => $this->oembed_video()
					);
					break;

				case 'embed': 
					$output = array( 
						'type' => $type, 
						'src'  => $this->embed_video()
					);
					break;

				case 'advanced': 
					$output = array( 
						'type'   => $type, 
						'poster' => $this->get_video_poster_src( 'full' ), 
						'src'    => $this->advanced_video_src()
					);
					break;

				default: 
					$output = apply_filters( 'maxson_portfolio_media_video_src_default_output', array(), $type, $this );
					break;

			} // endswitch

			return apply_filters( 'maxson_portfolio_media_video_src', $output, $type, $this );
		}


		/**
		 * Get plugin-specific video
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      array
		 */

		public function get_media( $args = array() )
		{ 
			global $post;

			$defaults = array( 
				'before' => '<div class="project-video">', 
				'after'  => '</div>'
			);

			$args = wp_parse_args( $args, $defaults );

			$passed_args = $args;
				unset( $passed_args['before'] );
				unset( $passed_args['after'] );

			$type = $this->video_type;

			switch( $type )
			{ 
				case 'url': 
					$output = $this->oembed_video( $passed_args );
					break;

				case 'embed': 
					$output = $this->embed_video();
					break;

				case 'advanced': 
					$output = $this->advanced_video( $passed_args );
					break;

				default: 
					$output = apply_filters( 'maxson_portfolio_media_video_default_output', false, $type, $passed_args, $this );
					break;

			} // endswitch

			if( empty( $output ) )
				return false;

			$output = $args['before'] . $output . $args['after'];

			return apply_filters( 'maxson_portfolio_media_video_html', $output, $type, $this );
		}

	}
} // endif

?>