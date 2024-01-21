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
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue' ) );
		}


		/**
		 * Enqueue scripts
		 * 
		 * @return      void
		 */

		public function enqueue()
		{ 
			wp_register_script( 'maxson-portfolio-isotope', MAXSON_PORTFOLIO_PLUGIN_URL . 'assets/js/isotope/isotope.pkgd.js', array( 'jquery' ), '2.2.0', true );
		//	wp_register_script( 'maxson-portfolio-images-loaded', MAXSON_PORTFOLIO_PLUGIN_URL . 'assets/js/imagesloaded/imagesloaded.pkgd.js', array( 'jquery' ), '3.1.8', true );

			wp_register_script( 'maxson-portfolio', MAXSON_PORTFOLIO_PLUGIN_URL . 'assets/js/portfolio-grid.js', array( 'jquery', 'maxson-portfolio-isotope' ), '1.0', true );


			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'maxson-portfolio-isotope' );
		//	wp_enqueue_script( 'maxson-portfolio-images-loaded' );
			wp_enqueue_script( 'maxson-portfolio' );


			// Modify the jQuery selectors, in case you want a custom HTML output
			$selectors = apply_filters( 'maxson_portfolio_asset_jquery_selectors', array( 
				'portfolio_grid'    => '.portfolio-grid', 
				'portfolio_filters' => '.portfolio-grid--filters', 
				'portfolio_teasers' => '.portfolio-grid--teasers', 
				'project_teaser'    => '.project-teaser'
			) );

			// There shou not be a need to modify this, but you're allowed to because we're nice
			$data = apply_filters( 'maxson_portfolio_asset_parameters', array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ), 
				'selector' => $selectors
			) );

			wp_localize_script( 'maxson-portfolio', 'MaxsonPortfolioParams', $data );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Assets();

?>