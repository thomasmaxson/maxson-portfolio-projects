<?php
/**
 * Single Project media
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

if( has_post_thumbnail() )
{ ?>
	<figure class="entry-featured-media">
		<?php the_post_thumbnail( $post, 'project_large' ); ?>
	</figure>

<?php } // endif ?>