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

$template = get_option( 'template' );

switch( $template )
{ 
	case 'twentytwelve': 
		echo '</div></div>';
		break;

	case 'twentythirteen': 
		echo '</div></div>';
		break;

	case 'twentyfourteen': 
		echo '</div>';
		echo '</div>';
		echo '</div>';
		get_sidebar( 'sidebar' );
		break;

	case 'twentyfifteen': 
		echo '</article>';
		echo '</div>';
		echo '</div>';
		break;

	case 'twentysixteen': 
		break;

	default: 
		echo '</div>';
		echo '</div>';
		echo '</div>';
		break;

} // endswitch