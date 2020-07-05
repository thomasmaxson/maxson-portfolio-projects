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

global $project;

?>
<div class="media">
	<?php echo $project->get_media(); ?>
</div>