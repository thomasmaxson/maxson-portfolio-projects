<?php
/**
 * Portfolio Project Factory Class
 *
 * The Portfolio Project package factory creating the right project object
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Factory' ) )
{ 
	class Maxson_Portfolio_Projects_Factory { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * get_project function.
		 * 
		 * @param       bool  $the_project
		 * @param       array $args
		 * @return      Portfolio_Project|bool false if the project cannot be loaded
		 */

		public function get_project( $the_project = false, $args = array() )
		{ 
			$the_project = $this->get_project_object( $the_project );

			if( $the_project )
			{ 
				$classname = $this->get_project_class( $the_project, $args );

				if( ! class_exists( $classname ) )
				{
					$classname = 'Maxson_Portfolio_Projects_Project_Data';

				} // endif

				return new $classname( $the_project, $args );

			} else
			{ 
				return false;

			} // endif
		}


		/**
		 * Create a coding standards compliant class name e.g. Maxson_Portfolio_Projects_Type_Class, instead of Maxson_Portfolio_Projects_type-class
		 * 
		 * @param       string $portfolio_type
		 * @return      string|false
		 */

		public static function get_classname_from_project_type( $portfolio_type )
		{ 
			$type = implode( '_', array_map( 'ucfirst', explode( '-', $portfolio_type ) ) );

			return ( $portfolio_type ) ? "Maxson_Portfolio_Projects_Project_{$type}_Data" : false;
		}


		/**
		 * Get the project class name
		 * 
		 * @param       WP_Post $the_project
		 * @param       array   $args
		 * @return      string
		 */

		private function get_project_class( $the_project, $args = array() )
		{ 
			$project_id = absint( $the_project->ID );
			$post_type  = $the_project->post_type;

			if( self::POST_TYPE === $post_type )
			{ 
				if( isset( $args['portfolio_type'] ) )
				{ 
					$type = $args['portfolio_type'];

				} else
				{ 
					$terms = get_the_terms( $project_id, 'portfolio_type' );

					if( ! empty( $terms ) )
					{ 
						$term = current( $terms );

						$term_meta = maxson_portfolio_get_term_type( $term->term_id );

						$type = sanitize_title( $term_meta );

					} else
					{ 
						$type = 'none';

					} // endif
				} // endif
			} else
			{ 
				$type = 'none';

			} // endif

			$classname = $this->get_classname_from_project_type( $type );

			return apply_filters( 'maxson_portfolio_project_class', $classname, $type, $post_type, $project_id );
		}


		/**
		 * Get the project object
		 * 
		 * @param       mixed        $the_project
		 * @return      WP_Post|bool 
		 */

		private function get_project_object( $the_project )
		{ 
			if( false === $the_project )
			{ 
				$the_project = $GLOBALS['post'];

			} elseif( is_numeric( $the_project ) )
			{ 
				$the_project = get_post( $the_project );

			} elseif( ! ( $the_project instanceof WP_Post ) )
			{ 
				$the_project = false;

			} // endif

			return apply_filters( 'maxson_portfolio_project_object', $the_project );
		}

	}
} // endif

?>