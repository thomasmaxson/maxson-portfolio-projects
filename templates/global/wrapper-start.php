<?php
/**
 * Content wrappers
 * 
 * @author      Thomas Maxson
 * @package 	Maxson_Portfolio_Projects/templates/global
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif

// https://twentytwelvedemo.wordpress.com/about-2/
// https://twentythirteendemo.wordpress.com/about/
// https://twentyfourteendemo.wordpress.com/about-2/
// https://twentyfifteendemo.wordpress.com/about-twenty-fifteen/

// http://pol-demo1.gogetthemes.com/?page_id=584
// http://simonswissbox.com/themeforest/booom-wp/portfolio/we-love-galleries/

$template = get_option( 'template' );

switch( $template )
{ 
	case 'twentytwelve': 
		echo '<div id="primary" class="site-content">';
		echo '<div id="content" role="main" class="twentytwelve">';
		break;

	case 'twentythirteen': 
		echo '<div id="primary" class="site-content">';
		echo '<div id="content" role="main" class="entry-content twentythirteen">';
		break;

	case 'twentyfourteen': 
		echo '<div id="primary" class="content-area">';
		echo '<div id="content" class="site-content twentyfourteen" role="main">';
		echo '<div class="entry-content">';
		break;

	case 'twentyfifteen': 
		echo '<div id="primary" class="content-area twentyfifteen" role="main">';
		echo '<div id="main" class="site-main">';
		echo '<article class="portfolio-projects hentry">';
		break;

	case 'twentysixteen': 
		break;

	default: 
		echo '<div class="container">';
		echo '<div id="content" role="main">';
		echo '<div class="portfolio-projects">';
		break;
}