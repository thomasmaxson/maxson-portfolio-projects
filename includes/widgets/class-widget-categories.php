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


if( ! class_exists( 'Maxson_Portfolio_Projects_Widget_Categories' ) )
{ 
	class Maxson_Portfolio_Projects_Widget_Categories extends Maxson_Portfolio_Projects_Widget 
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
			$this->widget_cache       = 'widget_portfolio_projects_categories';
			$this->widget_id          = 'categories_portfolio_projects';
			$this->widget_class       = 'portfolio-projects-widget portfolio-projects-categories';
			$this->widget_title       = __( 'Categories - Portfolio Projects', 'maxson' );
			$this->widget_description = __( 'A list or dropdown of portfolio project categories.', 'maxson' );

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

			$count = ! empty( $instance['count'] ) ? true : false;
			$hierarchical = ! empty( $instance['hierarchical'] ) ? true : false;
			$dropdown = ! empty( $instance['dropdown'] ) ? true : false;

			ob_start();

			do_action( 'maxson_portfolio_widget_before_project_categories', $args );
			echo $before_widget;

			if( $title )
				echo $before_title . $title . $after_title;

			$args = array( 
				'orderby'      => 'name',
				'id'           => 'portfolio-cat',
	    		'name'         => 'portfolio-cat',
				'show_count'   => $count,
				'hierarchical' => $hierarchical, 
				'echo'         => true
			);


			if( $dropdown )
			{ 
				$args['show_option_none'] = __( 'Select Portfolio Category', 'maxson' );
				$args['class'] = 'postform form-control';
				$args['term_type'] = 'slug';

				$args = apply_filters( 'maxson_portfolio_widget_project_category_dropdown_args', $args );

				$args['walker'] = new Maxson_Portfolio_Projects_Cat_Dropdown_Walker();

				maxson_portfolio_dropdown_categories( $args );
			?>
<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById( 'portfolio-cat' );

	function onPortfolioCatChange(){ 
		if( dropdown.options[dropdown.selectedIndex].value != '' ){ 
			location.href = "<?php echo home_url(); ?>/?portfolio_category=" + dropdown.options[dropdown.selectedIndex].value;
		}
	}

	dropdown.onchange = onPortfolioCatChange;
/* ]]> */
</script>

			<?php } else 
			{ 
				$args['title_li'] = '';

				/**
				 * Filter the arguments for the Categories widget.
				 * 
				 * @since  		1.0
				 * 
				 * @param       array $args An array of Categories widget options.
				 */

				$args = apply_filters( 'maxson_portfolio_widget_project_category_list_args', $args );
			?>
				<ul class="menu">
					<?php maxson_portfolio_list_categories( $args ); ?>
				</ul>

			<?php } // endif

			echo $after_widget;
			do_action( 'maxson_portfolio_widget_after_project_categories', $args );

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

			$instance['dropdown']     = esc_attr( $new_instance['dropdown'] );
			$instance['count']        = esc_attr( $new_instance['count'] );
			$instance['hierarchical'] = esc_attr( $new_instance['hierarchical'] );

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
				'title'        => '',
				'dropdown'     => false,
				'count'	       => false,
				'hierarchical' => false
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
		?>
			<!-- Widget Title: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'maxson' ); ?></label>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
			</p>

			<p>
				<!-- Widget Dropdown: Checkbox Input -->
				<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>">
					<input type="checkbox" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" value="1" <?php checked( '1', $instance['dropdown'] ); ?> />
					<?php _e( 'Display as dropdown', 'maxson' ); ?>
				</label><br>

				<!-- Widget Show Count: Checkbox Input -->
				<label for="<?php echo $this->get_field_id( 'count' ); ?>">
					<input type="checkbox" name="<?php echo $this->get_field_name( 'count' ); ?>" id="<?php echo $this->get_field_id( 'count' ); ?>" value="1" <?php checked( '1', $instance['count'] ); ?> /> 
					<?php _e( 'Show project counts', 'maxson' ); ?>
				</label><br>

				<!-- Widget Hierarchical: Checkbox Input -->
				<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>">
					<input type="checkbox" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" value="1" <?php checked( '1', $instance['hierarchical'] ); ?> /> 
					<?php _e( 'Show hierarchy', 'maxson' ); ?>
				</label>
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