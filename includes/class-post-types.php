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

			add_filter( 'rest_api_allowed_post_types', array( &$this, 'rest_api_allowed_post_types' ) );

			add_filter( 'gutenberg_can_edit_post_type', array( &$this, 'gutenberg_can_edit_post_type' ), 10, 2 );
			add_filter( 'use_block_editor_for_post_type', array( &$this, 'gutenberg_can_edit_post_type' ), 10, 2 );
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

			return array( 
				'name'                       => $plural, 
				'singular_name'              => $singular, 
			//	'add_new'                    => sprintf( __( 'Add New %1$s', 'bluegreen' ), $singular ), 
				'add_new_item'               => sprintf( __( 'Add New %1$s', 'bluegreen' ), $singular ), 
				'edit_item'                  => sprintf( __( 'Edit %1$s', 'bluegreen' ), $singular ), 
				'new_item'                   => sprintf( __( 'New %1$s', 'bluegreen' ), $singular ), 
				'view_item'                  => sprintf( __( 'View %1$s', 'bluegreen' ), $singular ), 
				'search_items'               => sprintf( __( 'Search %1$s', 'bluegreen' ), $singular ), 
				'not_found'                  => sprintf( __( 'No %1$s found.', 'bluegreen' ), strtolower( $plural ) ), 
				'not_found_in_trash'         => sprintf( __( 'No %1$s found in trash.', 'bluegreen' ), strtolower( $plural ) ), 
				'parent_item_colon'          => sprintf( __( 'Parent %1$s:', 'bluegreen' ), $singular ), 
				'all_items'                  => sprintf( __( 'All %1$s', 'bluegreen' ), $plural ), 
				'menu_name'                  => ( ! is_null( $menu_name ) ) ? $menu_name : $plural, 
				'name_admin_bar'             => $singular, 
				

				// WordPress 4.3 labels
				'promoted_image'             => sprintf( __( '%1$s Image', 'bluegreen' ), $singular ), 
				'set_promoted_image'         => sprintf( __( 'Set %1$s image', 'bluegreen' ), strtolower( $singular ) ), 
				'remove_promoted_image'      => sprintf( __( 'remove %1$s image', 'bluegreen' ), strtolower( $singular ) ), 
				'use_promoted_image'         => sprintf( __( 'Use as %1$s image', 'bluegreen' ), strtolower( $singular ) ), 

				// WordPress 4.4 labels
				'archives'                   => sprintf( __( '%1$s Archives', 'bluegreen' ), $singular ), 
				'insert_into_item'           => sprintf( __( 'Insert into %1$s', 'bluegreen' ), strtolower( $singular ) ), 
				'uploaded_to_this_item'      => sprintf( __( 'Uploaded to this %1$s', 'bluegreen' ), strtolower( $singular ) ), 
				'filter_items_list'          => sprintf( __( 'Filter %1$s list', 'bluegreen' ), strtolower( $singular ) ), 
				'items_list_navigation'      => sprintf( __( '%1$s list navigation', 'bluegreen' ), $singular ), 
				'items_list'                 => sprintf( __( '%1$s list', 'bluegreen' ), strtolower( $singular ) )
			);
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

			$archive_id = maxson_portfolio_get_archive_page_id();

			$has_archive = ( get_post( $archive_id ) && apply_filters( 'maxson_portfolio_post_type_has_archive', true ) ) ? get_page_uri( $archive_id ) : false;

			$label_singular  = _x( 'Project', 'Post type label, singular', 'maxson' );
			$label_plural    = _x( 'Projects', 'Post type label, plural', 'maxson' );
			$label_menu_name = _x( 'Portfolio', 'Post type label, menu type', 'maxson' );

			$labels = self::get_labels( $label_singular, $label_plural, $label_menu_name );

			$permalink  = maxson_portfolio_get_option( 'permalink_project', 'portfolio' );

			if( $permalink && ( '' != get_option( 'permalink_structure' ) ) )
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


			/*$capability_key = 'project';

			$capability_types = array( 
				$capability_key, 
				"{$capability_key}s"
			);

			$capabilities = array( 
				// Meta capabilities
				'edit_post'              => "edit_{$capability_key}", 
				'read_post'              => "read_{$capability_key}", 
				'delete_post'            => "delete_{$capability_key}", 

				// Primitive capabilities used outside of map_meta_cap():
				'edit_posts'             => "edit_{$capability_key}s", 
				'edit_others_posts'      => "edit_others_{$capability_key}s", 
				'publish_posts'          => "publish_{$capability_key}s", 
				'read_private_posts'     => "read_private_{$capability_key}s", 

				// Primitive capabilities used within map_meta_cap(): 
				'read'                   => "read", 
				'delete_posts'           => "delete_{$capability_key}s", 
				'delete_private_posts'   => "delete_private_{$capability_key}s", 
				'delete_published_posts' => "delete_published_{$capability_key}s", 
				'delete_others_posts'    => "delete_others_{$capability_key}s", 
				'edit_private_posts'     => "edit_private_{$capability_key}s", 
				'edit_published_posts'   => "edit_published_{$capability_key}s", 
				'create_posts'           => "edit_{$capability_key}s"
			);*/


			$description = __( 'Add projects to your portfolio library.', 'maxson' );


			$supports = array( 'author', 'custom-fields', 'editor', 'excerpt', 'revisions', 'thumbnail', 'title', 'publicize', 'wpcom-markdown' );


			$args = array( 
				'public'              => true, 
				'show_ui'             => true, 
				'show_in_menu'        => true, 
				'show_in_nav_menus'   => true, 
				'show_in_admin_bar'   => true, 
				'query_var'           => true, 
				'can_export'          => true, 
				'exclude_from_search' => false, 
				'hierarchical'        => false, 
				'menu_position'       => false, 
				'menu_icon'           => 'dashicons-portfolio', 
				'has_archive'         => $has_archive, 
				'rewrite'             => $rewrite, 
				'supports'            => $supports, 
				'labels'              => $labels, 
				'description'         => $description, 

				// Capabilities
			//	'capabilities'        => $capabilities, 
			//	'capability_type'     => $capability_types, 
				'capability_type'     => 'post', 
				'map_meta_cap'        => true, 

				// WordPress 4.3 args
				'show_in_rest'          => true, 
				'rest_base'             => 'project', 
			//	'rest_controller_class' => 'WP_REST_Posts_Controller'
			//	'rest_controller_class' => 'Maxson_Portfolio_Projects_REST_Posts_Controller'
			);

			register_post_type( self::POST_TYPE, apply_filters( 'maxson_portfolio_post_type_args', $args ) );

			do_action( 'maxson_portfolio_after_register_post_type' );

			flush_rewrite_rules();
		}


		/**
		 * Disable Gutenberg for portfolio
		 * 
		 * @param       bool   $can_edit Whether the post type can be edited or not
		 * @param       string $post_type The post type being checked
		 * @return      bool
		 */

		public static function gutenberg_can_edit_post_type( $can_edit, $post_type )
		{ 
			return ( self::POST_TYPE === $post_type ) ? false : $can_edit;
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


		/**
		 * Added post type for Jetpack related posts
		 * 
		 * @param       array $post_types
		 * @return      array
		 */

		public static function rest_api_allowed_post_types( $post_types )
		{ 
			$post_types[] = self::POST_TYPE;

			return $post_types;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Post_Types;

?>