<?php
/**
 * Audio Project Class
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Project_Audio_Data' ) )
{ 
	final class Maxson_Portfolio_Projects_Project_Audio_Data extends Maxson_Portfolio_Projects_Project_Data { 

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
		 * Get the audio autoplay settings
		 *
		 * @return      int
		 */

		public function get_audio_autoplay()
		{ 
			$value = ( 'on' == $this->audio_autoplay ) ? true : false;

			return apply_filters( 'maxson_portfolio_media_audio_autoplay', $value, $this );
		}


		/**
		 * Get the audio autoplay settings
		 *
		 * @return      int
		 */

		public function get_audio_loop()
		{ 
			$value = ( 'on' == $this->audio_loop ) ? true : false;

			return apply_filters( 'maxson_portfolio_media_audio_loop', $value, $this );
		}


		/**
		 * Get the audio preload settings
		 *
		 * @return      int
		 */

		public function get_audio_preload()
		{ 
			return apply_filters( 'maxson_portfolio_media_audio_preload', $this->audio_preload, $this );
		}


		/**
		 * Get the audio poster ID
		 *
		 * @return      int
		 */

		public function get_audio_poster_id()
		{ 
			return apply_filters( 'maxson_portfolio_media_audio_poster_id', $this->audio_poster, $this );
		}


		/**
		 * Get the audio poster source
		 * 
		 * @param       string      $size (optional) Size of WordPress thumbnail
		 * @return      string|bool
		 */

		public function get_audio_poster_src( $size = 'project_thumbnail' )
		{ 
			if( $poster_id = $this->get_audio_poster_id() )
			{ 
				$image = wp_get_attachment_image_src( $poster_id, $size, false );

				return ( is_array( $image ) ) ? $image[0] : $image;

			} // endif

			return false;
		}


		/**
		 * Get the audio poster
		 *
		 * @param       string $size (optional) Size of WordPress thumbnail
		 * @param       array  $args (optional) An array of arguments
		 * @return      string
		 */

		public function get_audio_poster( $size = 'project_thumbnail', $args = array() )
		{ 
			$image = false;

			if( $poster_id = $this->get_audio_poster_id() )
			{ 
				$defaults = array( 
					'class' => 'img-responsive project-audio-poster'
				);

				$args = wp_parse_args( $args, $defaults );

				$image = $this->get_image( $poster_id, $size, $args );

				$image = apply_filters( 'maxson_portfolio_media_audio_poster', $image, $this );

			} // endif

			return $image;
		}


		/**
		 * Get audio oembed
		 * 
		 * @return      string
		 */

		public function oembed_audio_src()
		{ 
			return apply_filters( 'maxson_portfolio_media_audio_oembed_src', $this->audio_oembed, $this->id );
		}


		/**
		 * Get audio oembed
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      string
		 */

		public function oembed_audio( $args = array() )
		{ 
			return wp_oembed_get( esc_url( $this->oembed_audio_src() ), $args );
		}


		/**
		 * Get audio advanced
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      string
		 */

		public function advanced_audio_src( $args = array() )
		{ 
			$output  = array();
			$formats = maxson_portfolio_get_audio_formats();

			foreach( $formats as $format )
			{ 
				$value = get_post_meta( $this->id, "_audio_{$format}", true );

				if( ! empty( $value ) )
				{
					$output[$format] = wp_get_attachment_url( $value );

				} // endif
			} // endforeach

			return apply_filters( 'maxson_portfolio_media_audio_advanced_src', $output, $this->id );
		}


		/**
		 * Get audio advanced
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      string
		 */

		public function advanced_audio( $args = array() )
		{ 
			$formats = maxson_portfolio_get_audio_formats();

			$defaults = array( 
				'loop'     => $this->get_audio_loop(), 
				'autoplay' => $this->get_audio_autoplay(), 
				'preload'  => $this->get_audio_preload()
			);

			$meta_array = advanced_audio_src();

			$defaults = array_merge( $defaults, $meta_array );

			$args = apply_filters( 'maxson_portfolio_media_audio_advanced_args', wp_parse_args( $args, $defaults ), $this->id );

			$output  = $this->get_audio_poster( 'full' );
			$output .= wp_audio_shortcode( $args );

			return $output;
		}


		/**
		 * Get plugin-specific media src
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      array
		 */

		public function get_media_src( $args = array() )
		{ 
			$type = $this->audio_type;

			switch( $type )
			{ 
				case 'url': 
					$output = array( 
						'type' => $type, 
						'src'  => $this->oembed_audio_src()
					);
					break;

				case 'advanced': 
					$output = array( 
						'type'   => $type, 
						'poster' => $this->get_audio_poster_src( 'full' ), 
						'src'    => $this->advanced_audio_src()
					);
					break;

				default: 
					$output = apply_filters( 'maxson_portfolio_media_audio_src_default_output', array(), $type, $this->id );
					break;

			} // endswitch

			return apply_filters( 'maxson_portfolio_media_audio_src', $output, $this->id, $type );
		}


		/**
		 * Get plugin-specific audio
		 * 
		 * @param 		array $args (optional) An array of arguments
		 * @return      array
		 */

		public function get_media( $args = array() )
		{ 
			global $post;

			$defaults = array( 
				'before' => '<div class="project-audio">', 
				'after'  => '</div>'
			);

			$args = wp_parse_args( $args, $defaults );

			$passed_args = $args;
				unset( $passed_args['before'] );
				unset( $passed_args['after'] );

			switch( $this->audio_type )
			{ 
				case 'url': 
					$output = $this->oembed_audio();
					break;

				case 'advanced': 
					$output = $this->advanced_audio( $passed_args );
					break;

				default: 
					$output = apply_filters( 'maxson_portfolio_media_audio_default_output', false, $this->audio_type, $this->id, $passed_args );
					break;

			} // endswitch

			if( empty( $output ) )
				return false;

			$output = $args['before'] . $output . $args['after'];

			return apply_filters( 'maxson_portfolio_media_audio_html', $output, $this->id, $args['type'] );
		}
	}
} // endif

?>