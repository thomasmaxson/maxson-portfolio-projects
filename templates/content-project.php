w=<?php
/**
 * The Template for displaying all single projects
 * 
 * Override this template by copying it to yourtheme/portfolio/content-project.php
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

?>
<div id="post-<?php the_ID(); ?>" <?php post_class( 'project' ); ?>>
	<?php /**
	 * maxson_portfolio_get_single_setting_before_project_content hook
	 *
	 * @hooked maxson_portfolio_template_single_media
	 */

	do_action( 'maxson_portfolio_single_settings_before_project_content' ); ?>

	<div class="summary entry-summary">
		<?php /**
		 * maxson_portfolio_get_single_setting_project_content hook
		 *
		 * @hooked maxson_portfolio_template_single_promoted_label
		 * @hooked maxson_portfolio_template_single_title
		 * @hooked maxson_portfolio_template_single_description
		 * @hooked maxson_portfolio_template_single_meta
		 */
		
		do_action( 'maxson_portfolio_single_settings_project_content' ); ?>

	</div><!-- .summary -->
	<?php /**
	 * maxson_portfolio_get_single_setting_after_project_content hook
	 * 
	 * @hooked maxson_portfolio_template_single_share
	 * @hooked maxson_portfolio_template_single_related
	 */

	do_action( 'maxson_portfolio_single_settings_after_project_content' ); ?>

	<meta itemprop="url" content="<?php the_permalink(); ?>">
</div>