<?php
/**
 * Plugin-Specfic Admin AJAX
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Ajax' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Ajax { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'wp_ajax_portfolio_project_promoted_callback', array( 'Maxson_Portfolio_Projects_Meta_Box_Project_Promoted', 'ajax_save' ) );

			add_action( 'wp_ajax_portfolio_project_process_gallery_images', array( 'Maxson_Portfolio_Projects_Meta_Box_Project_Gallery', 'get_gallery_images' ) );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Ajax();

?>