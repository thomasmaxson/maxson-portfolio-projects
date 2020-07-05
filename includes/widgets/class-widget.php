<?php
/**
 * Plugin-specific Widget Class
 *
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio_Projects/includes/widgets
 * @license     GPL-2.0+
 * @version		1.0
 * @extends     WP_Widget
 */

if( ! class_exists( 'Maxson_Portfolio_Projects_Widget' ) )
{ 
	class Maxson_Portfolio_Projects_Widget extends WP_Widget { 

		/**
		 * Variables
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
			$widget_ops = array( 
				'classname'   => $this->widget_class,
				'description' => $this->widget_description
			);

			parent::__construct( $this->widget_id, $this->widget_title, $widget_ops );

			add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
		}


		/**
		 * get_cached_widget function
		 * 
		 * @param array $args
		 */

		function get_cached_widget( $args )
		{ 
			$cache = wp_cache_get( $this->widget_cache, 'widget' );

			if( ! is_array( $cache ) )
				$cache = array();

			if( ! isset( $args['widget_id'] ) )
				$args['widget_id'] = $this->widget_id;

			if( isset( $cache[$args['widget_id']] ) )
			{ 
				echo $cache[$args['widget_id']];
				return true;

			} // endif

			return false;
		}


		/**
		 * Cache the widget
		 * 
		 * @param string $content
		 */

		public function cache_widget( $args, $content )
		{ 
			$cache = wp_cache_get( $this->widget_cache, 'widget' );

			$cache[$this->widget_id] = $content;

			wp_cache_set( $this->widget_cache, $cache, 'widget' );
		}


		/**
		 * Flush the cache
		 */

		public function flush_widget_cache()
		{ 
			wp_cache_delete( $this->widget_id, 'widget' );
		}

	} // endclass
} // endif 

?>