<?php
/**
 * Plugin-specific Blocks
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/blocks
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Block_Portfolio' ) )
{ 
	class Maxson_Portfolio_Projects_Block_Portfolio { 

		/**
		 * Construct
		 */

		public function __construct()
		{ 
			if( ! function_exists( 'register_block_type' ) )
			{ 
				return;

			} // endif

			add_action( 'init', array( &$this, 'init_block' ) );
		}


		/**
		 * Initialize custom block
		 */

		function init_block()
		{ 
			register_block_type( __DIR__ . '/build', array( 
				'render_callback' => array( $this, 'render_block' )
			) );
		}


		/**
		 * 
		 */

		function get_query_args( $attributes )
		{ 
			$args = array();

			if( isset( $attributes['numberOfItems'] ) && $attributes['numberOfItems'] )
			{ 
				$args['posts_per_page'] = $attributes['numberOfItems'];

			} // endif


			if( isset( $attributes['orderby'] ) && $attributes['orderby'] )
			{ 
				$args['orderby'] = $attributes['orderby'];

			} // endif


			if( isset( $attributes['order'] ) && $attributes['order'] )
			{ 
				$args['order'] = $attributes['order'];

			} // endif

			return maxson_portfolio_query_args( $args );
		}


		/**
		 * 
		 */

		function get_column_count( $attributes )
		{ 
			$count = 1;

			if( $attributes[ 'columns' ] > 1 )
			{ 
				$count = strval( $attributes[ 'columns' ] );

			} // endif

			return $count;
		}


		/**
		 * Render custom block
		 */

		function render_block( $attributes, $content, $block )
		{ 
			$output = '';

			$args = $this->get_query_args( $attributes );

			$query = new WP_Query( $args );

			if( $query->have_posts() )
			{ 
				$columns = $this->get_column_count( $attributes );

				$classes = array(
					'wp-block-portfolio-archive', 
					sprintf( 'portfolio-grid-columns-%1$s', $columns )
				);

				if( isset( $attributes['className'] ) && $attributes['className'] )
				{ 
					array_push( $classes, $attributes['className'] );

				} // endif

				$output .= sprintf( '<ul class="%1$s" %2$s>', join( ' ', $classes ), get_block_wrapper_attributes() );

				while( $query->have_posts() )
				{ 
					$query->the_post();

					ob_start();

					maxson_portfolio_template_part( 'content', 'project-teaser' );

					$output .= ob_get_contents();

					ob_end_clean();

				} // endwhile

				$output .= '</ul>';

			} else
			{ 
				$output .= esc_html_e( 'Sorry. No projects were found matching the criteria provided.', 'maxson' );

			} // endif

			wp_reset_postdata();

			return $output;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Block_Portfolio();

?>