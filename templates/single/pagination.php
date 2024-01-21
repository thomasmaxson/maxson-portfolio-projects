<?php
/**
 * Single Project pagination
 * 
 * @author      Thomas Maxson
 * @package     Maxson_Portfolio_Projects/includes/templates
 * @license     GPL-2.0+
 * @version     1.0
 */

if( ! maxson_portfolio_get_option( 'single', 'pagination' ) )
	return false;

$title_attr_before = __( 'View Project: ', 'maxson' );
$title_attr_after = '';

$prev_post = get_adjacent_post( false, '', true );
$next_post = get_adjacent_post( false, '', false );

?>
<nav class="navigation project-navigation">
	<div class="previous">
		<?php if( ! empty( $prev_post ) )
		{ 
			$prev_id = $prev_post->ID;

			$permalink  = get_permalink( $prev_id );
			$title      = get_the_title( $prev_id );
			$title_attr = the_title_attribute( array( 
				'before' => $title_attr_before, 
				'after'  => $title_attr_after,
				'post'   => $prev_id, 
				'echo'   => false
			) );

			printf( '<a href="%1$s" title="%2$s" rel="prev">%3$s</a>', $permalink, $title_attr, $title );

		} // endif ?>
	</div>
	<div class="next">
		<?php if( ! empty( $next_post ) )
		{ 
			$next_id = $next_post->ID;

			$permalink  = get_permalink( $next_id );
			$title      = get_the_title( $next_id );
			$title_attr = the_title_attribute( array( 
				'before' => $title_attr_before, 
				'after'  => $title_attr_after,
				'post'   => $next_id, 
				'echo'   => false
			) );

			printf( '<a href="%1$s" title="%2$s" rel="next">%3$s</a>', $permalink, $title_attr , $title );

		} // endif ?>
	</div>
</nav><!-- .project-navigation -->