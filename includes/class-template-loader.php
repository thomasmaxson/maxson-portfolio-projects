<?php
/**
 * Plugin-specific template loader
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Template_Loader' ) )
{ 
	class Maxson_Portfolio_Projects_Template_Loader { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_filter( 'archive-portfolio_project_template', array( &$this, 'archive_template' ) );
			add_filter( 'single-portfolio_project_template', array( &$this, 'single_template' ) );
		}


		/**
		 * Change the template name for archive portfolio projects
		 * 
		 * @param       string $template Path to the template. See {@see locate_template()}.
		 *
		 * @return      string
		 */

		public function archive_template( $template )
		{ 
			return 'archive-portfolio';
		}
			

		/**
		 * Change the template name for single portfolio projects
		 * 
		 * @param       string $template Path to the template. See {@see locate_template()}.
		 *
		 * @return      string
		 */

		public function single_template( $template )
		{ 
			return 'single-project';
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Template_Loader();

?>