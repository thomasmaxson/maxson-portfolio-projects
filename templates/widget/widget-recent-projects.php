<?php
/**
 * This template is used to display the Recent Projects widget.
 */

global $recent_projects_widget_show_date;

?>
<a href="<?php the_permalink(); ?>">
	<?php get_the_title() ? the_title() : the_ID(); ?></a>
	<?php if( $recent_projects_widget_show_date )
	{ ?>
		<span class="project-date"><?php echo get_the_date(); ?></span>

	<?php } // endif ?>
</a>