<?php
/**
 * Plugin-specific template functions
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


if( ! function_exists( 'maxson_portfolio_get_project_teaser_thumbnail' ) )
{ 
	/**
	 * Get the project teaser thumbnail
	 */

	 function maxson_portfolio_get_project_teaser_thumbnail()
	 { 
		echo maxson_portfolio_template( 'project-teaser/thumbnail.php' );
	}
}


if( ! function_exists( 'maxson_portfolio_get_project_teaser_callout' ) )
{ 
	/**
	 * Get the project teaser callout
	 */

	 function maxson_portfolio_get_project_teaser_callout()
	 { 
		echo maxson_portfolio_template( 'project-teaser/callout-label.php' );
	}
}


if( ! function_exists( 'maxson_portfolio_get_project_teaser_terms' ) )
{ 
	/**
	 * Get the project teaser callout
	 */

	 function maxson_portfolio_get_project_teaser_terms()
	 { 
		echo maxson_portfolio_template( 'project-teaser/terms.php' );
	}
}

?>