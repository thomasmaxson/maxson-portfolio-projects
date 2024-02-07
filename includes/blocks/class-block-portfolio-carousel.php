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
	if( ! class_exists( 'Maxson_Portfolio_Projects_Block', false ) )
	{ 
		include_once( 'abstract-class-block-portfolio.php' );
	
	} // endif


	class Maxson_Portfolio_Projects_Block_Project_Carousel extends Maxson_Portfolio_Projects_Block { 

		/**
		 * Block
		 *
		 * @var         string
		 */

		protected $block_name = __DIR__ . '/build/carousel';


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