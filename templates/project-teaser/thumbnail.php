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

global $project;

?>
<div class="entry-thumbnail">
	<?php echo $project->get_thumbnail( 'project_thumbnail' ); ?>
</div>