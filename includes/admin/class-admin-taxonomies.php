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

			add_action( 'portfolio_type_edit_form_fields', array( &$this, 'edit_project_type_fields' ), 10 );

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