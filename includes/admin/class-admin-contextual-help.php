<?php
/**
 * Plugin-specific Admin Contextual Help
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Contextual_Help' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Contextual_Help { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'load-portfolio_project_page_portfolio_settings', array( &$this, 'settings_contextual_help' ) );
		}


		/**
		 * Settings contextual help
		 * 
		 * @return      void
		 */

		public function settings_contextual_help()
		{ 
			$screen = get_current_screen();

		//	$screen->set_help_sidebar( 
		//		sprintf( '<p><strong>%1$s</strong></p>', __( 'For more information:', 'maxson' ) )
		//	);

			$screen->add_help_tab( array( 
				'id'      => 'maxson-portfolio-settings-archive', 
				'title'   => __( 'Archive', 'maxson' ), 
				'content' => '<p>' . __( 'This screen is used for managing your Portfolio archive.', 'maxson' ) . '</p>' . 
					'<p>' . __( 'From this screen you can:', 'maxson' ) . '</p>' . 
					'<ul>' . 
					'<li>' . __( 'Set your portfolio project archive page', 'maxson' ) . '</li>' . 
					'<li>' . __( 'Modify quantity and order of projects on archive pages', 'maxson' ) . '</li>' . 
					'</ul>'
			) );


			$screen->add_help_tab( array( 
				'id'      => 'maxson-portfolio-settings-setup', 
				'title'   => __( 'Setup', 'maxson' ), 
				'content' => '<p>' . __( 'This screen is used for managing Project fields and Portfolio taxonomies.', 'maxson' ) . '</p>' . 
					'<p>' . __( 'From this screen you can:', 'maxson' ) . '</p>' . 
					'<ul>' . 
					'<li>' . __( 'Enable/disable various portfolio taxonomies', 'maxson' ) . '</li>' . 
					'</ul>'
			) );

			do_action( 'maxson_portfolio_settings_contextual_help', $screen );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Contextual_Help();

?>