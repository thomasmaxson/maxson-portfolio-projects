<?php
/**
 * This template is used to display the Featured Projects widget.
 */

?>
<a href="<?php the_permalink(); ?>">
	<?php get_the_title() ? the_title() : the_ID(); ?></a>
