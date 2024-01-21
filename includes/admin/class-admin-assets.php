<?php
/**
 * Admin Assets
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Assets' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Assets { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_admin_scripts' ) );

			add_action( 'print_media_templates', array( &$this, 'media_gallery_styles' ) );
		}


		/**
		 * Get styles for the frontend.
		 * 
		 * @return      array
		 */

		public function load_admin_styles()
		{ 
			$min            = '.min'; // maxson_portfolio_get_minified_suffix();
			$plugin_file    = MAXSON_PORTFOLIO_FILE;
			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			wp_register_style( 'maxson-portfolio-jquery-ui',            plugins_url( "/admin/css/jquery-ui{$min}.css", $plugin_file ), array(), '1.9.2', 'all' );
			wp_register_style( 'maxson-portfolio-jquery-ui-datepicker', plugins_url( "/admin/css/jquery-ui-datepicker{$min}.css", $plugin_file ), array( 'maxson-portfolio-jquery-ui' ), $plugin_version, 'all' );
			wp_register_style( 'maxson-portfolio-chosen',               plugins_url( "/admin/css/chosen{$min}.css", $plugin_file ), array(), '1.4.0', 'all' );
			wp_register_style( 'maxson-portfolio-tiptip',               plugins_url( "/admin/css/tiptip{$min}.css", $plugin_file ), array(), '1.3', 'all' );
			wp_register_style( 'maxson-portfolio-admin',                plugins_url( '/admin/css/portfolio-projects-admin.min.css', $plugin_file ), array( 'maxson-portfolio-jquery-ui', 'maxson-portfolio-jquery-ui-datepicker', 'maxson-portfolio-chosen', 'maxson-portfolio-tiptip' ), $plugin_version, 'all' );

			wp_enqueue_style( 'maxson-portfolio-jquery-ui' );
			wp_enqueue_style( 'maxson-portfolio-jquery-ui-datepicker' );

			wp_enqueue_style( 'maxson-portfolio-chosen' );
			wp_enqueue_style( 'maxson-portfolio-tiptip' );
			wp_enqueue_style( 'maxson-portfolio-admin' );
		}


		/**
		 * Enqueue scripts
		 * 
		 * @return      void
		 */

		public function load_admin_scripts( $hook )
		{ 
			global $wp_locale;

			$min            = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$plugin_file    = MAXSON_PORTFOLIO_FILE;
			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			$screen = get_current_screen();

			wp_register_script( 'maxson-portfolio-quick-edit', plugins_url( "admin/js/portfolio-projects-admin-quick-edit{$min}.js", $plugin_file ), array( 'jquery', 'inline-edit-post' ), $plugin_version, true );

			wp_register_script( 'maxson-portfolio-chosen',     plugins_url( "/admin/js/jquery.chosen.js", $plugin_file ), array( 'jquery' ), '1.4.0', true );
			wp_register_script( 'maxson-portfolio-tiptip',     plugins_url( "/admin/js/jquery.tiptip.js", $plugin_file ), array( 'jquery' ), '1.3', true );

			wp_register_script( 'maxson-portfolio-admin',      plugins_url( "/admin/js/portfolio-projects-admin{$min}.js", $plugin_file ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'maxson-portfolio-chosen', 'maxson-portfolio-tiptip' ), $plugin_version, true );

		//	wp_register_script( 'maxson-portfolio-edit-image',      plugins_url( "/admin/js/portfolio-projects-admin-edit-image.js", $plugin_file ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'maxson-portfolio-chosen', 'maxson-portfolio-tiptip' ), $plugin_version, true );

			/*if( in_array( $screen->id, maxson_portfolio_get_admin_screen_ids() ) )
			{ 
				if( function_exists( 'wp_enqueue_media' ) )
				{ 
					wp_enqueue_media();

				} // endif

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );

				if( 'edit.php' == $hook )
				{ 
					wp_enqueue_script( 'maxson-portfolio-quick-edit' );

				} // endif

				wp_enqueue_script( 'maxson-portfolio-chosen' );
				wp_enqueue_script( 'maxson-portfolio-tiptip' );
				wp_enqueue_script( 'maxson-portfolio-admin' );
				//	wp_enqueue_script( 'maxson-portfolio-edit-image' );


				$chosen_params = apply_filters( 'maxson_portfolio_admin_params', array( 
					'multiple_text'  => __( 'Select Projects', 'maxson' ), 
					'single_text'    => __( 'Select a project', 'maxson' ), 
					'no_result_text' => __( 'No results match', 'maxson' ), 
					'placeholder_text_multiple' => __( 'Select Projects', 'maxson' )
				) );

				wp_localize_script( 'maxson-portfolio-chosen', 'maxson_portfolio_chosen_params', $chosen_params );


				$admin_params = apply_filters( 'maxson_portfolio_admin_params', array( 
					'i18n_taxonomy_term_image_title'   => __( 'Choose an image', 'maxson' ), 
					'i18n_taxonomy_term_image_button'  => __( 'Use image', 'maxson' ), 
					'taxonomy_term_image_default' => maxson_portfolio_placeholder_image_src(), 

					'showButtonPanel'  => false, 
					'i18n_closeText'   => _x( 'Done', 'jQuery UI datepicker param', 'maxson' ), 
					'i18n_nextText'    => _x( 'Next', 'jQuery UI datepicker param', 'maxson' ), 
					'i18n_prevText'    => _x( 'Prev', 'jQuery UI datepicker param', 'maxson' ), 
					'i18n_currentText' => _x( 'Today', 'jQuery UI datepicker param', 'maxson' ), 
				//	'i18n_monthStatus' => _x( 'Show a different month', 'jQuery UI datepicker param', 'maxson' ), 
					'numberOfMonths'   => '1', 
					'monthNames'       => array_values( $wp_locale->month ), 
					'monthNamesShort'  => array_values( $wp_locale->month_abbrev ), 
					'dayNames'         => array_values( $wp_locale->weekday ), 
					'dayNamesShort'    => array_values( $wp_locale->weekday_abbrev ), 
					'dayNamesMin'      => array_values( $wp_locale->weekday_initial ), 
					'dateFormat'       => date_format_php_to_js( maxson_portfolio_get_date_format() ), 
					'timeFormat'       => time_format_php_to_js( maxson_portfolio_get_time_format() ), 
					'firstDay'         => get_option( 'start_of_week' ), 
					'isRTL'            => function_exists( 'is_rtl' ) ? is_rtl() : false, 

					'pointerNonce'           => wp_create_nonce( 'portfolio-project-dismiss-all-pointers' ), 
					'i18n_pointerCloseLabel' => _x( 'Close', 'Pointer close button', 'maxson' ), 

					'i18n_messageUnsavedChanges' => __( 'There are unsaved changes that will be lost if you leave. Proceed?', 'maxson' )
				) );

				// Cannot override, sorry
				$admin_params['altFormat'] = 'yy-mm-dd';

				wp_localize_script( 'maxson-portfolio-admin', 'maxson_portfolio_admin_params', $admin_params );

			} // endif*/
		}


		/**
		 * Enqueue gallery media styles
		 * 
		 * @return      void
		 */

		public function media_gallery_styles()
		{ 
			$screen = get_current_screen();

			if( ! isset( $screen->id ) || 
				$screen->base != 'post' || 
				$screen->post_type != self::POST_TYPE )
			{ 
				return;

			} // endif

			?><style type="text/css">
				.media-sidebar { display: none;}

				.attachments-browser .attachments, 
				.attachments-browser .media-toolbar { right: 0;}

				.wp-core-ui .attachment { cursor: default;}
				.wp-core-ui .attachment-preview { cursor: move;}
			</style>

			<?php 
		}


		/**
		 * Compress inline CSS
		 * 
		 * @return      string
		 */

		private function minify( $css = '' )
		{ 
			// Remove comments
			$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

			// Remove space after colons, and last semicolon
			$css = str_replace( array( ': ', ';}' ), array( ':', '}' ), $css );

			// Remove space around brackets
			$css = str_replace( array( ' { ', '{ ', ' {', ' } ', ' }', '} ' ), array( '{', '{', '{', '}', '}', '}' ), $css );

			// Remove whitespace
			$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

			return $css;
		}	

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Assets();

?>