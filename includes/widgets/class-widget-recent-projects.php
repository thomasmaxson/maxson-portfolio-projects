<?php
/**
 * Plugin-specific widget
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'add_action' ) ) 
	exit( "Hi there! I'm just a widget, not much I can do when called directly." );


if( ! class_exists( 'Maxson_Portfolio_Projects_Widget_Recent' ) )
{ 
	class Maxson_Portfolio_Projects_Widget_Recent extends Maxson_Portfolio_Projects_Widget 
	{ 
		/**
		 * Variable Declarations
		 */

		var $widget_cache;
		var $widget_id;
		var $widget_class;
		var $widget_title;
		var $widget_description;


		/**
		 * Construct
		 * 
		 * @since  		1.0
		 * 
		 * @return  	void
		 */

		function __construct() 
		{ 
			$this->widget_cache       = 'widget_portfolio_projects_recent';
			$this->widget_id          = 'recent_portfolio_projects';
			$this->widget_class       = 'portfolio-projects-widget portfolio-projects-recent-projects';
			$this->widget_title       = __( 'Recent Portfolio Projects', 'maxson' );
			$this->widget_description = __( "Your site's most recent portfolio projects.", 'maxson' );

			$widget_ops = array( 
				'classname'   => $this->widget_class,
				'description' => $this->widget_description
			);

			$control_ops = array( 
				'id_base' => $this->widget_id
			);

			parent::__construct( $this->widget_id, $this->widget_title, $widget_ops, $control_ops );
		}


		/**
		 * Display the widget on the frontend
		 * 
		 * @param  		array $args     Widget arguments.
		 * @param  		array $instance Widget settings for this instance.
		 * @return      void
		 */

		public function widget( $args, $instance )
		{ 
			global $recent_projects_widget_show_date;

			if( $this->get_cached_widget( $args ) )
				return;

			extract( $args );

			// Our variables from the widget settings
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->widget_id );
			$limit = ( ! empty( $instance['limit'] ) ) ? absint( $instance['limit'] ) : -1;

			$recent_projects_widget_show_date = ( isset( $instance['show_date'] ) ) ? $instance['show_date'] : false;

			$args = array( 
				'posts_per_page' => $limit
			);

			$args = maxson_portfolio_query_args( apply_filters( 'maxson_portfolio_widget_recent_args', $args ) );

			$query = new WP_Query( $args );

			ob_start();

			if( $query->have_posts() )
			{ 
				do_action( 'maxson_portfolio_widget_recent_projects_before', $args );
				echo $before_widget;

				if( $title )
					echo $before_title . $title . $after_title;

				?>
				<ul>
					<?php while( $query->have_posts() )
					{ 
						$query->the_post();

						echo '<li>';
						maxson_portfolio_template_part( 'widget', 'recent-projects' );
						echo '</li>';

					} // endwhile ?>
				</ul>
				<?php 

				echo $after_widget;
				do_action( 'maxson_portfolio_widget_recent_projects_after', $args );

				wp_reset_postdata();

			} // endif

			$content = ob_get_clean();

			echo $content;

			$this->cache_widget( $args, $content );
		}


		/**
		 * Method to update the settings from the form() method
		 * 
		 * @since  		1.0
		 * 
		 * @param 		array $new_instance New settings.
		 * @param 		array $old_instance Previous settings.
		 * @return 		array Updated settings.
		 */

		public function update( $new_instance, $old_instance )
		{ 
			$instance = $old_instance;

			$instance['title']     = strip_tags( $new_instance['title'] );

			$instance['limit']     = $new_instance['limit'];
			$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;

			// Flush cache
			$this->flush_widget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );

			if( isset( $alloptions[$this->widget_cache] ) )
			{
				delete_option( $this->widget_cache );

			} // endif

			return $instance;
		}


		/**
		 * The form on the widget control in the widget administration area
		 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff
		 * 
		 * @since  		1.0
		 * 
		 * @param  		array $instance The settings for this instance.
		 * @return      void
		 */

		public function form( $instance )
		{ 
			$defaults = array( 
				'title'     => '',
				'limit'     => 5,
				'show_date' => false
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
		?>
			<!-- Widget Title: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'maxson' ); ?></label>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
			</p>

			<!-- Widget limit: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'limit of projects to show:', 'maxson' ); ?></label>
				<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo $instance['limit']; ?>" id="<?php echo $this->get_field_id( 'limit' ); ?>" size="3" />
			</p>

			<!-- Widget date: Checkbox Input -->
			<p>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'show_date' ); ?>" id="<?php echo $this->get_field_id( 'show_date' ); ?>" value="1" <?php checked( '1', $instance['show_date'] ); ?> />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display project date?', 'maxson' ); ?></label>
			</p>
		<?php }


	} // endclass
} // endif

?>