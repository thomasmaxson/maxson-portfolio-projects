<?php
/**
 * Plugin-specific gallery meta box
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

if( class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Project_Media' ) && ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Project_Gallery' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Project_Gallery extends Maxson_Portfolio_Projects_Meta_Box_Project_Media { 

		/**
		 * Media type setup
		 * 
		 * @return      void
		 */

		public function _setup()
		{ 
			$this->type  = 'gallery';
			$this->title = __( 'Project Gallery', 'maxson' );

			$this->types = apply_filters( 'maxson_portfolio_gallery_meta_box_types', array( 
				'images' => esc_html__( 'Images', 'maxson' )
			) );
		}


		/**
		 * Include meta box styles and scripts
		 * 
		 * @return      void
		 */

		public function _add_scripts()
		{ 
			global $post;

			$min            = maxson_portfolio_get_minified_suffix();
			$plugin_file    = MAXSON_PORTFOLIO_FILE;
			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			wp_register_script( 'portfolio_projects_admin_gallery', plugins_url( "/admin/js/portfolio-projects-admin-gallery{$min}.js", $plugin_file ), array( 'jquery' ), $plugin_version, true );


		//	wp_enqueue_script( 'media' );
			wp_enqueue_media();
			wp_enqueue_script( 'portfolio_projects_admin_gallery' );

			$localize = apply_filters( 'maxson_portfolio_admin_gallery_params', array( 
				'nonce' => wp_create_nonce( 'portfolio_project_add_gallery' ), 

				'create_gallery_title' => __( 'Create Project Gallery', 'maxson' ), 
				'add_to_gallery_title' => __( 'Add to Project Gallery', 'maxson' ), 
				'edit_gallery_title'   => __( 'Edit Project Gallery', 'maxson' ), 

				'set_gallery'    => __( 'Set project gallery', 'maxson' ), 
				'saving_gallery' => __( 'Please Wait...', 'maxson' )
			) );

			wp_localize_script( 'portfolio_projects_admin_gallery', 'portfolio_projects_gallery_params', $localize );
		}


		/**
		 * Prints the details meta box content
		 * 
		 * @param 		$post The object for the current post/page
		 * @return      void
		 */

		public function _render( $post, $metabox )
		{ 
			$post_id = $post->ID;

			$tabs    = array();
			$content = array();

			$type = get_post_meta( $post_id, '_gallery_type', true );

			if( empty( $type ) || ! array_key_exists( $type, $this->types ) )
				$type = key( $this->types );

			foreach( $this->types as $key => $label )
			{ 
				$is_checked = checked( $key, $type, false );

				if( $is_checked )
				{ 
					$tab_class     = "project-tab-{$key} active";
					$content_class = 'project-tab-content active';

				} else 
				{ 
					$tab_class     = "project-tab-{$key}";
					$content_class = 'project-tab-content';

				} // endif

				$tab_args = array( 
					'key'        => $key, 
					'name'       => "project_{$this->type}_type", 
					'label'      => $label, 
					'class'      => $tab_class, 
					'is_checked' => $is_checked
				);

				$tabs[] = $this->_build_tab( $tab_args );

			
				$content_args = array( 
					'key'     => $key, 
					'class'   => $content_class, 
					'content' => apply_filters( "portfolio_projects_{$this->type}_meta_box_content", call_user_func( array( $this, "{$key}_meta_box" ), array( 'post_id' => $post_id ) ), $key, $post_id, $label )
				);

				$content[] = $this->_build_content( $content_args );

			} // endforeach

			?>
			<div class="project-meta-box project-gallery-meta-box" id="projects-gallery-container">
				<?php if( count( $tabs ) > 1 )
				{ ?>
					<div class="project-meta-box-tabs">
						<ul class="maxson-tabs project-tabs hide-if-no-js">
							<?php echo join( "\r\n", $tabs ); ?>

						</ul>
					</div><!-- .project-meta-box-tabs -->
				<?php } // endif ?>
				<div class="project-meta-box-content">
					<?php echo join( "\r\n", $content ); ?>

				</div><!-- .project-meta-box-content -->
			</div><!-- #project-gallery-container -->

			<?php wp_nonce_field( MAXSON_PORTFOLIO_BASENAME, 'portfolio_projects_gallery_meta_box_nonce' );
		}


		/**
		 * URL/oEmbed meta fields
		 * 
		 * @return      void
		 */

		private function images_meta_box( $data )
		{ 
			$post_id = $data['post_id'];

			$images_ids = get_post_meta( $post_id, '_gallery_images', true );

			if( is_array( $images_ids ) )
			{ 
				$images_ids = implode( ',', $images_ids );

			} // endif

			$button_label = ( ! empty( $images_ids ) ) ? __( 'Edit Gallery Images', 'maxson' ) : __( 'Select Gallery Images', 'maxson' );

			ob_start();

			?>
				<div class="project-content-group">
					<div class="hide-if-no-js">
						<label for="project-gallery-images" class="screen-reader-text"><?php _e( 'Gallery Images', 'maxson' ); ?></label>
						<input type="hidden" name="project_gallery_image_ids" id="project-gallery-image-ids" value="<?php echo esc_attr( $images_ids ); ?>">

						<input type="button" name="project_gallery_image_button" class="button button-default project-gallery-upload-button" id="project-gallery-upload-button" value="<?php echo esc_attr( $button_label );  ?>">

						<p class="description"><?php _e( 'Upload or edit the project gallery.', 'maxson' ); ?></p>
					</div>

					<ul id="project-gallery-images" class="project-gallery-image-thumbnails">
						<?php echo self::build_gallery_images( $images_ids ); ?>
					</ul><!-- .project-gallery-image-thumbnails -->

					<script type="text/template" id="maxson-portfolio-gallery-slide">
						<li data-attachment-id="<%- attachmentID %>"><a href="#" target="_blank" title="<%- attachmentTitle %>" class="gallery-image"><img src="<%- attachmentSrc %>"></a></li>
					</script>

				</div><!-- .project-content-group -->

			<?php

			return ob_get_clean();
		}


		/**
		 * Get gallery images
		 * 
		 * @return      void
		 */

		public static function build_gallery_images( $attachment_ids = array() )
		{ 
			$output = false;

			if( $attachment_ids )
			{ 
				if( ! is_array( $attachment_ids ) )
				{
					$attachment_ids = explode( ',', $attachment_ids );

				} // endif

				$attachment_size = apply_filters( 'maxson_portfolio_gallery_meta_box_thumbnail_size', array( 132, 132 ) );

				foreach( $attachment_ids as $attachment_id )
				{ 
					$attachment = wp_get_attachment_image( $attachment_id, $attachment_size );

					if( false !== apply_filters( 'maxson_portfolio_gallery_meta_box_thumbnail_can_edit', false ) )
					{ 
						$attachment_url = get_edit_post_link( $attachment_id );

						$attachment_title = sprintf( 'Edit: %1$s', get_the_title( $attachment_id ) );

						$output .= sprintf( '<li data-attachment-id="%1$s"><a href="%2$s" target="_blank" title="%3$s" class="gallery-image">%4$s</a></li>', $attachment_id, $attachment_url, $attachment_title, $attachment );
					} else
					{ 
						$output .= sprintf( '<li data-attachment-id="%1$s">%2$s</li>', $attachment_id, $attachment );

					} // endif
				} // endforeach
			} // endif

			return $output;
		}


		/**
		 * Save gallery images
		 * 
		 * @return      void
		 */

		public static function get_gallery_images()
		{ 
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			{
				return;

			} // endif

			if( ! current_user_can( 'edit_posts' ) )
			{ 
				wp_send_json_error( array( 
					'message' => _x( 'You do not have sufficient permissions to do this action.', 'Portfolio AJAX error message', 'maxson' )
				) );

			} // endif


			if( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'portfolio_project_add_gallery' ) )
			{ 
				wp_send_json_error( array( 
					'message' => _x( 'WordPress AJAX validation failed.', 'Portfolio AJAX error message', 'maxson' )
				) );

			} // endif

			if( ! isset( $_POST['ids'] ) )
				return;
			
			$images_ids = array_map( 'sanitize_text_field', $_POST['ids'] );
			$images     = self::build_gallery_images( $images_ids );

			wp_send_json_success( array( 
				'images_ids'   => $images_ids, 
				'gallery_html' => $images
			) );
		}


		/**
		 * Save meta box
		 * 
		 * @return      void
		 */

		public function _save( $post_id, $post )
		{ 
			if( isset( $_POST['portfolio_projects_gallery_meta_box_nonce'] ) )
			{ 
				if( ! wp_verify_nonce( $_POST['portfolio_projects_gallery_meta_box_nonce'], MAXSON_PORTFOLIO_BASENAME ) )
					return;

				if( isset( $_POST['project_gallery_type'] ) )
				{ 
					update_post_meta( $post_id, '_gallery_type', $_POST['project_gallery_type'] );

				} else
				{
					delete_post_meta( $post_id, '_gallery_type' );

				} // endif

				if( isset( $_POST['project_gallery_image_ids'] ) )
				{ 
					$attachment_ids = trim( $_POST['project_gallery_image_ids'] );

					if( ! is_array( $attachment_ids ) )
					{ 
						$attachment_ids = explode( ',', $attachment_ids );

					} // endif

					$attachment_ids = wp_parse_id_list( $_POST['project_gallery_image_ids'] );
					$attachment_ids = array_filter( $attachment_ids, 'wp_attachment_is_image' );

					update_post_meta( $post_id, '_gallery_images', $attachment_ids );

				} else
				{ 
					delete_post_meta( $post_id, '_gallery_images' );

				} // endif

				do_action( 'maxson_portfolio_save_gallery_meta_box', $post_id, $post );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Meta_Box_Project_Gallery();

?>