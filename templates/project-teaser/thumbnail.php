<?php
/**
 * Project teaser media (image)
 * The Template for displaying project archives, including the portfolio which is a post type archive.
 * 
 * Override this template by copying it to yourtheme/portfolio/teaser-project/thumbnail.php
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
	<div class="entry-thumbnail">
		<?php echo get_the_post_thumbnail( $post, 'project_thumbnail' ); ?>
	</div>

<?php } // endif ?>