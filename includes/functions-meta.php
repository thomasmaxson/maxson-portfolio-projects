<?php
/**
 * Plugin-specific meta functions
 * 
 * @author      Thomas Maxson
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'portfolio_project_get_id' ) )
{ 
	/**
	 * Return the current projects ID
	 * 
	 * @return      int
	 */

	function portfolio_project_get_id( $post_id = null )
	{ 
		global $post;

		return ( is_null( $post_id ) ) ? $post->ID : $post_id;
	}
} // endif


if( ! function_exists( 'maxson_project_get_type' ) )
{ 
	/**
	 * Return the project type
	 * 
	 * @param       string $key  (optional) Type of data to return
	 * @param       array  $args (optional) An array of arguments
	 * @return      string
	 */

	function maxson_project_get_type( $post_id = null, $key = 'name', $args = array() )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$value = '';
		$terms = wp_get_post_terms( $project_id, 'portfolio_type', $args );

		if( ! empty( $terms ) && ! is_wp_error( $terms ) )
		{ 
			$term = current( $terms );

			switch( strtolower( $key ) )
			{ 
				case 'name': 
				case 'title': 
					$value = $term->name;
					break;

				case 'slug': 
					$value = $term->slug;
					break;

				case 'id': 
				case 'term_id': 
					$value = $term->term_id;
					break;

			} // endswitch
		} // endif

		return apply_filters( 'portfolio_project_the_meta_type', $value, $key, $args );
	}
} // endif


if( ! function_exists( 'portfolio_project_is_type' ) )
{ 
	/**
	 * Checks the project type
	 * 
	 * @param       string $type (optional) Type to check for
	 * @param       string $key  (optional) Type of data to compare
	 * @return      bool
	 */

	function portfolio_project_is_type( $post_id = null, $type = null, $key = 'slug' )
	{ 
		return ( maxson_project_get_type( $post_id, $key ) == $type ) ? true : false;
	}
} // endif


if( ! function_exists( 'maxson_project_has_callout' ) )
{ 
	/**
	 * Checks whether or not the project is callout
	 * 
	 * @return      bool
	 */

	function maxson_project_has_callout( $post_id = null, $true = true, $false = false )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$meta = maxson_project_get_callout_label( $project_id );

		return ( '' !== $meta ) ? $true : $false;
	}
}


if( ! function_exists( 'maxson_project_get_callout_label' ) )
{ 
	/**
	 * Return the project callout label
	 * 
	 * @return      string
	 */

	function maxson_project_get_callout_label( $post_id = null )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$meta = get_post_meta( $project_id, '_callout', true );

		return apply_filters( 'maxson_portfolio_the_callout_label', $meta, $project_id );
	}
} // endif


if( ! function_exists( 'maxson_project_get_client' ) )
{ 
	/**
	 * Return the project client
	 * 
	 * @return      string
	 */

	function maxson_project_get_client( $post_id = null )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$meta = get_post_meta( $project_id, '_client', true );

		return apply_filters( 'maxson_portfolio_the_client', $meta, $project_id );
	}
} // endif


if( ! function_exists( 'maxson_project_get_url' ) )
{ 
	/**
	 * Return the project url
	 * 
	 * @return      string|bool
	 */

	function maxson_project_get_url( $post_id = null )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$meta = get_post_meta( $project_id, '_url', true );

		return apply_filters( 'maxson_portfolio_the_url', $meta, $project_id );
	}
} // endif


if( ! function_exists( 'maxson_project_get_start_end_date' ) )
{ 
	/**
	 * Return the project start or end date
	 * 
	 * @param       string $type    (required) Type of date to return
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       string $format  (optional) Requested output format, should be a PHP date format string
	 * @return      string
	 */

	function maxson_project_get_start_end_date( $type = null, $post_id = null, $format = null )
	{ 
		$type = strtolower( $type );

		if( is_null( $type ) || ! in_array( $type, array( 'start', 'end' ) ) )
		{
			return false;

		} else
		{ 
			$project_id = portfolio_project_get_id( $post_id );

		} // endif

		switch( $type )
		{ 
			case 'start': 
				$value = get_post_meta( $project_id, '_start_date', true );
				break;

			case 'end': 
				$value = get_post_meta( $project_id, '_end_date', true );
				break;

		} // endswitch

		//wp_die( var_dump( $value ) );

		$date = apply_filters( "maxson_portfolio_the_{$type}_date_raw", $value, $project_id );

		if( ! empty( $date ) )
		{ 
			if( 'raw' === $format )
			{ 
				$output = $date;

			} else
			{ 
				if( is_null( $format ) || empty( $format ) )
				{ 
					$format = maxson_portfolio_get_date_format();

				} // endif

				$output = mysql2date( $format, $date );

			} // endif
		} else
		{ 
			$output = false;

		} // endif

		return apply_filters( "maxson_portfolio_the_{$type}_date", $output, $project_id, $format );
	}
} // endif


if( ! function_exists( 'maxson_project_get_start_date' ) )
{ 
	/**
	 * Return the project start date
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       string $format  (optional) Requested output format, should be a PHP date format string
	 * @return      string
	 */

	function maxson_project_get_start_date( $post_id = null, $format = null )
	{ 
		return maxson_project_get_start_end_date( 'start', $post_id, $format );
	}
} // endif


if( ! function_exists( 'maxson_project_get_end_date' ) )
{ 
	/**
	 * Return the project end date
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       string $format  (optional) Requested output format, should be a PHP date format string
	 * @return      string
	 */

	function maxson_project_get_end_date( $post_id = null, $format = null )
	{ 
		return maxson_project_get_start_end_date( 'end', $post_id, $format );
	}
} // endif


if( ! function_exists( 'maxson_project_get_start_end_date_html' ) )
{ 
	/**
	 * Return the project  date
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       string $args (optional) An array of arguments
	 * @return      string
	 */

	function maxson_project_get_start_end_date_html( $post_id = null, $args = array() )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$defaults = array( 
			'format'        => maxson_portfolio_get_date_format(), 
			'separator'     => ' - ', 
			'before_start'  => '<span itemprop="dateCreated" class="project-start-date entry-date updated">', 
			'after_start'   => '</span>', 
			'before_end'    => '<span class="project-end-date">', 
			'after_end'     => '</span>'
		);

		$args = wp_parse_args( $args, $defaults );

		$start_date = maxson_project_get_start_date( $project_id, $args['format'] );
		$end_date   = maxson_project_get_end_date( $project_id, $args['format'] );

		if( ! empty( $start_date ) && ! empty( $end_date ) )
		{ 
			$output  = $args['before_start'] . $start_date . $args['after_start'];
			$output .= $args['separator'];
			$output .= $args['before_end'] . $end_date . $args['after_end'];

		} elseif( ! empty( $start_date ) )
		{ 
			$output  = $args['before_start'] . $start_date . $args['after_start'];

		} elseif( ! empty( $end_date ) )
		{ 
			$output = $args['before_end'] . $end_date . $args['after_end'];

		} else 
		{ 
			$output = false;

		} // endif

		return apply_filters( 'maxson_portfolio_the_start_end_date_html', $output, $project_id, $args );
	}
} // endif


if( ! function_exists( 'maxson_project_get_term_html' ) )
{ 
	/**
	 * Return the project taxonomy terms
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       array       $args (optional) An array of arguments
	 * @return      string|bool
	 */

	function maxson_project_get_term_html( $post_id = null, $args = array() )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$defaults = array( 
			'link'       => true, 
			'taxonomy'   => 'portfolio_category', 
			'separator'  => ', ', 
			'before'     => false, 
			'after'      => false, 
			'beforeitem' => false, 
			'afteritem'  => false
		);

		$args = wp_parse_args( $args, $defaults );

		if( ! taxonomy_exists( $args['taxonomy'] ) )
		{
			return false;

		} // endif


		$terms = wp_get_object_terms( $project_id, $args['taxonomy'] );

		if( ! empty( $terms ) && ! is_wp_error( $terms ) )
		{ 
			$items = array();

			if( $args['link'] )
			{ 
				foreach( $terms as $term )
				{ 
					$link = get_term_link( $term );

					$items[] = sprintf( '<a href="%1$s">%2$s</a>', esc_attr( $link ), esc_html( $term->name ) );

				} // endforeach

			} else
			{ 
				foreach( $terms as $term )
				{ 
					$items[] = esc_html( $term->name );

				} // endforeach

			} // endif

			$output  = $args['before'];
			$output .= $args['beforeitem'];
			$output .= join( $args['afteritem'] . $args['separator'] . $args['beforeitem'], $items );
			$output .= $args['afteritem'];
			$output .= $args['after'];

			return $output;

		} else 
		{ 
			return false;

		} // endif
	}
} // endif


if( ! function_exists( 'maxson_project_get_categories' ) )
{ 
	/**
	 * Return the project categories
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       array  $args (optional) An array of arguments
	 * @return      string
	 */

	function maxson_project_get_categories( $post_id = null, $args = array() )
	{ 
		$args['taxonomy'] = 'portfolio_category';

		return maxson_project_get_term_html( $post_id, $args );
	}
} // endif


if( ! function_exists( 'maxson_project_get_roles' ) )
{ 
	/**
	 * Return the project tags
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       array  $args (optional) An array of arguments
	 * @return      string|bool
	 */

	function maxson_project_get_roles( $post_id = null, $args = array() )
	{ 
		$args['taxonomy'] = 'portfolio_role';

		return maxson_project_get_term_html( $post_id, $args );
	}
} // endif


if( ! function_exists( 'maxson_project_data_term_tags' ) )
{ 
	/**
	 * Return the project tags
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       array  $args (optional) An array of arguments
	 * @return      string|bool
	 */

	function maxson_project_get_tags( $post_id = null, $args = array() )
	{ 
		$args['taxonomy'] = 'portfolio_tag';

		return maxson_project_get_html( $post_id, $args );
	}
} // endif


if( ! function_exists( 'maxson_project_get_taxonomy_terms' ) )
{ 
	/**
	 * Retrieves related project terms
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       string $taxonomy (optional) Taxonomy name
	 * @return      array
	 */

	function maxson_project_get_taxonomy_terms( $post_id = null, $taxonomy = 'portfolio_category', $key = 'term_id' )
	{ 
		if( ! taxonomy_exists( $taxonomy ) )
		{
			return array();

		} // endif

		$project_id = portfolio_project_get_id( $post_id );

		$terms = apply_filters( "maxson_{$taxonomy}_project_terms", wp_get_post_terms( $project_id, $taxonomy ), $project_id );

		$output = wp_list_pluck( $terms, $key );

		return $output;
	}
} // endif


if( ! function_exists( 'maxson_project_get_related_projects' ) )
{ 
	/**
	 * Get and return related projects
	 * 
	 * @param       string $post_id (required) The ID of the post/project
	 * @param       int    $limit    (optional) Number of posts to return
	 * @param       array  $args     (optional) An array of arguments
	 * @param       string $taxonomy (optional) Taxonomy name
	 * @return      array  Array of post IDs
	 */

	function maxson_project_get_related_projects( $post_id = null, $limit = 5, $args = array(), $taxonomy = 'portfolio_category' )
	{ 
		$project_id = portfolio_project_get_id( $post_id );

		$transient_name     = "maxson_portfolio_related_{$project_id}_{$taxonomy}_{$limit}";
		$transient_duration = apply_filters( 'maxson_portfolio_related_projects_transient_duration', ( DAY_IN_SECONDS * 30 ), $taxonomy, $project_id );

		if( false === ( $related_posts = get_transient( $transient_name ) ) )
		{ 
			// Related projects are found from category, role, tag or type
			$terms = maxson_project_get_taxonomy_terms
			( $project_id, $taxonomy, 'term_id' );

			// Don't bother if none are set
			if( sizeof( $terms ) == 1 )
			{ 
				$related_posts = array();

			} else
			{ 
				$exclude_ids = wp_parse_id_list( array( 0, $project_id ) );

				$defaults = array( 
					'not_in'            => $exclude_ids, 
					'orderby'           => 'rand', 
					'require_thumbnail' => true
				);

				switch( $taxonomy )
				{ 
					case 'portfolio_category': 
						$defaults['category']       = $terms;
						$defaults['category_field'] = 'id';
						break;

					case 'portfolio_role': 
						$defaults['role']       = $terms;
						$defaults['role_field'] = 'id';
						break;

					case 'portfolio_tag': 
						$defaults['tag']       = $terms;
						$defaults['tag_field'] = 'id';
						break;

					case 'portfolio_type': 
						$defaults['type']       = $terms;
						$defaults['type_field'] = 'id';
						break;

				} // endswitch

				$args = wp_parse_args( $args, $defaults );

				$related_args = maxson_portfolio_query_args( $args );

				// Cannot override, it is passed in
				$related_args['posts_per_page'] = $limit;
				$related_args['fields']         = 'ids';

				// Get the related projects
				$queried_posts = new WP_Query( $related_args );

			//	$related_posts = wp_list_pluck( $queried_posts->posts, 'ID' );
				$related_posts = $queried_posts;

			} // endif

			set_transient( $transient_name, $related_posts, $transient_duration );

		} // endif

		shuffle( $related_posts );

		return $related_posts;
	}
} // endif

?>