<?php
/**
 * Single Related Projects
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


$column_count = maxson_portfolio_get_option( 'archive_column_count' );

$column_class = apply_filters( 'maxson_portfolio_related_projects_column_class', "columns-{$column_count}", $column_count );


$related_projects = $project->get_related( $column_count );


if( $related_projects && apply_filters( 'maxson_portfolio_show_related_projects', '__return_true' ) )
{ 
	$args = apply_filters( 'maxson_portfolio_single_settings_related_args', array( 
		'post_type'           => 'portfolio_project', 
		'ignore_sticky_posts' => 1, 
		'no_found_rows'       => 1, 
		'posts_per_page'      => $column_count, 
		'post__in'            => $related_projects, 
		'post__not_in'        => array( $project->id ), 
		'orderby'             => 'post__in', 
		'meta_query'          => array( 
			array( 
				'key'     => '_thumbnail_id', 
				'compare' => 'EXISTS'
			)
		)
	) );

	$projects = new WP_Query( $args );

	if( $projects->have_posts() )
	{ ?>
		<div class="related-projects">
			<h2 class="h2 entry-subtitle"><?php echo apply_filters( 'maxson_portfolio_single_settings_related_projects_title', __( 'Related Projects', 'maxson' ) ); ?></h2>
			<ul class="project-teasers <?php echo $column_class; ?>">
			<?php while( $projects->have_posts() )
			{ 
				$projects->the_post();

				maxson_portfolio_template_part( 'content', 'project-teaser' );

			} // endwhile ?>
			</ul>
		</div>

	<?php } // endif

	wp_reset_postdata();

} // endif

?>