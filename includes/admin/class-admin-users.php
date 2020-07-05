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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Users' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Users { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_filter( 'manage_users_columns', array( &$this, 'add_users_custom_column' ) );
			add_filter( 'manage_users_custom_column', array( &$this, 'output_users_custom_column' ), 10, 3 );
		}


		/**
		 * Add an extra column to the users table
		 * 
		 * @param	    mixed $columns
		 * @return	    array
		 */

		public function add_users_custom_column( $columns )
		{ 
			$columns['portfolio_project_count column-posts num'] = __( 'Projects', 'maxson' );

			return $columns;
		}


		/**
		 * Filter the display output of custom columns in the Users list table.
		 *
		 * @param       string $output      Custom column output. Default empty
		 * @param       string $column_name Column name
		 * @param       int    $user_id     ID of the currently-listed user
		 * @return	    array
		 */

		public function output_users_custom_column( $value, $column_name, $user_id )
		{ 
			if( self::POST_TYPE . '_count column-posts num' == $column_name )
			{ 
				$user_post_count = count_user_posts( $user_id, self::POST_TYPE );

				$count = apply_filters( 'maxson_portfolio_user_column_count', $user_post_count, $user_id );

				if( ( $count > 0 ) && current_user_can( 'edit_posts' ) )
				{ 
					$query_args = array( 
						'post_status' => 'publish', 
						'post_type'   => self::POST_TYPE, 
						'author'      => $user_id
					);

					$value = sprintf( '<a href="%1$s" class="user-%2$s-count">%3$s</a>', add_query_arg( $query_args, 'edit.php' ), self::POST_TYPE, $count );

				} else
				{ 
					$value = sprintf( '<span class="user-%2$s-count">%2$s</span>', self::POST_TYPE, $count );

				} // endif

				$value = apply_filters( 'maxson_portfolio_user_column_count_html', $value, $user_id );

			} // endif

			return $value;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Users();

?>