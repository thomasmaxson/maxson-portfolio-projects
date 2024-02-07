<?php
/**
 * The template for looping through portfolios and displaying their content.
 * Override this template by copying it to yourtheme/portfolio/archive/filter.php
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Portfolio_Projects/templates
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif

$taxonomy = $attributes['taxonomy'];
$terms = get_terms( $taxonomy );

if( ! empty( $terms ) )
{ ?>
	<label class="control-label" for="portfolio-filter"><?php _ex( 'Sort by: ', 'Portfolio Sort Dropdown', 'maxson' ); ?></label>
	<select class="form-control portfolio-filter" id="portfolio-filter">
		<?php do_action( 'maxson_portfolio_archive_settings_filter_before_links', $terms, $taxonomy );

		if( $attributes['resetShow'] )
		{ 
			printf( '<option value="%1$s" data-filter="*">%2$s</option>', get_post_type_archive_link( 'portfolio_project' ), $attributes['resetLabel'] );

		} // endif

		foreach( $terms as $term )
		{ 
			$term_link = apply_filters( 'maxson_portfolio_archive_settings_filter_link', get_term_link( $term ), $term );

			if( is_wp_error( $term_link ) )
				continue;

			$term_slug  = apply_filters( 'maxson_portfolio_archive_settings_filter_slug', ".{$taxonomy}-{$term->slug}", $term, $taxonomy );
			$term_text  = apply_filters( 'maxson_portfolio_archive_settings_filter_text', $term->name, $term, $taxonomy );

			printf( '<option value="%1$s" data-filter="%2$s">%3$s</option>', esc_url( $term_link ), esc_attr( $term_slug ), $term_text );

		} // endforeach

		do_action( 'maxson_portfolio_archive_settings_filter_after_links', $terms, $taxonomy ); ?>
	</select>

<?php } // endif ?>