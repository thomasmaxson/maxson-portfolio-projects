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
			add_action( 'init', array( &$this, 'create_terms' ), 5 );
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
				'all_items'                  => sprintf( __( 'All %1$s', 'bluegreen' ), $plural ), 
				'edit_item'                  => sprintf( __( 'Edit %1$s', 'bluegreen' ), $singular ), 
				'view_item'                  => sprintf( __( 'View %1$s', 'bluegreen' ), $singular ), 
				'update_item'                => sprintf( __( 'Update %1$s', 'bluegreen' ), $singular ), 
				'add_new_item'               => sprintf( __( 'Add New %1$s', 'bluegreen' ), $singular ), 
				'new_item_name'              => sprintf( __( 'Add %1$s Name', 'bluegreen' ), $singular ), 
				'parent_item'                => sprintf( __( 'Parent %1$s', 'bluegreen' ), $singular ), 
				'parent_item_colon'          => sprintf( __( 'Parent %1$s:', 'bluegreen' ), $singular ), 
				'search_items'               => sprintf( __( 'Search %1$s', 'bluegreen' ), $singular ), 
				'popular_items'              => sprintf( __( 'Popular %1$s', 'bluegreen' ), $plural ), 

				'separate_items_with_commas' => sprintf( __( 'Separate %1$s with commas', 'bluegreen' ), strtolower( $plural ) ), 
				'add_or_remove_items'        => sprintf( __( 'Add or remove %1$s', 'bluegreen' ), strtolower( $plural ) ), 
				'choose_from_most_used'      => sprintf( __( 'Choose from the most used %1$s', 'bluegreen' ), strtolower( $plural ) ), 
				'not_found'                  => sprintf( __( 'No %1$s found.', 'bluegreen' ), strtolower( $plural ) ), 

				// WordPress 4.3 Labels
				'no_terms'                   => sprintf( __( 'No %1$s', 'bluegreen' ), strtolower( $plural ) ), 

				// WordPress 4.4 Labels
				'items_list_navigation'      => sprintf( __( '%1$s list navigation', 'bluegreen' ), strtolower( $plural ) ), 
				'items_list'                 => sprintf( __( '%1$s list', 'bluegreen' ), strtolower( $plural ) )
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

			if( ! taxonomy_exists( 'portfolio_category' ) && maxson_portfolio_get_option( 'setup_portfolio_category' ) )
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
					'show_in_rest'          => true,
					'rest_base'             => 'category', 
					'rest_controller_class' => 'WP_REST_Terms_Controller'
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_category_args', $args );

				register_taxonomy( 'portfolio_category', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_category', self::POST_TYPE );

			} // endif


			if( ! taxonomy_exists( 'portfolio_role' ) && maxson_portfolio_get_option( 'setup_portfolio_role' ) )
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
					'capability_type'       => 'project', 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// WordPress 4.3
					'show_in_rest'          => true,
					'rest_base'             => 'role', 
					'rest_controller_class' => 'WP_REST_Terms_Controller'
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_role_args', $args );

				register_taxonomy( 'portfolio_role', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_role', self::POST_TYPE );

			} // endif


			if( ! taxonomy_exists( 'portfolio_tag' ) && maxson_portfolio_get_option( 'setup_portfolio_tag' ) )
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
					'capability_type'       => 'project', 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// WordPress 4.3
					'show_in_rest'          => true, 
					'rest_base'             => 'tag', 
					'rest_controller_class' => 'WP_REST_Terms_Controller'
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
					'public'                => false, 
					'show_ui'               => true, 
					'show_in_menu'          => false, 
					'show_in_nav_menus'     => false, 

					'show_tagcloud'         => false, 
					'show_admin_column'     => false,
					'hierarchical'          => false, 
					'query_var'             => is_admin(), 
				//	'capability_type'       => 'project', 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite'               => $rewrite, 
					'labels'                => $labels, 

					// WordPress 4.3
					'show_in_rest'          => false, 
					'rest_base'             => 'type'
				);

				$args = apply_filters( 'maxson_portfolio_taxonomy_type_args', $args );

				register_taxonomy( 'portfolio_type', array( self::POST_TYPE ), $args );
				register_taxonomy_for_object_type( 'portfolio_type', self::POST_TYPE );

			} // endif

			do_action( 'maxson_portfolio_after_register_taxonomy' );

		}


		/**
		 * Add the default terms for taxonomies - Portfolio Project Types
		 * 
		 * @return      void
		 */

		public static function create_terms()
		{ 
			$taxonomies = array( 
				'portfolio_type' => maxson_portfolio_get_project_types()
			);

			foreach( $taxonomies as $taxonomy => $terms )
			{ 
				foreach( $terms as $key => $value )
				{ 
					$slug = apply_filters( "maxson_portfolio_project_{$key}_default_slug", "{$key}-projects" );

					if( false === ( $term = get_term_by( 'slug', $slug, $taxonomy ) ) )
					{ 
						$term = wp_insert_term( $value, $taxonomy, array( 
							'slug' => $slug
						) );

						if( ! is_wp_error( $term ) )
						{ 
							$term_id = isset( $term->term_id ) ? $term->term_id : 0;

							add_term_meta( $term_id, '_project_type', $key, true );

						} // endif

						do_action( 'maxson_portfolio_insert_term', $term, $slug );

					} // endif
				} // endforeach
			} // endforeach
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Taxonomies();

?>