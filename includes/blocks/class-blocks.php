<?php
/**
 * Plugin-specific Blocks
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Blocks' ) )
{ 
	class Maxson_Portfolio_Projects_Blocks { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			// Skip block registration if Gutenberg is not enabled/merged.
			if( ! function_exists( 'register_block_type' ) )
			{ 
				return;

			} // endif

			add_action( 'init', array( &$this, 'init' ) );
		}


		/**
		 * Initialize block library features
		 */

		public static function init()
		{ 
			add_filter( 'block_categories', array( &$this, 'add_block_category' ) );
		}


		/**
		 * Adds a custom category to the block inserter
		 * 
		 * @param       array $categories Array of categories
		 * @return      array
		 */

		public static function add_block_category( $categories )
		{ 
			return array_merge( $categories, array(
				array(
					'slug'  => 'portfolioprojects', 
					'title' => __( 'Portfolio Projects', 'maxson' ), 
					'icon'  => 'dashicons-portfolio'
				)
			) );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Blocks();
// Maxson_Portfolio_Projects_Blocks::get_instance();

?>