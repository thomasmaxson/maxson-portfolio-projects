<?php
/**
 * Plugin-specific post type class
 * 
 * @package     Maxson_Portfolio_Projects/includes
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Post_Types' ) )
{ 
	class Maxson_Portfolio_Projects_Post_Types { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 * 
		 * @return      void
		 */

		public function __construct()
		{ 
			add_action( 'init', array( &$this, 'register' ) );

			add_action( 'init', array( &$this, 'support_jetpack_omnisearch' ) );
		}


		/**
		 * Generate plugin post type labels
		 * 
		 * @return      array
		 */

		private static function get_labels( $singular = null, $plural = null, $menu_name = null )
		{ 
			if( is_null( $plural ) )
			{ 
				$plural = "{$singular}s";

			} // endif

			return apply_filters( 'maxson_portfolio_post_type_labels', array( 
				'name'                     => $plural, 
				'singular_name'            => $singular, 
				'add_new'                  => sprintf( __( 'Add New %1$s', 'maxson' ), $singular ), 
				'add_new_item'             => sprintf( __( 'Add New %1$s', 'maxson' ), $singular ), 
				'edit_item'                => sprintf( __( 'Edit %1$s', 'maxson' ), $singular ), 
				'new_item'                 => sprintf( __( 'New %1$s', 'maxson' ), $singular ), 
				'view_item'                => sprintf( __( 'View %1$s', 'maxson' ), $singular ), 
				'view_items'               => sprintf( __( 'View %1$s', 'maxson' ), $plural ), 
				'search_items'             => sprintf( __( 'Search %1$s', 'maxson' ), $plural ), 
				'not_found'                => sprintf( __( 'No %1$s found.', 'maxson' ), strtolower( $plural ) ), 
				'not_found_in_trash'       => sprintf( __( 'No %1$s found in trash.', 'maxson' ), strtolower( $plural ) ), 
				'parent_item_colon'        => sprintf( __( 'Parent %1$s:', 'maxson' ), $singular ), 
				'all_items'                => sprintf( __( 'All %1$s', 'maxson' ), $plural ), 
				'archives'                 => sprintf( __( '%1$s Archives', 'maxson' ), $singular ), 
				'attributes'               => sprintf( __( '%1$s Attributes', 'maxson' ), $singular ), 
				'insert_into_item'         => sprintf( __( 'Insert into %1$s', 'maxson' ), strtolower( $singular ) ), 
				'uploaded_to_this_item'    => sprintf( __( 'Uploaded to this %1$s', 'maxson' ), strtolower( $singular ) ), 
			//	'featured_image'           => __( 'Featured image', 'maxson' ), 
			//	'set_featured_image'       => __( 'Set featured image', 'maxson' ), 
			//	'remove_featured_image'    => __( 'Remove featured image', 'maxson' ), 
			//	'use_featured_image'       => __( 'Use as featured image', 'maxson' ), 
				'menu_name'                => ( ! is_null( $menu_name ) ) ? $menu_name : $plural, 
				'filter_items_list'        => sprintf( __( 'Filter %1$s list', 'maxson' ), strtolower( $singular ) ), 
			//	'filter_by_date'           => __( 'Filter by date', 'maxson' ), 
				'items_list_navigation'    => sprintf( __( '%1$s list navigation', 'maxson' ), $singular ), 
				'items_list'               => sprintf( __( '%1$s list', 'maxson' ), strtolower( $singular ) ), 
				'item_published'           => sprintf( __( '%1$s published', 'maxson' ), strtolower( $singular ) ), 
				'item_published_privately' => sprintf( __( '%1$s published privately', 'maxson' ), strtolower( $singular ) ), 
				'item_reverted_to_draft'   => sprintf( __( '%1$s reverted to draft', 'maxson' ), strtolower( $singular ) ), 
				'item_trashed'             => sprintf( __( '%1$s trashed', 'maxson' ), strtolower( $singular ) ), 
				'item_scheduled'           => sprintf( __( '%1$s scheduled', 'maxson' ), strtolower( $singular ) ), 
				'item_updated'             => sprintf( __( '%1$s updated', 'maxson' ), strtolower( $singular ) ), 
				'item_link'                => sprintf( __( '%1$s Link', 'maxson' ), $singular ), 
				'item_link_description'    => sprintf( __( 'A link to a %1$s', 'maxson' ), strtolower( $singular ) )
			) );
		}


		/**
		 * Register plugin-specific post types
		 * 
		 * @return      void
		 */

		public static function register()
		{ 
			if( ! is_blog_installed() || post_type_exists( self::POST_TYPE ) )
			{
				return;

			} // endif


			do_action( 'maxson_portfolio_register_post_type' );

			$label_singular  = _x( 'Project', 'Post type label, singular', 'maxson' );
			$label_plural    = _x( 'Projects', 'Post type label, plural', 'maxson' );
			$label_menu_name = _x( 'Portfolio', 'Post type label, menu name', 'maxson' );

			$permalink  = maxson_portfolio_get_option( 'permalink_project', 'portfolio' );

			if( $permalink && ( '' !== get_option( 'permalink_structure' ) ) )
			{ 
				$rewrite = array( 
					'slug'       => "/{$permalink}", 
					'with_front' => false, 
					'feeds'      => true
				);

			} else
			{ 
				$rewrite = false;

			} // endif


			$args = array( 
				'public'              => true, 
				'query_var'           => true, 
				'can_export'          => true, 
				'has_archive'         => true, 
				'exclude_from_search' => false, 
				'hierarchical'        => false, 
				'menu_icon'           => 'dashicons-portfolio', 
				'rewrite'             => array( 'slug' => 'project' ), // $rewrite, 
				'supports'            => array( 'author', 'custom-fields', 'editor', 'excerpt', 'revisions', 'thumbnail', 'title', 'publicize', 'wpcom-markdown' ), 
				'description'         => __( 'Add projects to your portfolio library.', 'maxson' ), 
				'labels'              => self::get_labels( $label_singular, $label_plural, $label_menu_name ), 
				
				// WordPress 4.3
				'show_in_rest'        => true // Enable Gutenberg editor
			);

			register_post_type( self::POST_TYPE, apply_filters( 'maxson_portfolio_post_type_args', $args ) );

			do_action( 'maxson_portfolio_after_register_post_type' );

			flush_rewrite_rules();
		}


		/**
		 * Add Support to Jetpack Omnisearch
		 * 
		 * @return      null
		 */

		public static function support_jetpack_omnisearch()
		{ 
			if( class_exists( 'Jetpack_Omnisearch_Posts' ) )
			{ 
				new Jetpack_Omnisearch_Posts( self::POST_TYPE );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Post_Types;

?>