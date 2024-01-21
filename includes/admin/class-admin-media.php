<?php
/**
 * Plugin-specific Admin Media
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Media_Settings' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Media_Settings { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'admin_init', array( &$this, 'register' ) );
			add_filter( 'upload_dir', array( &$this, 'upload_dir' ) );

			$this->create_directory();
		}


		/**
		 * Media image settings
		 * 
		 * @return 		void
		 */

		public function register()
		{ 
			// https://codex.wordpress.org/Roles_and_Capabilities#upload_files
			if( current_user_can( 'upload_files' ) )
			{ 
				$tab     = 'media';
				$section = 'section';

				add_settings_section( "maxson_portfolio_{$tab}_{$section}", 
					__( 'Portfolio image sizes', 'maxson' ), array( &$this, 'description' ), $tab );

				register_setting( $tab, 'maxson_portfolio_media_size_thumbnail_width' );
				register_setting( $tab, 'maxson_portfolio_media_size_thumbnail_height' );
				register_setting( $tab, 'maxson_portfolio_media_thumbnail_crop' );

				$thumbnail_label = apply_filters( 'maxson_portfolio_media_thumbnail_label', _x( 'Project thumbnail size', 'Media setting label', 'maxson' ) );

				add_settings_field( "maxson_portfolio_setting_{$tab}_thumbnail_image", $thumbnail_label, 
					array( &$this, 'media_option' ), $tab, "maxson_portfolio_{$tab}_{$section}", array( 
						'size' => 'thumbnail'
					)
				);


				register_setting( $tab, 'maxson_portfolio_media_size_medium_width' );
				register_setting( $tab, 'maxson_portfolio_media_medium_height' );
				register_setting( $tab, 'maxson_portfolio_media_size_medium_crop' );

				$medium_label = apply_filters( 'maxson_portfolio_media_medium_label', _x( 'Project medium size', 'Media setting label', 'maxson' ) );

				add_settings_field( "maxson_portfolio_setting_{$tab}_medium_image", $medium_label, 
					array( &$this, 'media_option' ), $tab, "maxson_portfolio_{$tab}_{$section}", array( 
						'size' => 'medium'
					)
				);


				register_setting( $tab, 'maxson_portfolio_media_size_large_width' );
				register_setting( $tab, 'maxson_portfolio_media_size_large_height' );
				register_setting( $tab, 'maxson_portfolio_media_large_crop' );

				$large_label = apply_filters( 'maxson_portfolio_media_large_label', _x( 'Project large size', 'Media setting label', 'maxson' ) );

				add_settings_field( "maxson_portfolio_setting_{$tab}_large_image", $large_label, 
					array( &$this, 'media_option' ), $tab, "maxson_portfolio_{$tab}_{$section}", array( 
						'size' => 'large'
					)
				);

			} // endif
		}


		/** 
		 * Media image description
		 * 
		 * @return 		string
		 */

		public function description()
		{ 
			echo wpautop( wptexturize( wp_kses_post( __( 'These settings affect the actual dimensions of images for portfolio projects &ndash; the display on the front-end will still be affected by CSS styles.', 'maxson' ) ) ) );
		}


		/** 
		 * Media image fields
		 * 
		 * @return 		string
		 */

		public function media_option( $args )
		{ 
			extract( $args );

			if( 'thumbnail' === strtolower( $size ) )
			{ 
				$width_label  = __( 'Width', 'maxson' );
				$height_label = __( 'Height', 'maxson' );
				$show_crop    = true;

			} else
			{ 
				$width_label  = __( 'Max Width', 'maxson' );
				$height_label = __( 'Max Height', 'maxson' );
				$show_crop    = false;

			} // endif

			$has_filter = ( has_filter( "maxson_portfolio_media_{$size}_image_size" ) ) ? true : false;

			$name_w = "maxson_portfolio_media_size_{$size}_width";
			$name_h = "maxson_portfolio_media_size_{$size}_height";
			$name_c = "maxson_portfolio_media_{$size}_crop";
			$value = maxson_portfolio_get_media_sizes( $size );

			$is_disabled = disabled( $has_filter, true, false );

			$width  = ( isset( $value['width'] ) )  ? $value['width']  : false;
			$height = ( isset( $value['height'] ) ) ? $value['height'] : false;

			echo '<fieldset>';
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $name_w ), $width_label );
			echo ' ';
			printf( '<input type="number" name="%1$s" id="%1$s" class="small-text" value="%2$s" step="1" min="0"%3$s>', esc_attr( $name_w ), esc_attr( $width ), $is_disabled );

			echo '<br>';

			echo ' ';
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $name_h ), $height_label );
			echo ' ';
			printf( '<input type="number" name="%1$s" id="%1$s" class="small-text" value="%2$s" step="1" min="0"%3$s>', esc_attr( $name_h ), esc_attr( $height ), $is_disabled );
			echo '</fieldset>';

			if( $show_crop )
			{ 
				$crop = ( isset( $value['crop'] ) )   ? $value['crop']   : false;

				$is_checked = checked( 1, $crop, false );

				printf( '<input type="checkbox" name="%1$s" id="%2$s" value="1"%3$s%4$s><label for="%2$s">%5$s</label>', esc_attr( $name_c ), esc_attr( $name_c ), $is_checked, $is_disabled, __( "Crop project {$size} image to exact dimensions (normally {$size} images are proportional)", 'maxson' ) );

			} // endif

			if( $has_filter )
			{ 
				printf( '<p class="description">%1$s</p>', __( 'This portfolio image size has been disabled because its values are being overwritten by a filter.', 'maxson' ) );

			} // endif
		}


		/** 
		 * Change Upload Directory for plugin specific post-type
		 * 
		 * @param       array $dir
		 * @return      array
		 */

		public function upload_dir( $dir )
		{ 
			if( isset( $_REQUEST['action'] ) && 'upload-attachment' == $_REQUEST['action'] )
			{ 
				if( isset( $_REQUEST['post_id'] ) )
				{ 
					$post_parent_id = get_post( $_REQUEST['post_id'] )->post_parent;

					if( self::POST_TYPE == get_post_type( $_REQUEST['post_id'] ) || self::POST_TYPE == get_post_type( $post_parent_id ) )
					{ 
						$upload_folder = Portfolio_Projects()->upload_folder();

						if( empty( $dir['subdir'] ) )
						{ 
							$dir['path']   = path_join( $dir['basedir'], $upload_folder );
							$dir['url']    = path_join( $dir['baseurl'], $upload_folder );
							$dir['subdir'] = "/{$upload_folder}";

						} else
						{ 
							$new_subdir = "/{$upload_folder}{$dir['subdir']}";

							$dir['path']   = str_replace( $dir['subdir'], $new_subdir, $dir['path'] );
							$dir['url']    = str_replace( $dir['subdir'], $new_subdir, $dir['url'] );
							$dir['subdir'] = str_replace( $dir['subdir'], $new_subdir, $dir['subdir'] );

						} // endif
					} // endif
				} // endif
			} // endif

			return $dir;
		}


		/**
		 * Install directories and files for uploading files
		 * 
		 * @return      void
		 */

		private function create_directory()
		{ 
			$wp_upload_dir = wp_upload_dir();
			$upload_folder = Portfolio_Projects()->upload_folder();

			$files = array( 
				array( 
					'base'    => trailingslashit( $wp_upload_dir['basedir'] ) . $upload_folder, 
					'file'    => 'index.php', 
					'content' => "<?php \r\r// Silence is Golden\r\r?>"
				)
			);

			foreach( $files as $file )
			{ 
				if( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) )
				{ 
					if( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) )
					{ 
						fwrite( $file_handle, $file['content'] );
						fclose( $file_handle );

					} // endif
				} // endif
			} // endforeach
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Media_Settings();

?>