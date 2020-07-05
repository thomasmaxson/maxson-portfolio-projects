<?php
/**
 * Plugin-specific taxonomies
 * 
 * @author      Bluegreen Creative Group
 * @category    Class
 * @package     Bluegreen_Experiences/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Widgets' ) )
{ 
	class Maxson_Portfolio_Projects_Widgets { 

		/**
		 * Constructor
		 * 
		 * @return      void
		 */

		public function __construct()
		{ 
			$this->includes();

			add_action( 'widgets_init', array( &$this, 'register' ) );
		}


		/**
		 * Include widget files
		 */

		public function includes()
		{ 
			$includes_folder = MAXSON_PORTFOLIO_INCLUDES;

			include_once( "{$includes_folder}widgets/class-widget.php" );

			include_once( "{$includes_folder}widgets/class-widget-categories.php" );
			include_once( "{$includes_folder}widgets/class-widget-tags.php" );
			include_once( "{$includes_folder}widgets/class-widget-promoted-projects.php" );
			include_once( "{$includes_folder}widgets/class-widget-recent-projects.php" );
		}


		/**
		 * Register widgets
		 */

		public function register()
		{ 
			if( maxson_portfolio_taxonomy_exists( 'category' ) )
			{ 
				register_widget( 'Maxson_Portfolio_Projects_Widget_Categories' );

			} // endif


			if( maxson_portfolio_taxonomy_exists( 'tag' ) )
			{ 
				register_widget( 'Maxson_Portfolio_Projects_Widget_Tags' );

			} // endif


			if( maxson_portfolio_get_option( 'setup_promoted' ) )
			{ 
				register_widget( 'Maxson_Portfolio_Projects_Widget_Promoted' );

			} // endif


			register_widget( 'Maxson_Portfolio_Projects_Widget_Recent' );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Widgets;

?>