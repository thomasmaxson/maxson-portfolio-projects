<?php
/**
 * Plugin-specific Block
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/blocks
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Block_Project_Carousel' ) )
{ 
	class Maxson_Portfolio_Projects_Block_Project_Carousel { 

		/**
		 * Construct
		 */

		public function __construct()
		{ 
			if( ! function_exists( 'register_block_type' ) )
			{ 
				return;

			} // endif

			add_action( 'init', array( &$this, 'init' ) );

			add_action( 'enqueue_block_editor_assets', array( &$this,'assets' ) );
		}


		/**
		 * Initialize custom block
		 */

		function init()
		{ 
			register_block_type( __DIR__ . '/build/carousel', array( 
				'render_callback' => array( $this, 'render_block' )
			) );
		}


		/**
		 * Initialize custom block
		 */

		function assets()
		{ 
			wp_enqueue_script( 'maxson-portfolio-projects-carousel', plugins_url( 'editor-scripts.js', __FILE__ ), array(), '1.0', 'all' );
		}


		/**
		 * Render custom block
		 */

		function render_block( $attributes, $content, $block )
		{ 
			$output = '';

			ob_start();

			maxson_portfolio_template( 'project/gallery.php', array( 
				'block'      => $block, 
				'attributes' => $attributes
			) );

			$output .= ob_get_contents();

			ob_end_clean();

			return $output;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Block_Project_Carousel();

?>