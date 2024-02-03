<?php
/**
 * Plugin-specific media
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Media' ) )
{ 
	class Maxson_Portfolio_Projects_Media { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'after_setup_theme', array( &$this, 'setup' ), 10 );

			add_filter( 'post_html', array( &$this, 'image_dimensions' ), 10, 2 );
			add_filter( 'oembed_dataparse', array( &$this, 'oembed_dimensions' ), 10 ,2 );

			add_filter( 'post_thumbnail_html', array( &$this, 'image_dimensions' ), 10, 2 );
			add_filter( 'image_send_to_editor', array( &$this, 'image_dimensions' ), 10, 2 );

			add_filter( 'image_size_names_choose', array( &$this, 'media_names' ), 11 );
		}


		/**
		 * Setup plugin
		 * 
		 * @return      void
		 */

		public function setup()
		{ 
			if( ! current_theme_supports( 'post-thumbnails' ) )
			{ 
				add_theme_support( 'post-thumbnails' );

			} // endif

			add_post_type_support( self::POST_TYPE, 'thumbnail' );

			$thumbnail = maxson_portfolio_get_media_sizes();
			add_image_size( 'project_thumbnail', $thumbnail['width'], $thumbnail['height'], $thumbnail['crop'] );

			$medium = maxson_portfolio_get_media_sizes( 'medium' );
			add_image_size( 'project_medium', $medium['width'], $medium['height'], $medium['crop'] );

			$large = maxson_portfolio_get_media_sizes( 'large' );
			add_image_size( 'project_large', $large['width'], $large['height'], $large['crop'] );
		}


		/**
		 * Remove plugin-specific image attributes
		 * 
		 * @param       int    $html    Output html of the post thumbnail
		 * @param       int    $post_id Post ID
		 * @return      string
		 */

		public function image_dimensions( $html, $post_id )
		{ 
			if( self::POST_TYPE == get_post_type( $post_id ) )
			{ 
				$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );

			} // endif

			return $html;
		}


		/**
		 * Remove plugin-specific oEmbed attributes
		 * 
		 * @param       (object) A data object result from an oEmbed provider
		 * @param       (string) The URL of the content to be embedded
		 * @return      string
		 */

		public function oembed_dimensions( $data, $url )
		{ 
			global $post;

			$post_id = $post->ID;

			// Verify oembed data (as done in the oEmbed data2html code)
			if( ! is_object( $url ) || empty( $url->type ) )
				return $data;
 
			if( ! ( $url->type == 'video' ) )
				return $data;

			if( self::POST_TYPE == get_post_type( $post_id ) )
			{ 
				$apect_ratio = ( $url->width / $url->height );

				$apect_ratio_class = ( abs( $apect_ratio - ( 4 / 3 ) ) < abs( $apect_ratio - ( 16 / 9 ) ) ? 'video-is-4by3' : 'video-is-16by9' );

				$data = preg_replace( '/(width|height)="\d*"\s/', '', $data );

				$data = sprintf( '<div class="project-video %1$s">%2$s</div>', $apect_ratio_class, $data );

			} // endif

			return $data;
		}


		/**
		 * List additional image sizes that are available to administrators in the WordPress Media Library
		 * 
		 * @param       (array) Array of image sizes and their names
		 * @return      array
		 */

		public function media_names( $size_names )
		{ 
			if( true === apply_filters( 'maxson_portfolio_add_media_manager_image_sizes', true ) )
			{ 
				$new_sizes = array( 
					'project_thumbnail' => _x( 'Project Thumbnail', 'Media size image name', 'maxson' ), 
					'project_medium'    => _x( 'Project Medium', 'Media size image name', 'maxson' ), 
					'project_large'     => _x( 'Project Large', 'Media size image name', 'maxson' )
				);

				foreach( $new_sizes as $key => $label )
				{ 
					if( has_image_size( $key ) )
					{
						$size_names[$key] = $label;

					} // endif
				} // endfoeach
			} // endif

			return $size_names;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Media();

?>