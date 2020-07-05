<?php
/**
 * Displayed when nothing is found matching the current query.
 * 
 * Override this template by copying it to yourtheme/portfolio/loop/none-found.php
 * 
 * @author      Thomas Maxson
 * @package     Portfolio_Projects/templates
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif

?>
<p class="portfolio-projects-info"><?php _e( 'No projects were found.', 'maxson' ); ?></p>