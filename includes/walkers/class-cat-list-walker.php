<?php
/**
 * Plugin-specific walker class
 *
 * @extends 	Walker
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/walker
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


class Maxson_Portfolio_Projects_Cat_List_Walker extends Walker { 

	var $tree_type = 'category';

	var $db_fields = array( 
		'parent' => 'parent', 
		'id'     => 'term_id', 
		'slug'   => 'slug'
	);

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */

	public function start_lvl( &$output, $depth = 0, $args = array() ){ 
		if( 'list' != $args['style'] )
			return;

		$indent = str_repeat( "\t", $depth );

		$output .= $indent . '<ul class="children">' . "\n";
	}


	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */

	public function end_lvl( &$output, $depth = 0, $args = array() )
	{ 
		if( 'list' != $args['style'] )
		{
			return;

		} // endif

		$indent = str_repeat( "\t", $depth );

		$output .= $indent . '</ul>' . "\n";
	}


	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category in reference to parents.
	 * @param integer $current_object_id
	 */

	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 )
	{ 
		$classes = array( 'cat-item' );

		$classes[] = sprintf( 'cat-item-%1$s', $cat->term_id );

		if( $args['current_category'] == $cat->term_id )
		{ 
			$classes[] = 'current-cat';

		} // endif


		if( $args['has_children'] && $args['hierarchical'] )
		{ 
			$classes[] = 'cat-parent';

		} // endif


		if( $args['current_category_ancestors'] && $args['current_category'] && in_array( $cat->term_id, $args['current_category_ancestors'] ) ) 
		{ 
			$classes[] = 'current-cat-parent';

		} // endif


		$term = get_term_link( (int) $cat->term_id, 'portfolio_project_category' );

		$output .=  sprintf( '<li class="%1$s"><a href="%2$s">%3$s</a>', join( ' ', $classes ), $term, $cat->name );

		if( $args['show_count'] )
		{ 
			$output .= sprintf( ' <span class="count">(%1$s)</span>', $cat->count );

		} // endif
	}


	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Not used.
	 * @param array $args Only uses 'list' for whether should append to output.
	 */

	public function end_el( &$output, $cat, $depth = 0, $args = array() )
	{ 
		$output .= '</li>' . "\n";
	}


	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */

	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output )
	{ 
		if( ! $element || 0 === $element->count )
		{ 
			return;

		} // endif

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
} // endclass

?>