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


if( ! class_exists( 'Maxson_Portfolio_Projects_Block_Portfolio_Filter' ) )
{ 
	class Maxson_Portfolio_Projects_Block_Portfolio_Filter { 

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
		}


		/**
		 * Initialize custom block
		 */

		function init()
		{ 
			register_block_type( __DIR__ . '/build/filter', array( 
				'render_callback' => array( $this, 'render_block' )
			) );
		}


		/**
		 * Render custom block
		 */

		function render_block( $attributes, $content, $block )
		{ 
			$output = sprintf( '<p>%1$s</p>', __( 'Portfolio filter gets placed here.', 'maxson' ) );

			return $output;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Block_Portfolio_Filter();

?>