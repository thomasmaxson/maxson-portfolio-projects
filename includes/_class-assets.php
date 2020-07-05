<?php
/**
 * Plugin-specific assets
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Assets' ) )
{ 
	class Maxson_Portfolio_Projects_Assets { 

		/**
		 * Contains an array of script handles registered
		 * 
		 * @var         array
		 */

		private $scripts = array();


		/**
		 * Contains an array of script handles to be localized
		 * 
		 * @var array
		 */

		private $localize_scripts = array();


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'wp_enqueue_scripts', array( &$this, 'styles' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
		}


		/**
		 * Enqueue styles
		 * 
		 * @return      void
		 */

		public function styles()
		{ 
			$plugin_file    = MAXSON_PORTFOLIO_FILE;
			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			if( maxson_portfolio_can_load_css() )
			{ 
				wp_register_style( 'portfolio_projects', plugins_url( '/assets/css/portfolio-projects.css', $plugin_file ), array(), $plugin_version, 'all' );

				wp_enqueue_style( 'portfolio_projects' );


				if( is_project() )
				{ 
					wp_register_style( 'flexslider', plugins_url( '/assets/css/flexslider/flexslider.css', $plugin_file ), array( 'portfolio_projects' ), '2.5.0', 'all' );

					wp_enqueue_style( 'flexslider' );


					if( maxson_portfolio_get_option( 'single', 'lightbox' ) )
					{ 
						wp_register_style( 'swipebox', plugins_url( '/assets/css/swipebox/swipebox.css', $plugin_file ), array( 'portfolio_projects' ), '1.4.1', 'all' );

						wp_enqueue_style( 'swipebox' );

					} // endif
				} // endif
			} // endif
		}


		/**
		 * Enqueue scripts
		 * 
		 * @return      void
		 */

		public function scripts( $hook )
		{ 
			$plugin_file    = MAXSON_PORTFOLIO_FILE;
			$plugin_version = MAXSON_PORTFOLIO_VERSION;

			if( maxson_portfolio_can_load_js() )
			{ 
				$min = maxson_portfolio_get_minified_suffix();

				wp_enqueue_script( 'jquery' );

				wp_register_script( 'images_loaded', plugins_url( "/assets/js/imagesloaded/imagesloaded.pkgd{$min}.js", $plugin_file ), array( 'jquery' ), '3.1.8' );

				wp_enqueue_script( 'images_loaded' );


				if( is_portfolio_archive() )
				{ 
					if( maxson_portfolio_get_option( 'archive_filter' ) )
					{ 
						wp_register_script( 'isotope', plugins_url( "/assets/js/isotope/isotope.pkgd{$min}.js", $plugin_file ), array( 'jquery' ), '2.2.0' );
						wp_register_script( 'isotope_init', plugins_url( "/assets/js/isotope/isotope.init{$min}.js", $plugin_file ), array( 'jquery', 'isotope' ), $plugin_version );

						wp_enqueue_script( 'isotope' );
						wp_enqueue_script( 'isotope_init' );

					} // endif
				} elseif( is_project() )
				{ 
					wp_enqueue_script( 'flexslider', plugins_url( "/assets/js/flexslider/jquery.flexslider{$min}.js", $plugin_file ), array( 'jquery' ), '2.5.0' );
					wp_enqueue_script( 'flexslider_init', plugins_url( "/assets/js/flexslider/jquery.flexslider.init{$min}.js", $plugin_file ), array( 'jquery', 'flexslider' ), $plugin_version );

					wp_enqueue_script( 'flexslider' );
					wp_enqueue_script( 'flexslider_init' );

					$flexslider_data = array( 
						'labelNext' => __( 'Next', 'maxson' ), 
						'labelPrev' => __( 'Previous', 'maxson' )
					);

					wp_localize_script( 'flexslider_init', 'flexslider_params', $flexslider_data );


					if( maxson_portfolio_get_option( 'single_lightbox' ) )
					{ 
						wp_enqueue_script( 'swipebox', plugins_url( "/assets/js/swipebox/jquery.swipebox{$min}.js", $plugin_file ), array( 'jquery' ), '1.4.1' );
						wp_enqueue_script( 'swipebox_init', plugins_url( "/assets/js/swipebox/jquery.swipebox.init{$min}.js", $plugin_file ), array( 'jquery', 'swipebox' ), $plugin_version );

						wp_enqueue_script( 'swipebox' );
						wp_enqueue_script( 'swipebox_init' );

					} // endif
				} // endif
			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Assets();

?>