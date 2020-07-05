<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/portfolio/content-project-teaser.php
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
<li id="post-<?php the_ID(); ?>" <?php post_class( 'project-teaser' ); ?>>
	<?php /**
	 * maxson_portfolio_project_teaser_before_link hook
	 */

	do_action( 'maxson_portfolio_project_teaser_before_link' ); ?>

	<a href="<?php echo $project->get_permalink(); ?>" class="entry-link" rel="bookmark">
		<?php /**
		 * maxson_portfolio_project_teaser_before_summary hook
		 *
		 * @hooked maxson_portfolio_template_teaser_thumbnail
		 * @hooked maxson_portfolio_template_teaser_promoted_label
		 */

		do_action( 'maxson_portfolio_project_teaser_before_summary' ); ?>

		<div class="summary entry-summary">
			<?php /**
			 * maxson_portfolio_project_teaser_before_title hook
			 */

			do_action( 'maxson_portfolio_project_teaser_before_title' );

			echo $project->get_title( '<h3 class="entry-title project-title">', '</h3>' );

			/**
			 * maxson_portfolio_project_teaser_after_title hook
			 * 
			 * @hooked maxson_portfolio_template_teaser_terms
			 */

			do_action( 'maxson_portfolio_project_teaser_after_title' ); ?>
		</div><!-- .entry-summary -->

		<?php /**
		 * maxson_portfolio_project_teaser_after_summary hook
		 */

		do_action( 'maxson_portfolio_project_teaser_after_summary' ); ?>
	</a>

	<?php /**
	 * maxson_portfolio_project_teaser_after_link hook
	 */

	do_action( 'maxson_portfolio_project_teaser_after_link' ); ?>
</li>