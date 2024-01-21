<?php
/**
 * Project teaser terms
 * The Template for displaying project archives, including the portfolio which is a post type archive.
 * 
 * Override this template by copying it to yourtheme/portfolio/teaser-project/terms.php
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

echo maxson_project_data_term_categories( get_the_ID(), array( 
	'link'      => false, 
	'separator' => ' / ', 
	'before'    => '<div class="entry-meta">', 
	'after'     => '</div>'
) );

?>