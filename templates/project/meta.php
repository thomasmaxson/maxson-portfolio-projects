<?php
/**
 * Single Project meta
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

$project_id = get_the_ID();

$client = maxson_project_get_client( $project_id );
$url    = maxson_project_get_url( $project_id );
$dates  = maxson_project_get_start_end_date_html( $project_id );
$tags   = maxson_project_get_tags( $project_id, array( 
	'link'       => false, 
	'separator'  => '</li><li>', 
	'before'     => '<ul class="project-tags">', 
	'after'      => '</ul>', 
	'beforeitem' => '<li>', 
	'afteritem'  => '</li>'
);

if( ! empty( $client ) || ! empty( $url ) || ! empty( $dates ) )
{ ?>
	<div class="entry-meta">
		<ul><?php 

			do_action( 'maxson_portfolio_meta_start' );

			if( ! empty( $dates ) )
				printf(  '<li><strong>%1$s</strong> <span class="project-date">%2$s</span></li>', __( 'Date: ', 'maxson' ), $dates );

			if( ! empty( $client ) && ! empty( $url ) )
			{ 
				printf(  '<li><strong>%1$s</strong> <a href="%2$s" target="_blank" class="project-url">%3$s</a></li>', __( 'Client: ', 'maxson' ), esc_url( $url ), $client );

			} elseif( ! empty( $client ) )
			{ 
				printf(  '<li><strong>%1$s</strong> <span class="project-client">%2$s</span></li>', __( 'Client: ', 'maxson' ), $client );

			} elseif( ! empty( $url ) )
			{ 
				printf(  '<li><strong>%1$s</strong> <a href="%2$s" target="_blank" class="project-url">%2$s</a></li>', __( 'URL: ', 'maxson' ), esc_url( $url ) );

			} // endif

			if( ! empty( $tags ) )
			{
				printf(  '<li><strong>%1$s</strong> %2$s</li>', __( 'Tagged: ', 'maxson' ), $tags );

			} // endif

			do_action( 'maxson_portfolio_meta_end' );

		?></ul>
	</div>

<?php } // endif ?>
