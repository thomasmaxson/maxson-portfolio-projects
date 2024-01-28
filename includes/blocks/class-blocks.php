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
		 * Construct
		 */

		public function __construct()
		{ 
			if( ! function_exists( 'register_block_type' ) )
			{ 
				return;

			} // endif

			add_action( 'init', array( &$this, 'register' ) );
		}


		/**
		 * 
		 */

		public static function register()
		{ 
			add_filter( 'block_categories_all', array( __CLASS__, 'add_category' ) );
		}


		/**
		 * Adds a custom category to the block inserter
		 * 
		 * @param       array $categories Array of categories
		 * @return      array
		 */

		public static function add_category( $categories )
		{ 
			array_push( $categories, array( 
				'slug'  => 'portfolioprojects', 
				'title' => __( 'Portfolio Projects', 'maxson' ), 
				'icon'  => 'dashicons-portfolio'
			) );

			return $categories;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Blocks();

?>