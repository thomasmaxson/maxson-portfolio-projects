<?php
/**
 * Plugin-specific template hooks
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


/**
 * Before portfolio content
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_before_main_content', 'portfolio_projects_output_content_wrapper', 10 );


/**
 * After portfolio content
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_after_main_content', 'portfolio_projects_output_content_wrapper_end', 10 );




/**
 * Before portfolio loop
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_archive_settings_before_loop', 'maxson_portfolio_get_archive_setting_description', 10 );
add_action( 'maxson_portfolio_archive_settings_before_loop', 'maxson_portfolio_template_portfolio_archive_filter', 20 );
add_action( 'maxson_portfolio_archive_settings_before_loop', 'maxson_portfolio_template_archive_start', 30 );


/**
 * After portfolio loop
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_archive_settings_after_loop', 'maxson_portfolio_template_archive_end', 10 );
add_action( 'maxson_portfolio_archive_settings_after_loop', 'maxson_portfolio_template_portfolio_archive_pagination', 20 );




/** 
 * Project Teasers
 */

/**
 * Before project teaser summary
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_project_teaser_before_summary', 'maxson_portfolio_template_teaser_thumbnail', 10 );
add_action( 'maxson_portfolio_project_teaser_before_summary', 'maxson_portfolio_template_teaser_promoted_label', 20 );


/**
 * After project teaser summary
 * 
 * @return      void
 */

// NULL


/**
 * Before project teaser title
 * 
 * @return      void
 */

// NULL



/**
 * After project teaser title
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_project_teaser_after_title', 'maxson_portfolio_template_teaser_terms', 30 );




/** 
 * Project Details
 */

/**
 * Before project loop
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_single_settings_before_loop', 'maxson_portfolio_template_single_password', 5 );
add_action( 'maxson_portfolio_single_settings_before_loop', 'maxson_portfolio_template_single_start', 10 );


/**
 * After project loop
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_single_settings_after_loop', 'maxson_portfolio_template_single_end', 10 );
add_action( 'maxson_portfolio_single_settings_after_loop', 'maxson_portfolio_template_single_share', 15 );
add_action( 'maxson_portfolio_single_settings_after_loop', 'maxson_portfolio_template_single_pagination', 15 );
add_action( 'maxson_portfolio_single_settings_after_loop', 'maxson_portfolio_template_single_related', 20 );


/**
 * Before project content
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_single_settings_before_project_content', 'maxson_portfolio_template_single_media', 5 );


/**
 * Project content
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_single_settings_project_content', 'maxson_portfolio_template_single_promoted_label', 5 );
add_action( 'maxson_portfolio_single_settings_project_content', 'maxson_portfolio_template_single_title', 10 );
add_action( 'maxson_portfolio_single_settings_project_content', 'maxson_portfolio_template_single_description', 15 );
add_action( 'maxson_portfolio_single_settings_project_content', 'maxson_portfolio_template_single_meta', 20 );


/**
 * Project sidebar
 * 
 * @return      void
 */

add_action( 'maxson_portfolio_sidebar', 'portfolio_projects_get_sidebar', 5 );


/**
 * After project content
 * 
 * @return      void
 */

?>