<?php
/**
 * Plugin-specific Admin Dashboard
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Dashboard' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Dashboard { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * @var         Plugin post type singular name
		 */

		public $singular_name;


		/**
		 * @var         Plugin post type plural name
		 */

		public $plural_name;


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'right_now_content_table_end', array( &$this, 'right_now_widget' ) );
			add_action( 'dashboard_glance_items', array( &$this, 'glace_widget' ) );
		}


		/**
		 * Add plugin-specific dashboard information (Pre-WordPress 3.6)
		 * 
		 * @return      void
		 */

		public function right_now_widget()
		{ 
			if( ! post_type_exists( self::POST_TYPE ) )
			{
				return;

			} // endif

			$post_object = get_post_type_object( self::POST_TYPE );
				$this->singular_name = $post_object->labels->singular_name;
				$this->plural_name   = $post_object->labels->name;

			$post_count = wp_count_posts( self::POST_TYPE );

			$count = number_format_i18n( $post_count->publish );
			$text  = _n( "{$this->singular_name} Item", "{$this->plural_name} Items", intval( $post_count->publish ) );

			if( current_user_can( 'edit_posts' ) )
			{ 
				$link  = add_query_arg( 'post_type', self::POST_TYPE, 'edit.php' );

				$count = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $link ), $count );
				$text  = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $link ), $text );

			} // endif

			printf( '<td class="first b b-%1$s">%2$s</td>', self::POST_TYPE, $count );
			printf( '<td class="t %1$s">%2$s</td>', self::POST_TYPE, $text );
			echo '</tr>';

			if( $post_count->pending > 0 )
			{ 
				$pending_count = number_format_i18n( $post_count->pending );
				$pending_text  = _n( "{$this->singular_name} Item Pending", "{$this->plural_name} Items Pending", intval( $post_count->pending ), 'maxson' );

				if( current_user_can( 'edit_posts' ) )
				{ 
					$pending_link  = add_query_arg( array( 'post_type' => self::POST_TYPE, 'post_status' => 'pending' ), 'edit.php' );
					$pending_count = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $pending_link ), $pending_count );
					$pending_text  = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $pending_link ), $text );

				} // endif

				printf( '<td class="first b b-%1$s">%2$s</td>', self::POST_TYPE, $pending_count );
				printf( '<td class="t %1$s">%2$s</td>', self::POST_TYPE, $pending_text );
				echo '</tr>';

			} // endif
		}
		

		/**
		 * Add plugin-specific dashboard information (WordPress 3.6+)
		 * 
		 * @return      void
		 */

		public function glace_widget()
		{ 
			if( current_user_can( 'edit_posts' ) )
			{ 
				$post_object = get_post_type_object( self::POST_TYPE );
					$this->singular_name = $post_object->labels->singular_name;
					$this->plural_name   = $post_object->labels->name;

				$post_count = wp_count_posts( self::POST_TYPE );

				$count = number_format_i18n( $post_count->publish );
				$text  = _n( $this->singular_name, $this->plural_name, intval( $post_count->publish ), 'maxson' );
				$href  = add_query_arg( array( 
					'post_type' => self::POST_TYPE
				), 'edit.php' );

				printf( '<li class="page-count %1$s-count"><a href="%2$s">%3$s %4$s</a></li>', self::POST_TYPE, $href, $count, $text );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Dashboard();

?>