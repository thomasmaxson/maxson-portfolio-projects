<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 * Override this template by copying it to yourtheme/portfolio/archive/pagination.php
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

global $wp_query;

if( $wp_query->max_num_pages != 1 )
{ 
	$args = apply_filters( 'maxson_portfolio_archive_settings_pagination_args', array(
		'base'      => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ), 
		'format'    => '', 
		'add_args'  => '', 
		'current'   => max( 1, get_query_var( 'paged' ) ), 
		'total'     => $wp_query->max_num_pages, 
		'prev_text' => '&lsaquo;', 
		'next_text' => '&rsaquo;', 
		'type'      => 'list', 
		'end_size'  => 3, 
		'mid_size'  => 3 
	) );

	?>
	<nav class="portfolio-projects-pagination">
		<?php echo paginate_links( $args ); ?>
	</nav>

<?php } // endif ?>