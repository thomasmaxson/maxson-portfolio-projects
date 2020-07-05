<?php
/**
 * Plugin-specific template loader
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Query' ) )
{  
	class Maxson_Portfolio_Projects_Query { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			if( ! is_admin() )
			{ 
				add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
				add_filter( 'pre_get_posts', array( &$this, 'pre_get_posts' ) );

			} // endif
		}


		/**
		 * Register custom query vars
		 * 
		 * @param       array $vars The array of available query variables
		 * @return      array
		 */

		public function add_query_vars( $vars )
		{ 
			$vars[] = 'project_start_date';
			$vars[] = 'project_start_year';
			$vars[] = 'project_start_month';
			$vars[] = 'project_start_day';

			$vars[] = 'project_end_date';
			$vars[] = 'project_end_year';
			$vars[] = 'project_end_month';
			$vars[] = 'project_end_day';

			$vars[] = 'show_promoted_projects';

			return $vars;
		}


		/**
		 * Are we currently on the front page?
		 * 
		 * @param       object $q
		 * @return      bool
		 */

		private function is_showing_page_on_front( $query )
		{ 
			return $query->is_page() && 'page' === get_option( 'show_on_front' );
		}


		/**
		 * Is the front page a page we define?
		 * 
		 * @param       int $page_id
		 * @return      bool
		 */

		private function page_on_front_is( $page_id )
		{ 
			return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
		}


		/**
		 * Setup global settings on archive and taxonomy pages
		 * 
		 * @param       array $query
		 * @return      string
		 */

		public function pre_get_posts( $query )
		{ 
			// We only want to affect the main query
			if( ! $query->is_main_query() )
			{ 
				return;

			} // endif

			// Fix portfolio feed
			if( $query->is_feed() && $query->is_post_type_archive( self::POST_TYPE ) )
			{ 
				$query->is_comment_feed = false;

			} // endif

			$archive_id = maxson_portfolio_get_archive_page_id();

			// Special check for pages with the portfolio archive on front
		//	if( $this->is_showing_page_on_front( $query ) && $this->page_on_front_is( $archive_id ) )
			if( $this->is_showing_page_on_front( $query ) && 
				absint( $query->get( 'page_id' ) ) == $archive_id )
			{ 
				// This is a front-page archive
				$query->set( 'post_type', self::POST_TYPE );
				$query->set( 'page_id', '' );

				if( isset( $query->query['paged'] ) )
				{ 
					$query->set( 'paged', $query->query['paged'] );

				} // endif

				// Define a variable so we know this is the front page shop later on
				define( 'MAXSON_PORTFOLIO_IS_FRONT_PAGE', true );

				// Get the actual WP page to avoid errors and let us use is_front_page()
				// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
				global $wp_post_types;

				$page = get_post( $archive_id );

				$wp_post_types[ self::POST_TYPE ]->ID         = $page->ID;
				$wp_post_types[ self::POST_TYPE ]->post_title = $page->post_title;
				$wp_post_types[ self::POST_TYPE ]->post_name  = $page->post_name;
				$wp_post_types[ self::POST_TYPE ]->post_type  = $page->post_type;
				$wp_post_types[ self::POST_TYPE ]->ancestors  = get_ancestors( $page->ID, $page->post_type );

				// Fix conditional Functions like is_front_page
				$query->is_singular          = false;
				$query->is_post_type_archive = true;
				$query->is_archive           = true;
				$query->is_page              = true;

				// Remove post type archive name from front page title tag
				add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

				// Fix WP SEO
				if( class_exists( 'WPSEO_Meta' ) )
				{ 
					add_filter( 'wpseo_metadesc', array( &$this, 'wpseo_metadesc' ) );
					add_filter( 'wpseo_metakey', array( &$this, 'wpseo_metakey' ) );

				} // endif

			// Only apply to portfolio categories, the product post archive, the shop page, product tags, and product attribute taxonomies
			} elseif( ! $query->is_post_type_archive( self::POST_TYPE ) && 
				! $query->is_tax( get_object_taxonomies( self::POST_TYPE ) ) )
			{ 
				return;

			} // endif

			$this->get_portfolio_query( $query );

			// And remove the pre_get_posts hook
			$this->remove_portfolio_query();
		}


		/**
		 * wpseo_metadesc function
		 * 
		 * @return      string
		 */

		public function wpseo_metadesc()
		{ 
			$archive_id = maxson_portfolio_get_archive_page_id();
			$output     = false;

			if( ! empty( $archive_id ) )
			{ 
				$output = WPSEO_Meta::get_value( 'metadesc', $archive_id );

			} // endif

			return $output;
		}


		/**
		 * wpseo_metakey function
		 * 
		 * @return      string
		 */

		public function wpseo_metakey()
		{ 
			$archive_id = maxson_portfolio_get_archive_page_id();
			$output     = false;

			if( ! empty( $archive_id ) )
			{ 
				$output = WPSEO_Meta::get_value( 'metakey', $archive_id );

			} // endif

			return $output;
		}


		/**
		 * Remove the query
		 */

		public function remove_portfolio_query()
		{ 
			remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}


		/**
		 * Get the tax query which was used by the main query
		 * 
		 * @return      array
		 */

		public static function get_main_tax_query()
		{ 
			global $wp_the_query;

			$query = isset( $wp_the_query->tax_query, $wp_the_query->tax_query->queries ) ? $wp_the_query->tax_query->queries : array();

			return $query;
		}


		/**
		 * Get the meta query which was used by the main query
		 * 
		 * @return      array
		 */

		public static function get_main_meta_query()
		{ 
			global $wp_the_query;

			$args  = $wp_the_query->query_vars;
			$query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

			return $query;
		}


		/**
		 * Query the portfolio, applying sorting/ordering etc. This applies to the main WordPress loop.
		 * 
		 * @param       mixed $q
		 */

		public function get_portfolio_query( $q )
		{ 
			// Ordering query vars
			$ordering  = $this->get_ordering_args();
			$posts_per_page = ( $q->get( 'posts_per_page' ) ) ? $q->get( 'posts_per_page' ) : maxson_portfolio_get_archive_limit();

			$q->set( 'orderby', $ordering['orderby'] );
			$q->set( 'order', $ordering['order'] );

			if( isset( $ordering['meta_key'] ) )
			{ 
				$q->set( 'meta_key', $ordering['meta_key'] );

			} // endif

			// Query vars that affect posts shown
			$q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ) ) );
		//	$q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ) ) );
			$q->set( 'posts_per_page', $posts_per_page );
			$q->set( 'maxson_query', 'portfolio_query' );

			do_action( 'maxson_portfolio_query', $q, $this );
		}



		/**
		 * Returns an array of arguments for ordering based on the selected values
		 * 
		 * @return      array
		 */

		public function get_ordering_args( $orderby = '', $order = '' )
		{ 
			// Get ordering from query string unless defined
			if( empty( $orderby ) )
			{ 
				$orderby = maxson_portfolio_get_option( 'archive_orderby' );

			} // endif

			if( empty( $order ) )
			{ 
				$order = maxson_portfolio_get_option( 'archive_order' );

			} // endif

			$args = array( 
				'orderby'  => 'menu_order title', 
				'order'    => ( $order == 'DESC' ) ? 'DESC' : 'ASC'
			);

			switch( $orderby )
			{ 
				case 'rand': 
					$args['orderby'] = 'rand';
					break;

				case 'date': 
					$args['orderby'] = 'date ID';
					break;

				case 'title': 
					$args['orderby'] = 'title';
					break;

				case 'project_start_date': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_start_date';
					break;

				case 'project_start_year': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_start_year';
					break;

				case 'project_start_month': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_start_month';
					break;

				case 'project_start_day': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_start_day';
					break;

				case 'project_end_date': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_end_date';
					break;

				case 'project_end_year': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_end_year';
					break;

				case 'project_end_month': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_end_month';
					break;

				case 'project_end_day': 
					$args['orderby'] = 'meta_value ID';
					$args['meta_key'] = '_end_day';
					break;

			} // endswitch

			return apply_filters( 'maxson_portfolio_query_ordering_args', $args );
		}


		/**
		 * Appends meta queries to an array
		 * 
		 * @param       array $meta_query
		 * @return      array
		 */

		public function get_meta_query( $meta_query = array() )
		{ 
			if( ! is_array( $meta_query ) )
			{ 
				$meta_query = array();

			} // endif

			$meta_query['require_thumbnail'] = $this->require_thumbnail_meta_query();
			$meta_query['promoted_projects'] = $this->promoted_projects_meta_query();
			$meta_query['project_dates']     = $this->start_end_date_meta_query();

			if( ! empty( $meta_query ) && count( $meta_query ) > 1 )
			{ 
				$meta_query['relation'] = 'AND';

			} // endif

			return array_filter( apply_filters( 'maxson_portfolio_query_meta_query', $meta_query, $this ) );
		}


		/**
		 * Get the tax query which was used by the main query.
		 * 
		 * @return      array
		 */

	//	public static function get_main_tax_query()
	//	{ 
	//		global $wp_the_query;

	//		$tax_query = array();

	//		if( isset( $wp_the_query->tax_query, $wp_the_query->tax_query->queries ) )
	//		{ 
	//			$tax_query = $wp_the_query->tax_query->queries;

	//		} // endif

	//		return $tax_query;
	//	}


		/**
		 * Returns a meta query to handle required thumbnails
		 * 
		 * @return      array
		 */

		public function require_thumbnail_meta_query()
		{ 
			$option = maxson_portfolio_get_option( 'archive_thumbnail' );
			$query  = array();

			if( ! empty( $option ) )
			{ 
				$query = array( 
					'key'     => '_thumbnail_id', 
					'compare' => 'EXISTS'
				);

			} // endif

			return $query;
		}


		/**
		 * Returns a meta query to handle required thumbnails
		 * 
		 * @return      array
		 */

		public function promoted_projects_meta_query()
		{ 
			if( filter_input( INPUT_GET, 'show_promoted_projects' ) )
			{ 
				$query = array( 
					'key'     => '_promoted', 
					'compare' => 'EXISTS'
				);

			} else
			{ 
				$query = array();

			} // endif

			return $query;
		}


		/**
		 * Return a meta query for filtering by project start/end date 
		 * 
		 * @return      array
		 */

		private function start_end_date_meta_query()
		{ 
			$query = array();

			$start_date  = isset( $_GET['project_start_date'] )  ? $_GET['project_start_date']  : null;
			$start_year  = isset( $_GET['project_start_year'] )  ? $_GET['project_start_year']  : null;
			$start_month = isset( $_GET['project_start_month'] ) ? $_GET['project_start_month'] : null;
			$start_day   = isset( $_GET['project_start_day'] )   ? $_GET['project_start_day']   : null;

			$end_date  = isset( $_GET['project_end_date'] )  ? $_GET['project_end_date']  : null;
			$end_year  = isset( $_GET['project_end_year'] )  ? $_GET['project_end_year']  : null;
			$end_month = isset( $_GET['project_end_month'] ) ? $_GET['project_end_month'] : null;
			$end_day   = isset( $_GET['project_end_day'] )   ? $_GET['project_end_day']   : null;

			if( ! is_null( $start_date ) && ! is_null( $end_date ) )
			{ 
				$query['project_start_date'] = array( 
					'key'     => '_start_date', 
					'value'   => $start_date, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_start_date'] = array( 
					'key'     => '_start_date', 
					'compare' => 'EXISTS'
				);

				$query['project_end_date'] = array( 
					'key'     => '_end_date', 
					'value'   => $end_date, 
					'compare' => '<=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_end_date'] = array( 
					'key'     => '_end_date', 
					'compare' => 'EXISTS'
				);

			} elseif( ! is_null( $start_date ) )
			{ 
				$query['project_start_date'] = array( 
					'key'     => '_start_date', 
					'value'   => $start_date, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_start_date'] = array( 
					'key'     => '_start_date', 
					'compare' => 'EXISTS'
				);

			} elseif( ! is_null( $end_date ) )
			{ 
				$query['project_end_date'] = array( 
					'key'     => '_end_date', 
					'value'   => $end_date, 
					'compare' => '<=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_end_date'] = array( 
					'key'     => '_end_date', 
					'compare' => 'EXISTS'
				);

			} // endif


			if( ! is_null( $start_year ) )
			{ 
				$query['project_start_year'] = array( 
					'key'     => '_start_year', 
					'value'   => $start_year, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_start_year'] = array( 
					'key'     => '_start_year', 
					'compare' => 'EXISTS'
				);

			} // endif


			if( ! is_null( $start_month ) )
			{ 
				$query['project_start_month'] = array( 
					'key'     => '_start_month', 
					'value'   => $start_month, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_start_month'] = array( 
					'key'     => '_start_month', 
					'compare' => 'EXISTS'
				);

			} // endif


			if( ! is_null( $start_day ) )
			{ 
				$query['project_start_day'] = array( 
					'key'     => '_start_day', 
					'value'   => $start_day, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_start_day'] = array( 
					'key'     => '_start_day', 
					'compare' => 'EXISTS'
				);

			} // endif


			if( ! is_null( $end_year ) )
			{ 
				$query['project_end_year'] = array( 
					'key'     => '_end_year', 
					'value'   => $end_year, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_end_year'] = array( 
					'key'     => '_end_year', 
					'compare' => 'EXISTS'
				);

			} // endif


			if( ! is_null( $end_month ) )
			{ 
				$query['project_end_month'] = array( 
					'key'     => '_end_month', 
					'value'   => $end_month, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_end_month'] = array( 
					'key'     => '_end_month', 
					'compare' => 'EXISTS'
				);

			} // endif


			if( ! is_null( $end_day ) )
			{ 
				$query['project_end_day'] = array( 
					'key'     => '_end_day', 
					'value'   => $end_day, 
					'compare' => '>=', 
					'type'    => 'NUMERIC'
				);

				$query['has_project_end_day'] = array( 
					'key'     => '_end_day', 
					'compare' => 'EXISTS'
				);

			} // endif

			$query['relation'] = 'AND';

			return $query;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Query();

?>