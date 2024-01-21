<?php
/**
 * Plugin-specific Archive Block
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/blocks/archive
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Block_Archive' ) )
{ 
	class Maxson_Portfolio_Projects_Block_Archive { 

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
			add_action( 'enqueue_block_editor_assets', array( &$this, 'scripts' ) );
		}


		/**
		 * Initialize Gutenberg block
		 */

		public static function register()
		{ 
			register_block_type( 'maxson-portfolio-projects/archive', array( 
				'editor_script' => 'maxson-portfolio-block-archive', 
				'editor_style'  => 'maxson-portfolio-block-archive'
			) );
		}


		/**
		 * Initialize Gutenberg block
		 */

		public static function scripts()
		{ 
			// Ensure Gutenberg is active.
			if( function_exists( 'register_block_type' ) )
			{ 
				// Enqueue the JavaScript file for the block.
				wp_enqueue_script( 'maxson-portfolio-block-archive', plugins_url( 'build/archive.js', __FILE__ ),  array( 'wp-blocks', 'wp-editor' ), 
				filemtime( plugin_dir_path( __FILE__ ) . 'build/archive.js' ) );

				// // Enqueue the CSS file for the block.
				// wp_enqueue_style( 'maxson-portfolio-block-archive', plugins_url( 'build/archive.js', __FILE__ ), array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . 'build/archive.css' ) );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Block_Archive();

?>