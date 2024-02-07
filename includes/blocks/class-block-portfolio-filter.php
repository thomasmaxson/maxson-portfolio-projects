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
	if( ! class_exists( 'Maxson_Portfolio_Projects_Block', false ) )
	{ 
		include_once( 'abstract-class-block-portfolio.php' );
	
	} // endif


	class Maxson_Portfolio_Projects_Block_Portfolio_Filter extends Maxson_Portfolio_Projects_Block { 

		/**
		 * Block
		 *
		 * @var         string
		 */

		protected $block_name = __DIR__ . '/build/filter';


		/**
		 * Render custom block
		 */

		function render_block( $attributes, $content, $block )
		{ 
			$output = '';

			$classes = $this->get_block_classes( $attributes, array( 
				'portfolio-filter-variation-' . $attributes['variation']
			) );

			$blockAttrs = get_block_wrapper_attributes( array( 
				'class' => $classes
			) );

			ob_start();

			if( 'select' === $attributes['variation'] )
			{ 
				printf( '<form %1$s>', $blockAttrs );

				maxson_portfolio_template( 'portfolio-filter/select.php', array( 
					'block'      => $block, 
					'attributes' => $attributes
				) );

				echo '</form>';

			} else
			{ 
				printf( '<ul %1$s>', $blockAttrs );

				maxson_portfolio_template( 'portfolio-filter/list.php', array( 
					'block'      => $block, 
					'attributes' => $attributes
				) );

				echo '</ul>';

			} // endif

			$output .= ob_get_contents();

			ob_end_clean();

			return $output;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Block_Portfolio_Filter();

?>