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

if( maxson_project_has_callout() )
{ ?>
	<div class="entry-promoted-label">
		<?php echo maxson_project_get_callout_label(); ?>
	</div>

<?php } // endif ?>