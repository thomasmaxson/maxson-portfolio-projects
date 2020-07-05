<?php
/**
 * Plugin-specific permalinks
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Permalinks' ) )
{ 
	class Maxson_Portfolio_Projects_Permalinks { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'init', array( &$this, 'rewrite' ) );

			add_filter( 'post_type_archive_link', array( &$this, 'archive_link' ), 10, 2 );

			add_filter( 'post_link', array( &$this, 'post_link' ), 1, 3 );
			add_filter( 'post_type_link', array( &$this, 'post_link' ), 1, 3 );
		}


		/**
		 * Custom permalink rewrite structure
		 * 
		 * @return      void
		 */

		public function rewrite()
		{ 
			add_rewrite_tag( '%project_category%', '([^&]+)' );
			add_rewrite_tag( '%project_tag%', '([^&]+)' );
			add_rewrite_tag( '%project_role%', '([^&]+)' );
		}


		/**
		 * Filter the post type archive permalink when post type archive is set as home page
		 * 
		 * @return      string
		 */

		public function archive_link( $link, $post_type )
		{ 
			// Abort if post is not a project
			if( self::POST_TYPE !== $post_type )
			{
				return $link;

			} // endif

			// Abort if archive is not set to home page
			if( maxson_portfolio_get_archive_page_id() !== get_option( 'page_on_front' ) )
			{
				return $link;

			} // endif

    		return home_url();
		}


		/**
		 * Filter to allow permalinks for plugin-specific post type.
		 * 
		 * @param       string $url       The existing permalink URL.
		 * @param       object $post      The post in question.
		 * @param       bool   $leavename Whether to keep the post name.
		 * @return      string
		 */

		public function post_link( $url, $post, $leavename )
		{ 
			// Abort if post is not a project
			if( ! is_object( $post ) || self::POST_TYPE !== get_post_type( $post ) )
			{
				return $url;

			} // endif

			// Abort early if the placeholder rewrite tag isn't in the generated URL
			if( false === strpos( $url, '%' ) )
			{
				return $url;

			} // endif

			$project_cat = _x( 'uncategorized', 'Project taxonomy category slug', 'maxson' );

			if( maxson_portfolio_taxonomy_exists( 'category' ) )
			{ 
				$cat_terms = get_the_terms( $post->ID, 'portfolio_category' );

				if( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
				{ 
					$first_cat_term = array_shift( $cat_terms );
					$project_cat = $first_cat_term->slug;

				} // endif
			} // endif


			$project_role = _x( 'no-role', 'Project taxonomy role slug', 'maxson' );

			if( maxson_portfolio_taxonomy_exists( 'role' ) )
			{ 
				$role_terms = get_the_terms( $post->ID, 'portfolio_role' );

				if( ! is_wp_error( $role_terms ) && ! empty( $role_terms ) )
				{ 
					$first_role_term = array_shift( $role_terms );
					$project_role = $first_role_term->slug;

				} // endif
			} // endif


			$project_tag = _x( 'untagged', 'Project taxonomy tag slug', 'maxson' );

			if( maxson_portfolio_taxonomy_exists( 'tag' ) )
			{ 
				$tag_terms = get_the_terms( $post->ID, 'portfolio_tag' );

				if( ! is_wp_error( $tag_terms ) && ! empty( $tag_terms ) )
				{ 
					$first_tag_term = array_shift( $tag_terms );
					$project_tag = $first_tag_term->slug;

				} // endif
			} // endif


			$find = array( 
				'%year%', 
				'%monthnum%', 
				'%day%', 
				'%hour%', 
				'%minute%', 
				'%second%', 
				'%post_id%', 
				'%postname%', 
				'%category%', 
				'%project_category%', 
				'%role%', 
				'%project_role%', 
				'%tag%', 
				'%project_tag%'
			);

			$replace = array( 
				date_i18n( 'Y', strtotime( $post->post_date ) ), 
				date_i18n( 'm', strtotime( $post->post_date ) ), 
				date_i18n( 'd', strtotime( $post->post_date ) ), 
				date_i18n( 'H', strtotime( $post->post_date ) ), 
				date_i18n( 'i', strtotime( $post->post_date ) ), 
				date_i18n( 's', strtotime( $post->post_date ) ), 
				$post->ID, 
				$post->post_title, 
				$project_cat, 
				$project_cat, 
				$project_role, 
				$project_role, 
				$project_tag, 
				$project_tag, 
			);

			$replace = array_map( 'sanitize_title', $replace );

			$url = str_replace( $find, $replace, $url );

			return $url;
		}
		
	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Permalinks();

?>