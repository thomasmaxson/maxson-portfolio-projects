<?php
/**
 * Plugin-Specfic Admin Taxonomies
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Taxonomies' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Taxonomies { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'portfolio_category_pre_add_form', array( &$this, 'portfolio_category_description' ) );

			add_filter( 'manage_edit-portfolio_category_columns', array( &$this, 'taxonomy_columns' ) );
			add_filter( 'manage_portfolio_category_custom_column', array( &$this, 'taxonomy_content' ), 10, 3 );
			add_filter( 'manage_edit-portfolio_category_sortable_columns', array( &$this, 'taxonomy_sortable_columns' ) );

			add_action( 'portfolio_type_edit_form_fields', array( &$this, 'edit_project_type_fields' ), 10 );

			add_action( 'portfolio_category_add_form_fields', array( &$this, 'add_term_featured_image' ) );
			add_action( 'portfolio_category_edit_form_fields', array( &$this, 'edit_term_featured_image' ), 10 );

			add_action( 'created_term', array( &$this, 'save_taxonomy_fields' ), 10, 3 );
			add_action( 'edit_term', array( &$this, 'update_taxonomy_fields' ), 10, 3 );

			add_filter( 'wp_terms_checklist_args', array( &$this, 'disable_checked_ontop' ) );
		}


		/**
		 * Description for product_cat page to aid users
		 * 
		 * @return      void
		 */

		public function portfolio_category_description( $taxonomy )
		{ 
			echo wpautop( __( 'Project categories for your portfolio can be managed here.', 'maxson' ) );
		}


		/**
		 * Thumbnail column added to category admin.
		 *
		 * @param       mixed $columns
		 * @return      void
		 */

		public function taxonomy_columns( $existing_columns )
		{ 
			$columns = array();

			if( empty( $existing_columns ) || ! is_array( $existing_columns ) )
			{ 
				$existing_columns = array();

			} else
			{ 
				unset( $existing_columns['cb'] );

			} // endif

			$columns['cb'] = '<input type="checkbox" />';

			$columns['thumbnail'] = __( 'Thumbnail', 'maxson' );

			return array_merge( $columns, $existing_columns );
		}


		/**
		 * Thumbnail column value added to taxonomy admin
		 *
		 * @param       string $deprecated
		 * @param       string $column_name
		 * @param       int    $term_id
		 * @return      array
		 */

		public function taxonomy_content( $deprecated, $column_name, $term_id )
		{ 
			if( 'thumbnail' == $column_name )
			{ 
				$user_can = current_user_can( 'edit_term', $term_id );

				$size = apply_filters( 'maxson_portfolio_taxonomy_column_thumbnail_size', array( 60, 60 ) );
				$attr = apply_filters( 'maxson_portfolio_taxonomy_column_thumbnail_attrs', array() );

				$thumbnail_id = get_term_meta( $term_id, '_thumbnail_id', true );

				if( $thumbnail_id && maxson_portfolio_attachment_exists( $thumbnail_id ) )
				{ 
					$image = wp_get_attachment_image( absint( $thumbnail_id ), $size, $attr );

				} else
				{ 
					$image = maxson_portfolio_placeholder_image( null, $size, $attr );

				} // endif

				if( $user_can )
				{ 
					$term_obj = get_term( $term_id );
					$taxonomy = $term_obj->taxonomy;

					if( isset( $_GET['post_type'] ) )
					{ 
						$post_type = $_GET['post_type'];

					} else 
					{ 
						$taxonomy_obj = get_taxonomy( $taxonomy );

						$post_type = $taxonomy_obj->object_type[0];

					} // endif

					$uri = ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? wp_get_referer() : $_SERVER['REQUEST_URI'];

					// Set referrer URL - @see ../wp-admin/includes/class-wp-terms-list-table.php
					$edit_link = add_query_arg( array( 
						'wp_http_referer' => urlencode( wp_unslash( $uri ) )
					), get_edit_term_link( $term_id, $taxonomy, $post_type ) );

					printf( '<a href="%1$s">%2$s</a>', $edit_link, $image );

				} else
				{ 
					echo $image;

				} // endif
			} // endif
		}


		/**
		 * Make columns sortable
		 * 
		 * @param       string $columns
		 * @return      array
		 */

		function taxonomy_sortable_columns( $columns )
		{ 
			$custom_columns = array( 
				'thumbnail' => 'title'
			);

			return wp_parse_args( $custom_columns, $columns );
		}


		/**
		 * Edit taxonomy thumbnail field
		 *
		 * @param       mixed $term Term (category) being edited
		 * @return      void
		 */

		public function edit_project_type_fields( $term )
		{ 
			$project_type = maxson_portfolio_get_term_type( $term->term_id );

			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="portfolio-taxonomy-project-type"><?php _e( 'Type', 'maxson' ); ?></label></th>
				<td><input type="text" name="portfolio_taxonomy_project_type" class="large-text" id="portfolio-taxonomy-project-type" value="<?php echo esc_attr( $project_type ); ?>"></td>
			</tr>

			<?php 
		}


		/**
		 * Taxonomy thumbnail fields
		 * 
		 * @return      void
		 */

		public function add_term_featured_image()
		{ 
			$image = maxson_portfolio_placeholder_image_src();

			?>
			<div class="form-field term-thumbnail-wrap hide-if-no-js">
				<label><?php _e( 'Thumbnail', 'maxson' ); ?></label>
				<div class="portfolio-taxonomy-thumbnail">
					<img src="<?php echo esc_url( $image ); ?>">
				</div>
				<div>
					<input type="hidden" name="portfolio_taxonomy_thumbnail_id" id="portfolio-taxonomy-thumbnail-id" value="">
					<button type="button" class="portfolio-taxonomy-thumbnail-upload button button-secondary"><?php _e( 'Upload/Add image', 'maxson' ); ?></button>
					<button type="button" class="portfolio-taxonomy-thumbnail-remove button button-secondary hidden"><?php _e( 'Remove image', 'maxson' ); ?></button>
				</div>
			</div>

		<?php }


		/**
		 * Edit taxonomy thumbnail field
		 *
		 * @param       mixed $term Term (category) being edited
		 * @return      void
		 */

		public function edit_term_featured_image( $term )
		{ 
			$thumbnail_id = get_term_meta( $term->term_id, '_thumbnail_id', true );

			if( $thumbnail_id && maxson_portfolio_attachment_exists( $thumbnail_id ) )
			{ 
				$size = apply_filters( 'maxson_portfolio_taxonomy_edit_term_thumbnail_size', array( 60, 60 ) );

				$remove_button_class = '';
				$image = wp_get_attachment_image_url( absint( $thumbnail_id ), $size );

			} else
			{ 
				$remove_button_class = 'hidden';
				$image = maxson_portfolio_placeholder_image_src( null );

			} // endif

			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'maxson' ); ?></label></th>
				<td><div class="portfolio-taxonomy-thumbnail">
						<img src="<?php echo esc_url( $image ); ?>">
					</div>
					<div>
						<input type="hidden" name="portfolio_taxonomy_thumbnail_id" id="portfolio-taxonomy-thumbnail-id" value="<?php echo esc_attr( $thumbnail_id ); ?>">
						<button type="button" class="portfolio-taxonomy-thumbnail-upload button button-secondary"><?php _e( 'Upload/Add image', 'maxson' ); ?></button>
						<button type="button" class="portfolio-taxonomy-thumbnail-remove button button-secondary <?php echo $remove_button_class; ?>"><?php _e( 'Remove image', 'maxson' ); ?></button>
					</div>
				</td>
			</tr>

		<?php }


		/**
		 * Save the form field
		 * 
		 * @return      void
		*/

		public function save_taxonomy_fields( $term_id, $tt_id )
		{ 
			if( isset( $_POST['portfolio_taxonomy_thumbnail_id'] ) && '' !== $_POST['portfolio_taxonomy_thumbnail_id'] )
			{ 
				$thumbnail_id = $_POST['portfolio_taxonomy_thumbnail_id'];

				add_term_meta( $term_id, '_thumbnail_id', absint( $thumbnail_id ) );

			} // endif
		}


 		/**
		 * Save taxonomy function
		 *
		 * @param       mixed $term_id Term ID being saved
		 * @param       mixed $tt_id
		 * @param       string $taxonomy
		 */

		public function update_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' )
		{ 
			if( in_array( $taxonomy, get_object_taxonomies( array( self::POST_TYPE ) ) ) )
			{ 
				if( isset( $_POST['portfolio_taxonomy_project_type'] ) && '' !== $_POST['portfolio_taxonomy_project_type'] )
				{ 
					$project_type = $_POST['portfolio_taxonomy_project_type'];

					update_term_meta( $term_id, '_project_type', trim( $project_type ) );

				} else
				{ 
					delete_term_meta( $term_id, '_project_type' );

				} // endif


				if( isset( $_POST['portfolio_taxonomy_thumbnail_id'] ) && '' !== $_POST['portfolio_taxonomy_thumbnail_id'] )
				{ 
					$thumbnail_id = $_POST['portfolio_taxonomy_thumbnail_id'];

					update_term_meta( $term_id, '_thumbnail_id', absint( $thumbnail_id ) );

				} else
				{ 
					delete_term_meta( $term_id, '_thumbnail_id' );

				} // endif
			} // endif
		}


		/**
		 * Maintain term hierarchy when editing projects
		 * 
		 * @param       array $args
		 * @return      array
		 */

		public function disable_checked_ontop( $args )
		{ 
			if( ! empty( $args['taxonomy'] ) )
			{ 
				$array = apply_filters( 'maxson_portfolio_taxonomy_checklist_taxonomies', array( 'portfolio_category' ) );

				if( in_array( $args['taxonomy'], $array ) )
				{ 
					$args['checked_ontop'] = false;

				} // endif
			} // endif

			return $args;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Taxonomies();

?>