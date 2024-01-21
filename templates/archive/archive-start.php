<?php
/**
 * Content portfolio archive wrapper
 * 
 * @author      Thomas Maxson
 * @package 	Maxson_Portfolio_Projects/templates/archive
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif

$column_count = maxson_portfolio_get_option( 'archive_column_count' );

$column_class = apply_filters( 'maxson_portfolio_grid_projects_column_class', "columns-{$column_count}", $column_count );

?>
<ul id="portfolio-isotope" class="project-teasers <?php echo $column_class; ?>">