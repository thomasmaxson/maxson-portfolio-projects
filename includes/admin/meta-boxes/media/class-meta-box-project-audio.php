<?php
/**
 * Plugin-specific audio meta box
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Project_Audio' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Project_Audio extends Maxson_Portfolio_Projects_Meta_Box_Project_Media { 

		/**
		 * The media tabs
		 */

		public $types = array();


		/**
		 * Media type setup
		 * 
		 * @return      void
		 */

		public function _setup()
		{ 
			$this->type     = 'audio';
			$this->title = __( 'Project Audio', 'maxson' );

			$this->types = apply_filters( 'maxson_portfolio_meta_box_audio_types', array( 
				'url'      => esc_html__( 'URL', 'maxson' ), 
				'advanced' => esc_html__( 'Advanced', 'maxson' )
			) );
		}


		/**
		 * Include meta box styles and scripts
		 * 
		 * @return      void
		 */

		public function _add_scripts()
		{ 
			$min            = maxson_portfolio_get_minified_suffix();
			$plugin_file    = MAXSON_PORTFOLIO_FILE;
			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			wp_register_script( 'portfolio_projects_admin_audio', plugins_url( "/admin/js/portfolio-projects-admin-audio{$min}.js", $plugin_file ), array( 'jquery' ), $plugin_version, true );

			wp_enqueue_script( 'portfolio_projects_admin_audio' );

			$localize = apply_filters( 'maxson_portfolio_meta_box_audio_params', array( 
				'audio_title'   => __( 'Add Project Audio', 'maxson' ), 
				'audio_button'  => __( 'Insert %%filetype%% Audio to Project', 'maxson' ), 

				'poster_title'  => __( 'Add Audio Poster', 'maxson' ), 
				'poster_button' => __( 'Insert Audio Poster to Project', 'maxson' ), 

				'audio_type_invalid' => _x( 'This field requires a &quot;%%filetype%%&quot; file type.', 'Project audio invalid file type error message', 'maxson' )
			) );

			wp_localize_script( 'portfolio_projects_admin_audio', 'portfolio_projects_audio_params', $localize );

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

			$type = get_post_meta( $post_id, '_audio_type', true );

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
					'content' => apply_filters( "portfolio_projects_meta_box_audio_{$this->type}_content", call_user_func( array( $this, "{$key}_meta_box" ), array( 'post_id' => $post_id ) ), $key, $post_id, $label )
				);

				$content[] = $this->_build_content( $content_args );

			} // endforeach

			?>
			<div class="project-meta-box" id="projects-audio-container">
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
			</div><!-- #project-audio-container -->
			<?php wp_nonce_field( MAXSON_PORTFOLIO_BASENAME, 'portfolio_projects_audio_meta_nonce' );
		}


		/**
		 * 
		 * 
		 * @return      void
		 */

		public function url_meta_box( $data )
		{ 
			$post_id = $data['post_id'];

			$oembed = get_post_meta( $post_id, '_audio_oembed', true );

			$audio_sources = apply_filters( 'maxson_portfolio_meta_box_audio_select_oembed_providers', array( 
				_x( 'SoundCloud', 'Audio oEmbed provider', 'maxson' ), 
				_x( 'Spotify', 'Audio oEmbed provider', 'maxson' ), 
				_x( 'Rdio', 'Audio oEmbed provider', 'maxson' )
			) );

			ob_start();

			?>
				<div class="project-content-group">
					<label class="hide-if-js" for="project-audio-oembed"><?php _e( 'oEmbed URL', 'maxson' ); ?></label>
					<input type="url" name="project_audio_oembed" class="large-text" id="project-audio-oembed" value="<?php echo esc_url( $oembed ); ?>">
					<p class="description"><?php printf( __( 'Enter %1$s or other trusted audio source URL. %2$sView trusted oEmbed providers.%3$s', 'maxson' ), join( ', ', $audio_sources ), '<a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">', '</a>' ); ?></p>

				</div><!-- .project-content-group -->

			<?php $content = ob_get_clean();

			return $content;
		}


		/**
		 * 
		 * 
		 * @return      void
		 */

		public function advanced_meta_box( $data )
		{ 
			$post_id = $data['post_id'];

			$formats = maxson_portfolio_get_audio_formats();

			$loop_options = array( 
				'off' => esc_html_x( 'Off', 'Audio loop metabox option', 'maxson' ), 
				'on'  => esc_html_x( 'On', 'Audio loop metabox option', 'maxson' )
			);

			$autoplay_options = array( 
				'off' => esc_html_x( 'Off', 'Audio autoplay metabox option', 'maxson' ), 
				'on'  => esc_html_x( 'On', 'Audio autoplay metabox option', 'maxson' )
			);

			$preload_options = array( 
				'none'     => esc_html_x( 'None', 'Audio autoplay metabox option', 'maxson' ), 
				'auto'     => esc_html_x( 'Auto', 'Audio autoplay metabox option', 'maxson' ), 
				'metadata' => esc_html_x( 'Metadata', 'Audio autoplay metabox option', 'maxson' )
			);

			$poster   = get_post_meta( $post_id, '_audio_poster', true );
			$loop     = get_post_meta( $post_id, '_audio_loop', true );
			$autoplay = get_post_meta( $post_id, '_audio_autoplay', true );
			$preload  = get_post_meta( $post_id, '_audio_preload', true );

			if( empty( $loop ) )
			{
				$loop = apply_filters( 'maxson_portfolio_meta_box_audio_advanced_loop_default', key( $loop_options ) );

			} // endif

			if( empty( $autoplay ) )
			{
				$autoplay = apply_filters( 'maxson_portfolio_meta_box_audio_advanced_autoplay_default', key( $autoplay_options ) );

			} // endif

			if( empty( $preload ) )
			{
				$preload = apply_filters( 'maxson_portfolio_meta_box_audio_advanced_preload_default', key( $preload_options ) );

			} // endif

			ob_start();

			?>
				<div class="project-content-group visible-label">
					<label><?php _e( 'Sources', 'maxson' ); ?></label>
					<?php foreach( $formats as $format )
					{ 
						$value = get_post_meta( $post_id, "_audio_{$format}", true );

						$value_url = wp_get_attachment_url( $value );

						$name = "project_audio_{$format}";

						$button = _x( "Select .{$format} audio", 'Meta field button text', 'maxson' );

						?><div class="input-group">

							<?php printf( '<label class="hide-if-js" for="%1$s">%2$s</label>', esc_attr( $name ), __( ".{$format} audio file", 'maxson' ) ); ?>

							<?php printf( '<input type="text" class="large-text project-audio-media-url" name="%1$s" id="%1$s" value="%2$s">', esc_attr( "{$name}_url" ), esc_attr( $value_url ) ); ?>
							<?php printf( '<input type="hidden" class="large-text project-audio-media-id" name="%1$s" value="%2$s">', esc_attr( $name ), esc_attr( $value ) ); ?>

							<?php echo '<span class="input-group-end hide-if-no-js">'; ?>
								<?php printf( '<input type="button" class="button project-audio-media-button" name="%1$s_button" data-media-type="audio" data-file-type="%2$s" value="%3$s">', esc_attr( "{$name}_button" ), esc_attr( $format ), esc_attr( $button ) ); ?>
							<?php echo '</span>'; ?>

						</div>

					<?php } // endforeach ?>
				</div><!-- .project-content-group -->


				<div class="project-content-group visible-label">
					<label for="project-audio-poster-url"><?php _e( 'Poster', 'maxson' ); ?></label>
					<div class="input-group">
						<input type="text" class="large-text project-audio-media-url" name="project_audio_poster_url" id="project-audio-poster-url" value="<?php echo esc_url( wp_get_attachment_url( $poster ) ); ?>">
						<input type="hidden" class="large-text project-audio-media-id" name="project_audio_poster" id="project-audio-poster" value="<?php echo esc_attr( $poster ); ?>">
						<span class="input-group-end hide-if-no-js">
							<input type="button" class="button project-audio-media-button" name="project_audio_poster_button" data-media-type="image" value="<?php esc_attr_e( 'Select audio image', 'maxson' ); ?>">
						</span>

					</div><!-- .input-group -->
				</div><!-- .project-content-group -->


				<div class="project-content-group visible-label">
					<label><?php _e( 'Loop', 'maxson' ); ?></label>
					<ul class="project-inline-radio-buttons">
						<?php foreach( $loop_options as $key => $value )
						{
							$checked = checked( $key, $loop, false );

							$id = sprintf( 'project-audio-loop-%1$s', $key );

							printf( '<li><input type="radio" name="project_audio_loop" id="%2$s" value="%1$s"%3$s><label for="%2$s">%4$s</label></li>', esc_attr( $key ), esc_attr( $id ), $checked, esc_attr( $value ) );

						} // endforeach ?>
					</ul>
				</div><!-- .project-content-group -->


				<div class="project-content-group visible-label">
					<label><?php _e( 'Autoplay', 'maxson' ); ?></label>
					<ul class="project-inline-radio-buttons">
						<?php foreach( $autoplay_options as $key => $value )
						{
							$checked = checked( $key, $autoplay, false );

							$id = sprintf( 'project-audio-autoplay-%1$s', $key );

							printf( '<li><input type="radio" name="project_audio_autoplay" id="%2$s" value="%1$s"%3$s><label for="%2$s">%4$s</label></li>', esc_attr( $key ), esc_attr( $id ), $checked, esc_attr( $value ) );

						} // endforeach ?>
					</ul>
				</div><!-- .project-content-group -->


				<div class="project-content-group visible-label">
					<label><?php _e( 'Preload', 'maxson' ); ?></label>
					<ul class="project-inline-radio-buttons">
						<?php foreach( $preload_options as $key => $value )
						{
							$checked = checked( $key, $preload, false );

							$id = sprintf( 'project-audio-preload-%1$s', $key );

							printf( '<li><input type="radio" name="project_audio_preload" id="%2$s" value="%1$s"%3$s><label for="%2$s">%4$s</label></li>', esc_attr( $key ), esc_attr( $id ), $checked, esc_attr( $value ) );

						} // endforeach ?>
					</ul>
				</div><!-- .project-content-group -->

			<?php $content = ob_get_clean();

			return $content;
		}


		/**
		 * Save meta box data
		 * 
		 * @return      void
		 */

		public function _save( $post_id, $post )
		{ 
			if( isset( $_POST['portfolio_projects_audio_meta_nonce'] ) )
			{ 
				if( ! wp_verify_nonce( $_POST['portfolio_projects_audio_meta_nonce'], MAXSON_PORTFOLIO_BASENAME ) )
					return;

				if( isset( $_POST['project_audio_type'] ) )
				{ 
					update_post_meta( $post_id, '_audio_type', $_POST['project_audio_type'] );

				} else
				{
					delete_post_meta( $post_id, '_audio_type' );

				} // endif

				update_post_meta( $post_id, '_audio_oembed', $_POST['project_audio_oembed'] );


				$poster_id = ( isset( $_POST['project_audio_poster'] ) && wp_attachment_is_image( $_POST['project_audio_poster'] ) ) ? trim( $_POST['project_audio_poster'] ) : '';

				if( $poster_id )
				{ 
					update_post_meta( $post_id, '_audio_poster', $poster_id );

				} else
				{ 
					delete_post_meta( $post_id, '_audio_poster' );

				} // endif

				update_post_meta( $post_id, '_audio_loop', $_POST['project_audio_loop'] );
				update_post_meta( $post_id, '_audio_autoplay', $_POST['project_audio_autoplay'] );
				update_post_meta( $post_id, '_audio_preload', $_POST['project_audio_preload'] );

				$formats = maxson_portfolio_get_audio_formats();

				if( ! empty( $formats ) && is_array( $formats ) )
				{ 
					foreach( $formats as $format )
					{ 
						$audio_file_id = trim( $_POST["project_audio_{$format}"] );

						if( wp_attachment_is( 'audio', $audio_file_id ) )
						{ 
							update_post_meta( $post_id, "_audio_{$format}", $audio_file_id );

						} else
						{ 
							delete_post_meta( $post_id, "_audio_{$format}" );

						} // endif

					} // endforeach
				} // endif

				do_action( 'maxson_portfolio_save_meta_box_audio', $post_id, $post );

			} // endif
		}

	} // endclass
} // endif


return new Maxson_Portfolio_Projects_Meta_Box_Project_Audio();
?>