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

global $project;

$client = $project->get_client();
$url    = $project->get_url();
$dates  = $project->get_start_end_date_html();
$tags   = $project->get_tags( '</li><li>', '<li>', '</li>', false );

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
				printf(  '<li><strong>%1$s</strong> <ul class="project-tags">%2$s</ul></li>', __( 'Tagged: ', 'maxson' ), $tags );

			} // endif

			do_action( 'maxson_portfolio_meta_end' );

		?></ul>
	</div>

<?php } // endif ?>
