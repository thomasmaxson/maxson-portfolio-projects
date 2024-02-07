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


abstract class Maxson_Portfolio_Projects_Block { 

	/**
	 * Block
	 *
	 * @var         string
	 */

	protected $block_name = '';


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
		if( '' !== $this->block_name )
		{ 
			register_block_type( $this->block_name, array( 
				'render_callback' => array( $this, 'render_block' )
			) );

		} // endif
	}


	/**
	 * Retrieve block wrapper classes
	 */

	function get_block_classes( $attributes, $classes )
	{ 
		if( empty( $classes ) )
		{ 
			$classes = [];

		} // endif

		if( isset( $attributes['className'] ) && $attributes['className'] )
		{ 
			array_push( $classes, $attributes['className'] );

		} // endif

		return join( ' ', $classes );
	}


	/**
	 * Render custom block
	 */

	function render_block( $attributes, $content, $block )
	{ 
		return '';
	}

} // endclass

?>