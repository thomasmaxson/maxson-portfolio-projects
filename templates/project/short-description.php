<?php
/**
 * Single Project short description
 * 
 * Override this template by copying it to yourtheme/portfolio/single-project/short-description.php
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

global $post;

if( $post->post_excerpt )
{ ?>
	<div itemprop="description">
		<?php echo apply_filters( 'maxson_portfolio_short_description', $post->post_excerpt ) ?>
	</div>

<?php } // endif ?>