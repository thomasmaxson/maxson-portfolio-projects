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


if( ! class_exists( 'Maxson_Portfolio_Projects_Block_Project_Image_Slide' ) )
{ 
	class Maxson_Portfolio_Projects_Block_Project_Image_Slide { 

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
			register_block_type( __DIR__ . '/build/carousel-slide', array( 
				'render_callback' => array( $this, 'render_block' )
			) );
		}


		/**
		 * Render custom block
		 */

		function render_block( $attributes, $content, $block_class )
		{ 
			$output = '';

			ob_start(); 
			
			?>
			
			<li <?php echo get_block_wrapper_attributes(array("class" => "splide__slide")); ?>>
				<img src="<?php echo $attributes['imageURL'] ?>" alt="">
			</li>

			<?php return ob_get_clean();
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Block_Project_Image_Slide();

?>