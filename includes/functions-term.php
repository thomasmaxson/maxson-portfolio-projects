<?php
/**
 * Plugin-specific term functions
 * 
 * @author 		Thomas Maxson
 * @package 	Maxson_Portfolio/includes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'maxson_portfolio_get_term_type' ) )
{ 
	/**
	 * Get the term type.
	 * 
	 * @param       int $term_id (optional)
	 * @return      string|bool
	 */

	function maxson_portfolio_get_term_type( $term_id = null )
	{ 
		if( is_null( $term_id ) || empty( $term_id ) )
		{ 
			return false;

		} // endif

		return get_term_meta( $term_id, '_project_type', true );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_list_categories' ) )
{ 
	/**
	 * Get the HTML list of plugin-specific categories.
	 * 
	 * @see         wp_list_categories()
	 * 
	 * @param       string|array $args (optional) Override default arguments.
	 * @return      string
	 */

	function maxson_portfolio_list_categories( $args = array() )
	{ 
		$defaults = array( 
			'taxonomy' => 'portfolio_category'
		);

		$args = wp_parse_args( $args, $defaults );

		wp_list_categories( $args );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_dropdown_categories' ) )
{ 
	/**
	 * Get the dropdown list of plugin-specific categories.
	 * 
	 * @see         wp_dropdown_categories()
	 * 
	 * @param       string|array $args (optional) Override default arguments.
	 * @return      string
	 */

	function maxson_portfolio_dropdown_categories( $args = array() )
	{ 
		$defaults = array( 
			'taxonomy' => 'portfolio_category'
		);

		$args = wp_parse_args( $args, $defaults );

		wp_dropdown_categories( $args );
	}
} // endif


if( ! function_exists( 'maxson_portfolio_get_project_term_list' ) )
{ 
	/**
	 * Returns an HTML string of taxonomy terms associated with a post and given taxonomy
	 * 
	 * @see         get_the_term_list()
	 * 
	 * @param       string|array $args (optional) Override default arguments.
	 * @return      string
	 */

	function maxson_portfolio_get_project_term_list( $post_id = null, $args = array() )
	{ 
		if( null === $post_id )
			return false;

		$defaults = array( 
			'taxonomy'  => 'portfolio_category', 
			'separator' => ', ', 
			'before'    => '', 
			'after'     => '', 
			'link'      => true
		);

		$args = wp_parse_args( $args, $defaults );

		$output = false;

		if( $args['link'] )
		{ 
			$output = get_the_term_list( $post_id, $args['taxonomy'], $args['before'], $args['separator'], $args['after'] );

		} else
		{ 
			$terms = wp_get_object_terms( $post_id, $args['taxonomy'] );

			if( ! empty( $terms ) && ! is_wp_error( $terms ) )
			{ 
				$items = array();

				foreach( $terms as $term )
				{ 
					$items[] = esc_html( $term->name );

				} // endforeach

				$output = $args['before'] . join( $args['separator'], $items ) . $args['after'];

			} // endif
		} // endif

		return $output;
	}
} // endif

if( ! function_exists( 'maxson_portfolio_the_project_term_list' ) )
{ 
	function maxson_portfolio_the_project_term_list( $post_id = null, $args = array() )
	{ 
		echo maxson_portfolio_get_project_term_list( $post_id, $args );
	}
} // endif

?>