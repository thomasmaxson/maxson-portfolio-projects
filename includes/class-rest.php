<?php
/**
 * Plugin-specific REST API Endpoints
 * 
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio/includes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


// http://localhost/wordpress/wp-json/portfolio-projects/v1/project/1267


if( ! class_exists( 'Maxson_Portfolio_Projects_REST_Posts_Controller' ) && 
	class_exists( 'WP_REST_Posts_Controller' ) )
{ 
	class Maxson_Portfolio_Projects_REST_Posts_Controller extends WP_REST_Posts_Controller { 

		/**
		 * Construct
		 */

		public function __construct( $post_type )
		{ 
			$version  = 2;
			$post_obj = get_post_type_object( $post_type );

			parent::__construct( $post_type );

			$this->post_type = $post_type;
			$this->namespace = 'portfolio-projects/v' . $version;
			$this->rest_base = ( isset( $post_obj->rest_base ) && ! empty( $post_obj->rest_base ) ) ? $post_obj->rest_base : 'project';

			add_action( "rest_prepare_{$this->post_type}", array( &$this, 'modify_response' ), 10, 3 );
		}


		/**
		 * Filter the post data for a response
		 * 
		 * @param       array           $data    An array of post data, prepared for response
		 * @param       WP_Post         $post    Post object
		 * @param       WP_REST_Request $request Request object
		 * @return      array
		 */

		public function modify_response( $response, $post, $context )
		{ 
			$data = $response->data;

			// make sure you've got the right custom post type
			if( $this->post_type !== $data['type'] )
			{ 
				return $response;

			} // endif

			// We only want to modify the 'view' context, for reading posts 
			if( is_wp_error( $response ) )
			{ 
				return $response;

			} // endif

			$post_id = $post->ID;


			$taxonomies = maxson_portfolio_get_taxonomy_types();

			if( ! empty( $taxonomies ) )
			{ 
				foreach( $taxonomies as $key => $label )
				{ 
					// Skip over if taxonomy doesn't exist in current data
					if( ! isset( $data[$key ] ) )
					{
						continue;

					} // endif

					$taxonomy = "portfolio_{$key}";
					$terms    = wp_get_post_terms( $post_id, $taxonomy );

					// If empty terms, or WP_Error, skip over
					if( empty( $terms ) || is_wp_error( $terms ) )
					{
						continue;

					} // endif

					$data[$key ] = array();

					foreach( $terms as $term )
					{ 
						$data[ $key ][] = array( 
							'id'   => $term->term_id, 
							'name' => $term->name, 
							'slug' => $term->slug, 
							'description' => term_description( $term, $taxonomy )
						);

					} // endforeach
				} // endforeach
			} // endif

			unset( 
				$data['featured_media'], 
				$data['meta']
			);

			$project = maxson_portfolio_get_project( $post );

			$project_type = $project->get_type( 'meta' );

			$meta_data = apply_filters( 'maxson_portfolio_rest_api_project_information', array( 
				'details' => array( 
					'type'       => $project_type, 
					'promoted'   => false, 
					'client'     => $project->get_client(), 
					'url'        => $project->get_url(), 
					'start_date' => array( 
						'raw'       => $project->get_start_date( 'raw' ), 
						'formatted' => $project->get_start_date(), 

						'year'  => $project->get_start_date( 'Y' ), 
						'month' => $project->get_start_date( 'm' ), 
						'day'   => $project->get_start_date( 'd' )
					), 
					'end_date'   => array( 
						'raw'       => $project->get_end_date( 'raw' ), 
						'formatted' => $project->get_end_date(), 

						'year'  => $project->get_end_date( 'Y' ), 
						'month' => $project->get_end_date( 'm' ), 
						'day'   => $project->get_end_date( 'd' )
					)
				), 
				'media' => $project->get_media_src()
			), $post_id, $post, $project );

			if( $project->is_promoted() )
			{
				$meta_data['details']['promoted'] = $project->get_promoted_label();

			} // endif

			$additional_data = apply_filters( 'maxson_portfolio_rest_api_additional_information', array(), $post_id, $post, $project );

			$response->data = array_merge( $data, $meta_data, $additional_data );

			return $response;
		}


		/**
		 * Register the routes for the objects of the controller
		 * 
		 * Nearly the same as WP_REST_Posts_Controller::register_routes(), but all of these
		 * endpoints are hidden from the index.
		 */

		public function register_routes()
		{ 
			register_rest_route( $this->namespace, "/{$this->rest_base}", array( 
				array( 
					'methods'       => WP_REST_Server::READABLE, 
					'callback'      => array( &$this, 'get_items' ), 
					'show_in_index' => false, 
					'args'          => array( 
						'context' => $this->get_context_param( array( 
							'default' => 'view'
						) ), 
						'per_page' => array( 
							'default'           => 10, 
							'sanitize_callback' => 'absint'
						)
					)
				), 
			//	array( 
			//		'methods'             => WP_REST_Server::CREATABLE, 
			//		'callback'            => array( $this, 'create_item' ), 
			//		'args'                => $this->get_collection_params(), 
			//		'permission_callback' => array( $this, 'create_item_permissions_check' ), 
			//		'show_in_index'       => true
			//	), 
				'schema' => array( $this, 'get_public_item_schema' )
			) );


			register_rest_route( $this->namespace, "/{$this->rest_base}/(?P<id>[\d]+)", array( 
				array( 
					'methods'       => WP_REST_Server::READABLE, 
					'callback'      => array( &$this, 'get_item' ), 
					'show_in_index' => false, 
					'args'          => array( 
						'context' => $this->get_context_param( array( 
							'default' => 'view'
						) )
					)
				), 
				array( 
					'methods'             => WP_REST_Server::EDITABLE, 
					'callback'            => array( $this, 'update_item' ), 
					'permission_callback' => array( $this, 'update_item_permissions_check' ), 
					'show_in_index'       => true, 
					'args'                => $this->get_endpoint_args_for_item_schema( false )
				), 
				array( 
					'methods'             => WP_REST_Server::DELETABLE, 
					'callback'            => array( $this, 'delete_item' ), 
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'show_in_index'       => false, 
					'args'                => array( 
						'force' => array( 
							'default'     => false, 
							'description' => __( 'Whether to bypass trash and force deletion.', 'maxson' )
						), 
						'reassign' => array(), 
					)
				), 
				'schema' => array( $this, 'get_public_item_schema' )
			) );


			register_rest_field( array( $this->post_type ), 'promoted', array( 
			//	'get_callback'    => array( &$this, 'get_meta' ), 
				'update_callback' => array( &$this, 'update_meta' ), 
				'schema'          => array( 
					'type'        => 'string', 
					'context'     => array( 'view', 'edit', 'embed' ), 
					'description' => __( 'Project promoted label', 'maxson' )
				)
			) );

			register_rest_field( array( $this->post_type ), 'client', array( 
			//	'get_callback'    => array( &$this, 'get_meta' ), 
				'update_callback' => array( &$this, 'update_meta' ), 
				'schema'          => array( 
					'type'        => 'string', 
					'context'     => array( 'view', 'edit', 'embed' ), 
					'description' => __( 'The name of the client for the project.', 'maxson' )
				)
			) );

			register_rest_field( array( $this->post_type ), 'url', array( 
			//	'get_callback'    => array( &$this, 'get_meta' ), 
				'update_callback' => array( &$this, 'update_meta' ), 
				'schema'          => array( 
					'type'        => 'string', 
					'context'     => array( 'view', 'edit', 'embed' ), 
					'description' => __( 'URL to the project website.', 'maxson' )
				)
			) );

			register_rest_field( array( $this->post_type ), 'start_date', array( 
			//	'get_callback'    => array( &$this, 'get_meta' ), 
				'update_callback' => array( &$this, 'update_meta' ), 
				'schema'          => array( 
					'type'        => 'string', 
					'context'     => array( 'view', 'edit', 'embed' ), 
					'description' => __( "The date the project started, in the site's timezone.", 'maxson' )
				)
			) );

			register_rest_field( array( $this->post_type ), 'end_date', array( 
			//	'get_callback'    => array( &$this, 'get_meta' ), 
				'update_callback' => array( &$this, 'update_meta' ), 
				'schema'          => array( 
					'type'        => 'string', 
					'context'     => array( 'view', 'edit', 'embed' ), 
					'description' => __( "The date the project ended, in the site's timezone.", 'maxson' )
				)
			) );
		}


		/**
		 * Callback for updating post meta
		 * 
		 * @param       array             $object     Details of current post
		 * @param       string            $field_name Name of field
		 * @param       WP_REST_request   $request    Current request
		 * @return      string
		 */

		public function get_meta( $object, $field_name, $request )
		{ 
			get_post_meta( $object->ID, "_{$field_name}", true );
		}


		/**
		 * Callback for updating post meta
		 * 
		 * @param       mixed  $value      The value of the field
		 * @param       object $object     The object from the response
		 * @param       string $field_name Name of field
		 * @return      string
		 */

		public function update_meta( $value, $object, $field_name )
		{ 
			$field_name = strtolower( $field_name );

			switch( $field_name )
			{ 
				case 'promoted': 
					if( ! empty( $value ) )
					{ 
						update_post_meta( $object->ID, '_promoted', true );
						update_post_meta( $object->ID, '_promoted_label', $value );

					} else
					{ 
						update_post_meta( $object->ID, '_promoted', false );
						delete_post_meta( $object->ID, '_promoted_label' );

					} // endif
					break;

				default: 
					if( ! $value )
					{ 
						return;

					} // endif

					update_post_meta( $object->ID, "_{$field_name}", $value );
					break;

			} // endswitch
		}

	} // endclass
} // endif


if( ! function_exists( 'maxson_portfolio_rest_register_routes' ) )
{ 
	/** 
	 * Function to register plugin routes from the controller
	 */

	function maxson_portfolio_rest_register_routes()
	{ 
		$portfolio_rest = new Maxson_Portfolio_Projects_REST_Posts_Controller( 'portfolio_project' );
			$portfolio_rest->register_routes();
	}
} // endif
add_action( 'rest_api_init', 'maxson_portfolio_rest_register_routes', 10 );

?>