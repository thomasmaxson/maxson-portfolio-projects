<?php
/**
 * Project teaser promoted label, including microdata for SEO
 * The Template for displaying project archives, including the portfolio which is a post type archive.
 * 
 * Override this template by copying it to yourtheme/portfolio/teaser-project/promoted-label.php
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

if( $project->is_promoted() )
{ ?>
	<div class="entry-promoted-label">
		<?php echo $project->get_promoted_label(); ?>
	</div>

<?php } // endif ?>