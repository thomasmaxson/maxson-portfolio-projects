<?php
/**
 * Plugin-specific meta box fields
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Post_Meta' ) )
{ 
	class Maxson_Portfolio_Projects_Post_Meta { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'init', array( &$this, 'register' ), 5 );

			add_filter( 'wp_kses_allowed_html', array( &$this, 'allow_multisite_tags' ), 1 );
		}


		/**
		 * Return plugin meta fields
		 * 
		 * @return      array
		 */

		public static function meta_fields()
		{ 
			$audio_formats = maxson_portfolio_get_audio_formats();
			$video_formats = maxson_portfolio_get_video_formats();

			$array = array( 
				// Callout
				'_callout'       => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 

				// Details
				'_client'         => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_url'            => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_url' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_start_date'     => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'integer', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_end_date'       => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'integer', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 

				// Gallery
				'_gallery_type'   => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_gallery_images' => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_array' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'array', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 

				// Audio
				'_audio_type'     => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_audio_oembed'   => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_audio_poster'   => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_number' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'integer', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_audio_loop'     => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_audio_autoplay' => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_audio_preload'  => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 

				// Videos
				'_video_type'     => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_video_oembed'   => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_video_embed'    => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_textarea' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_video_poster'   => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_number' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'integer', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_video_loop'     => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_video_autoplay' => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				), 
				'_video_preload'  => array( 
				//	'sanitize_callback' => array( &$this, 'sanitize_text' ), 
					'auth_callback'     => '__return_true', 
					'type'              => 'string', 
					'description'       => __( '', 'maxson' ), 
					'single'            => true, 
					'show_in_rest'      => true
				)
			);

			if( is_array( $audio_formats ) && ! empty( $audio_formats ) )
			{ 
				foreach( $audio_formats as $audio_format )
				{ 
					$array["_audio_{$audio_format}"] = array( 
					//	'sanitize_callback' => array( &$this, 'sanitize_number' ), 
						'auth_callback'     => '__return_true', 
						'type'              => 'integer', 
						'description'       => sprintf( __( '%1$s audio attachment ID', 'maxson' ), $audio_format ), 
						'single'            => true, 
						'show_in_rest'      => true
					);

				} // endforeach
			} // endif


			if( is_array( $video_formats ) && ! empty( $video_formats ) )
			{ 
				foreach( $video_formats as $video_format )
				{ 
					$array["_video_{$video_format}"] = array( 
					//	'sanitize_callback' => array( &$this, 'sanitize_number' ), 
						'auth_callback'     => '__return_true', 
						'type'              => 'integer', 
						'description'       => sprintf( __( '%1$s video attachment ID', 'maxson' ), $video_format ), 
						'single'            => true, 
						'show_in_rest'      => true
					);

				} // endforeach
			} // endif

			return $array;
		}


		/**
		 * Register plugin meta fields
		 * 
		 * @return      void
		 */

		public static function register()
		{ 
			global $wp_version;

			$meta_array = self::meta_fields();

			// register_meta() is undocumented and not used by WP internally, wrapped in function_exists() as a precaution in case it is removed
			if( function_exists( 'register_meta' ) && ! empty( $meta_array ) )
			{ 
				if( version_compare( $wp_version, '4.6', '>=' ) )
				{ 
					foreach( $meta_array as $meta_key => $meta_args )
					{ 
						register_meta( 'post', $meta_key, $meta_args );

					} // endforeach
				} else
				{ 
					foreach( $meta as $meta_key => $meta_args )
					{ 
						$sanitize_callback = $meta_args['sanitize_callback'];
						$auth_callback     = $meta_args['auth_callback'];

						register_meta( 'post', $meta_key, $sanitize_callback, $auth_callback );

					} // endforeach
				} // endif
			} // endif
		}


		/**
		 * Sanitize array value
		 * 
		 * @return      array
		 */

		public function sanitize_array( $value )
		{ 
			if( empty( $value ) )
				$value = array();

			if( is_string( $value ) )
				$value = explode( ',', $value );

			return array_unique( $value );
		}


		/**
		 * Sanitize array value
		 * 
		 * @return      array
		 */

		public function sanitize_multidimentional_array( $value )
		{ 
			if( ! is_array( $value ) || ! count( $value ) )
			{
				return array();

			} // endif

			foreach( $value as $k => $v )
			{ 
				if( ! is_array( $v ) && ! is_object( $v ) )
				{ 
					$value[$k] = htmlspecialchars( trim( $v ) );

				} // endif

				if( is_array( $v ) )
				{ 
					$value[$k] = self::sanitize_multidimentional_array( $v );

				} // endif
			} // endforeach

			return $value;
		}


		/**
		 * Sanitize checkbox value
		 * 
		 * @return      bool
		 */

		public function sanitize_checkbox( $value )
		{ 
			return ( isset( $value ) && ! empty( $value ) ) ? 1 : 0;
		}


		/**
		 * Sanitize date value
		 * 
		 * @return      string
		 */

		public function sanitize_date( $value )
		{ 
			return strtotime( $value );
		}


		/**
		 * Sanitize wp_editor value
		 * 
		 * @return      string
		 */

		public function sanitize_editor( $value )
		{ 
			if( current_user_can( 'unfiltered_html' ) )
			{ 
				$output = $value;

			} else
			{ 
				global $allowedposttags;

				$output = wp_kses( $value, $allowedposttags );

			} // endif

			return $output;
		}


		/**
		 * Sanitize email address value
		 * 
		 * @return      string
		 */

		public function sanitize_email( $value )
		{ 
			if( ! is_email( $value ) )
				return false;

			return sanitize_email( $value );
		}


		/**
		 * Sanitize input value
		 * 
		 * @see         self::sanitize_text()
		 * 
		 * @return      string
		 */

		public function sanitize_input( $value )
		{ 
			return self::sanitize_text( $value );
		}


		/**
		 * Sanitize key value
		 * 
		 * @return      string
		 */

		public function sanitize_key( $value, $type = '-' )
		{ 
			$value = strtolower( $value );
			$value = preg_replace( '/&.+?;/', '', $value ); // kill entities
			$value = str_replace( '.', $type, $value );

			$value = preg_replace( '/[^%a-z0-9 _-]/', '', $value );
			$value = preg_replace( '/\s+/', $type, $value );
			$value = preg_replace( '|-+|', $type, $value );

			$value = trim( $value, $type );

			return sanitize_key( $value );
		}


		/**
		 * Sanitize key value with dashes
		 * 
		 * @return      string
		 */

		public function sanitize_key_with_dash( $value )
		{ 
			return sanitize_key( $value );
		}


		/**
		 * Sanitize key value with underscores
		 * 
		 * @return      string
		 */

		public function sanitize_key_with_underscore( $value )
		{ 
			return sanitize_key( $value, '_' );
		}


		/**
		 * Sanitize number value
		 * 
		 * @return      int
		 */

		public function sanitize_number( $value )
		{ 
			if( ! is_numeric( $value ) )
				return false;

			return intval( $value );
		}


		/**
		 * Sanitize password value
		 * 
		 * @return      string
		 */

		public function sanitize_password( $value )
		{ 
			return self::sanitize_text( $value );
		}


		/**
		 * Sanitize select value
		 * 
		 * @see         self::sanitize_text()
		 * 
		 * @return      string
		 */

		public function sanitize_select( $value )
		{ 
			return self::sanitize_text( $value );
		}


		/**
		 * Sanitize telephone number value (only allow numeric characters)
		 * 
		 * @return      string
		 */

		public function sanitize_telephone_number( $value )
		{ 
			return trim( preg_replace('/\D/', '', $value ) );
		}


		/**
		 * Sanitize text value
		 * 
		 * @return      string
		 */

		public function sanitize_text( $value )
		{ 
			return sanitize_text_field( $value );
		}


		/**
		 * Sanitize textarea value
		 * 
		 * @return      string
		 */

		public function sanitize_textarea( $value )
		{ 
			return wp_kses_post( $value );
		}


		/**
		 * Sanitize time value
		 * 
		 * @return      string
		 */

		public function sanitize_time( $value )
		{ 
			return sanitize_date( $value );
		}


		/**
		 * Sanitize URL value
		 * 
		 * @return      string
		 */

		public function sanitize_url( $value )
		{ 
		//	return strip_tags( esc_url_raw( $value ) );
			return esc_url_raw( sanitize_text_field( rawurldecode( $value ) ) );
		}


		/**
		 * Add <iframe> to the list of kses() allowed tags, multisite ONLY
		 * 
		 * @return      array
		 */

		public function allow_multisite_tags( $multisite_tags )
		{ 
			global $post;

			if( is_multisite() && $post && self::POST_TYPE == get_post_type( $post->ID ) )
			{ 
				$multisite_tags['iframe'] = array( 
					'src'             => true, 
					'width'           => true, 
					'height'          => true, 
					'align'           => true, 
					'class'           => true, 
					'name'            => true, 
					'id'              => true, 
					'frameborder'     => true, 
					'seamless'        => true, 
					'srcdoc'          => true, 
					'sandbox'         => true, 
					'allowfullscreen' => true
				);

			} // endif

			return $multisite_tags;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Post_Meta();

?>