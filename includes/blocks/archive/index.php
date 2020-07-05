<?php
/**
 * Gutenberg Custom Block assets
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


if( ! function_exists( 'maxson_portfolio_block_archive_scripts' ) )
{ 
	/**
	 * Enqueue the block's assets for the editor
	 * 
	 * wp-blocks  : Includes block type registration and related functions
	 * wp-element : Includes the WordPress Element abstraction for describing the structure of your blocks
	 * wp-i18n    : To internationalize the block's text
	 */

	function maxson_portfolio_block_archive_scripts()
	{ 
		$block_dependencies = array(
			'wp-api-fetch',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-editor',
			'wp-i18n',
			'wp-url'
		);

		wp_register_script( 'maxson-portfolio-block-archive-script', plugins_url( 'block.js', __FILE__ ), $block_dependencies, MAXSON_PORTFOLIO_VERSION );
	}
} // endif
add_action( 'enqueue_block_editor_assets', 'maxson_portfolio_block_archive_scripts' );


if( ! function_exists( 'maxson_portfolio_block_archive_register' ) )
{ 
	/**
	 * Register block element
	 * 
	 * @return      void
	 */

	function maxson_portfolio_block_archive_register()
	{ 
		register_block_type( 'portfolioprojects/archive', array( 
			'editor_script' => 'maxson-portfolio-block-archive-script'
		) );

	}
} // endif
add_action( 'init', 'maxson_portfolio_block_archive_register' );

?>