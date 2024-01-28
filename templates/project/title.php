<?php
/**
 * Single Project title
 * 
 * Override this template by copying it to yourtheme/portfolio/single-project/title.php
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

the_title( '<h1 itemprop="name" class="h1 entry-title project-title">', '</h1>' );