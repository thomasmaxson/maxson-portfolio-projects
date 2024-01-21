<?php
/**
 * The Template for displaying your portfolio, which is the "portfolio_project" post type archive
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-portfolio.php
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

/**
 * maxson_portfolio_before_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 */

do_action( 'maxson_portfolio_before_main_content' );


if( apply_filters( 'maxson_portfolio_show_archive_title', true ) )
{ 
	portfolio_projects_page_title( '<h1 class="entry-title">', '</h1>' );

} // endif


/**
 * maxson_portfolio_archive_description hook
 * 
 * @hooked maxson_portfolio_taxonomy_archive_description - 10
 * @hooked maxson_portfolio_posttype_archive_description - 10
 */

do_action( 'maxson_portfolio_archive_description' );

do_action( 'maxson_portfolio_archive_before_loop' );

if( have_posts() )
{ 
	while( have_posts() )
	{ 
		the_post();

		maxson_portfolio_template_part( 'content', 'project-teaser' );

	} // endwhile
} else
{ 
	maxson_portfolio_template_part( 'archive/none-found.php' );

} // endif

do_action( 'maxson_portfolio_archive_after_loop' );

do_action( 'maxson_portfolio_after_main_content' );

get_footer( 'portfolio' );

?>