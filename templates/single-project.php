 <?php
/**
 * The Template for displaying all single projects.
 * 
 * Override this template by copying it to yourtheme/portfolio/single-project.php
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

get_header( 'portfolio' );

do_action( 'maxson_portfolio_before_main_content' );

if( have_posts() )
{ 
	do_action( 'maxson_portfolio_single_settings_before_loop' );

	while( have_posts() )
	{ 
		the_post();

		maxson_portfolio_template_part( 'content', 'project' );

	} // endwhile

	do_action( 'maxson_portfolio_single_settings_after_loop' );

} else
{ 
	maxson_portfolio_template_part( 'loop/none-found.php' );

} // endif

do_action( 'maxson_portfolio_after_main_content' );

do_action( 'maxson_portfolio_sidebar' );

get_footer( 'portfolio' );

?>