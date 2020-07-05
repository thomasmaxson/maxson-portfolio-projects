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


class Maxson_Portfolio_Projects_Cat_Dropdown_Walker extends Walker { 

	var $tree_type = 'category';

	var $db_fields = array( 
		'parent' => 'parent', 
		'id'     => 'term_id', 
		'slug'   => 'slug'
	);

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
		extract( $args );

		$pad = $hierarchical ? str_repeat( ' ', $depth * 3 ) : '';

		$cat_name = apply_filters( 'list_product_cats', $cat->name, $cat );

		$name = "{$pad}{$cat_name}";

		$term = isset( $args['term_type'] ) && $args['term_type'] == 'slug' ? $cat->slug : $cat->term_id;

		$selected = selected( $selected, $term, false );

		$count = $show_count ? sprintf( '(%s)', number_format_i18n( $cat->count ) ) : '';

		$output .= "\t" . sprintf( '<option class="level-%1$s" value="%2$s"%3$s>%4$s %5$s</option>', $depth, $term, $selected, $name, $count );
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