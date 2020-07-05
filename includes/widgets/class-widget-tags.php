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


if( ! class_exists( 'Maxson_Portfolio_Projects_Widget_Tags' ) )
{ 
	class Maxson_Portfolio_Projects_Widget_Tags extends Maxson_Portfolio_Projects_Widget 
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

		public function __construct()
		{ 
			$this->widget_cache       = 'widget_portfolio_projects_tags';
			$this->widget_id          = 'tags_portfolio_projects';
			$this->widget_class       = 'portfolio-projects-widget portfolio-projects-tag-cloud';
			$this->widget_title       = __( 'Tag Cloud - Portfolio Projects', 'maxson' );
			$this->widget_description = __( 'A cloud of your most used portfolio project tags.', 'maxson' );

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
		 * @since  		1.0
		 * 
		 * @param  		array $args     Widget arguments.
		 * @param  		array $instance Widget settings for this instance.
		 * @return      void
		 */

		public function widget( $args, $instance )
		{ 
			if( $this->get_cached_widget( $args ) )
				return;

			extract( $args );

			// This filter is documented in wp-includes/default-widgets.php
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->widget_id );

			ob_start();

			do_action( 'maxson_portfolio_widget_before_project_tags', $args );
			echo $before_widget;

			if( $title )
				echo $before_title . $title . $after_title;

			$args = apply_filters( 'maxson_portfolio_widget_tag_cloud_args', array() );

			// Cannot override
			$args['taxonomy'] = 'portfolio_tag';
			$args['echo']     = true;

			wp_tag_cloud( $args );

			echo $after_widget;
			do_action( 'maxson_portfolio_widget_after_project_tags', $args );

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

			$instance['title']        = strip_tags( $new_instance['title'] );

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
				'title' => ''
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
		?>
			<!-- Widget Title: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'maxson' ); ?></label>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
			</p>

		<?php }


		/**
		 * Flush widget cache
		 * 
		 * @since  		1.0
		 * 
		 * @return      void
		 */

		public function flush_widget_cache()
		{
			wp_cache_delete( $this->widget_cache, 'widget' );
		}


	} // endclass
} // endif

?>