<?php
/**
 * Plugin-specific taxonomies
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Taxonomies' ) )
{ 
	class Maxson_Portfolio_Projects_Taxonomies { 

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
			add_action( 'init', array( &$this, 'register' ), 5 );
		}


		/**
		 * Generate plugin taxonomy labels
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
				'menu_name'                  => ( ! is_null( $menu_name ) ) ? $menu_name : $plural, 
				'all_items'                  => sprintf( __( 'All %1$s', 'maxson' ), $plural ), 
				'edit_item'                  => sprintf( __( 'Edit %1$s', 'maxson' ), $singular ), 
				'view_item'                  => sprintf( __( 'View %1$s', 'maxson' ), $singular ), 
				'update_item'                => sprintf( __( 'Update %1$s', 'maxson' ), $singular ), 
				'add_new_item'               => sprintf( __( 'Add New %1$s', 'maxson' ), $singular ), 
				'new_item_name'              => sprintf( __( 'Add %1$s Name', 'maxson' ), $singular ), 
				'parent_item'                => sprintf( __( 'Parent %1$s', 'maxson' ), $singular ), 
				'parent_item_colon'          => sprintf( __( 'Parent %1$s:', 'maxson' ), $singular ), 
				'search_items'               => sprintf( __( 'Search %1$s', 'maxson' ), $singular ), 
				'popular_items'              => sprintf( __( 'Popular %1$s', 'maxson' ), $plural ), 

				'separate_items_with_commas' => sprintf( __( 'Separate %1$s with commas', 'maxson' ), strtolower( $plural ) ), 
				'add_or_remove_items'        => sprintf( __( 'Add or remove %1$s', 'maxson' ), strtolower( $plural ) ), 
				'choose_from_most_used'      => sprintf( __( 'Choose from the most used %1$s', 'maxson' ), strtolower( $plural ) ), 
				'not_found'                  => sprintf( __( 'No %1$s found.', 'maxson' ), strtolower( $plural ) ), 

				// WordPress 4.3 Labels
				'no_terms'                   => sprintf( __( 'No %1$s', 'maxson' ), strtolower( $plural ) ), 

				// WordPress 4.4 Labels
				'items_list_navigation'      => sprintf( __( '%1$s list navigation', 'maxson' ), strtolower( $plural ) ), 
				'items_list'                 => sprintf( __( '%1$s list', 'maxson' ), strtolower( $plural ) )
			);
		}


		/**
		 * Register plugin taxonomies
		 * 
		 * @return      void
		 */

		public static function register()
		{ 
			if( ! is_blog_installed() )
			{ 
				return;

			} // endif


			do_action( 'maxson_portfolio_register_taxonomy' );

			if( ! taxonomy_exists( 'portfolio_category' ) )
			{ 
				$label_singular  = __( 'Project Category', 'maxson' );
				$label_plural    = __( 'Project Categories', 'maxson' );
				$label_menu_name = __( 'Categories', 'maxson' );

				$labels = self::get_labels( $label_singular, $label_plural, $label_menu_name );

				$rewrite = array( 
					'slug'         => maxson_portfolio_get_option( 'permalink_portfolio_category', 'project-category' ), 
					'with_front'   => true, 
					'hierarchical' => true
				);

				$capability_key = 'project_category';

				$capabilities = array( 
					// Meta capabilities
					'manage_terms'  => "manage_{$capability_key}_terms", 
					'edit_terms'    => "edit_{$capability_key}_terms", 
				//	'read_terms'    => "read_{$capability_key}_terms", 
					'delete_terms'  => "delete_{$capability_key}_terms", 
					'assign_terms'  => "assign_{$capability_key}_terms"
				);

				$args = array( 
					'public'                => true, 
					'show_ui'               => true, 
					'show_in_nav_menus'     => true, 
					'show_tagcloud'         => true, 
					'show_admin_column'     => false, 
					'hierarchical'          => true, 
					'query_var'             => true, 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// Capabilities
				//	'capability_type'     => $capability_types, 
				//	'map_meta_cap'        => true, 

					// WordPress 4.3
					'show_in_rest'          => true
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_category_args', $args );

				register_taxonomy( 'portfolio_category', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_category', self::POST_TYPE );

			} // endif


			if( ! taxonomy_exists( 'portfolio_role' ) )
			{ 
				$label_singular  = __( 'Project Role', 'maxson' );
				$label_plural    = __( 'Project Roles', 'maxson' );
				$label_menu_name = __( 'Roles', 'maxson' );

				$labels = self::get_labels( $label_singular, $label_plural, $label_menu_name );

				$rewrite = array( 
					'slug'         => maxson_portfolio_get_option( 'permalink_portfolio_role', 'project-role' ), 
					'with_front'   => true, 
					'hierarchical' => false
				);

				$args = array( 
					'public'                => true, 
					'show_ui'               => true, 
					'show_in_nav_menus'     => true, 
					'show_tagcloud'         => false, 
					'show_admin_column'     => false, 
					'hierarchical'          => false, 
					'query_var'             => true, 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// WordPress 4.3
					'show_in_rest'          => true
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_role_args', $args );

				register_taxonomy( 'portfolio_role', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_role', self::POST_TYPE );

			} // endif


			if( ! taxonomy_exists( 'portfolio_tag' ) )
			{ 
				$label_singular  = __( 'Project Tag', 'maxson' );
				$label_plural    = __( 'Project Tags', 'maxson' );
				$label_menu_name = __( 'Tags', 'maxson' );

				$labels = self::get_labels( $label_singular, $label_plural, $label_menu_name );

				$rewrite = array( 
					'slug'         => maxson_portfolio_get_option( 'permalink_portfolio_tag', 'project-tag' ), 
					'with_front'   => true, 
					'hierarchical' => false
				);

				$args = array( 
					'public'                => true, 
					'show_ui'               => true, 
					'show_in_nav_menus'     => true, 
					'show_tagcloud'         => true, 
					'show_admin_column'     => false, 
					'hierarchical'          => false, 
					'query_var'             => true, 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// WordPress 4.3
					'show_in_rest'          => true
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_tag_args', $args );

				register_taxonomy( 'portfolio_tag', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_tag', self::POST_TYPE );

			} // endif


			if( ! taxonomy_exists( 'portfolio_type' ) )
			{ 
				$label_singular  = __( 'Project Type', 'maxson' );
				$label_plural    = __( 'Project Types', 'maxson' );
				$label_menu_name = __( 'Types', 'maxson' );

				$labels = self::get_labels( $label_singular, $label_plural, $label_menu_name );

				if( apply_filters( 'maxson_portfolio_activate_project_type_permalink_slug', false ) )
				{ 
					$rewrite = array( 
						'slug'         => maxson_portfolio_get_option( 'permalink_portfolio_type', 'project-type' ), 
						'with_front'   => true, 
						'hierarchical' => false
					);

				} else 
				{ 
					$rewrite = false;

				} // endif

				$args = array( 
					'public'                => true, 
					'show_ui'               => true, 
					'show_in_nav_menus'     => true, 
					'show_tagcloud'         => true, 
					'show_admin_column'     => false, 
					'hierarchical'          => false, 
					'query_var'             => true, 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// WordPress 4.3
					'show_in_rest'          => true
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_type_args', $args );

				register_taxonomy( 'portfolio_type', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_type', self::POST_TYPE );

			} // endif

			do_action( 'maxson_portfolio_after_register_taxonomy' );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Taxonomies();

?>