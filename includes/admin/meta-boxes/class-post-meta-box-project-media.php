<?php
/**
 * Plugin-specific meta box
 *
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio_Projects/includes/admin/meta-boxes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Project_Media' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Project_Media { 

		/**
		 * The media type term slug
		 * 
		 * @var string
		 */

		public $type = '';


		/**
		 * Meta box title
		 * 
		 * @var         string
		 */

		public $title = false;


		/**
		 * Meta box post type
		 * 
		 * @var         string
		 */

		public $post_type = 'portfolio_project';


		/**
		 * Meta box context
		 * 
		 * @var         string
		 */

		public $context = 'normal';


		/**
		 * Meta box priority
		 * 
		 * @var         string
		 */

		public $priority = 'high';


		/**
		 * Meta box callback args
		 * 
		 * @var         string
		 */

		public $callback_args = array();


		/**
		 * Script minified file
		 * 
		 * @var         string
		 */

		public $min = false;


		/**
		 * Contruct
		 */

		public function __construct()
		{ 
			$this->_setup();

			add_action( 'admin_enqueue_scripts', array( $this, '_add_styles' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $this, '_add_scripts' ), 10 );

			add_action( 'add_meta_boxes', array( $this, '_add' ), 11 );
			add_action( 'maxson_portfolio_meta_box_save_details', array( $this, '_save' ), 10, 2 );
		}


		/**
		 * Setup meta box variables
		 */

		public function _add_styles()
		{ 
			return false;
		}


		/**
		 * Setup meta box variables
		 */

		public function _add_scripts()
		{ 
			return false;
		}


		/**
		 * Setup meta box variables
		 */

		public function _setup()
		{ 
			return false;
		}


		/**
		 * Add meta box
		 */

		public function _add()
		{ 
			if( ! empty( $this->type ) && ! empty( $this->title ) )
			{ 
				add_meta_box( "meta_box_project_type_{$this->type}", $this->title, array( &$this, '_render' ), $this->post_type, $this->context, $this->priority, $this->callback_args );

			} // endif
		}


		/**
		 * Display meta box
		 */

		public function _render( $post, $metabox )
		{ 
			return false;
		}


		/**
		 * Save meta box
		 */

		public function _save( $post_id, $post )
		{
        	return false;
		}


		/**
		 * 
		 */

		private function _content_id( $value = false )
		{ 
			return "project_tab_{$this->type}_{$value}";
		}


		/**
		 * 
		 */

		public function _build_tab( $args = array() )
		{ 
			$defaults = array( 
				'id'    => null, 
				'key'   => false, 
				'name'  => null, 
				'label' => null, 
				'class' => false, 
				'is_checked' => false
			);

			$args = wp_parse_args( $args, $defaults );

			if( is_null( $args['name'] ) || is_null( $args['key'] ) || is_null( $args['label'] ) )
			{
				return false;

			} // endif

			if( is_null( $args['id'] ) )
			{
				$args['id'] = $this->_content_id( $args['key'] );

			} // endif

			$field = sprintf( '<input type="radio" name="%1$s" value="%2$s"%3$s>', $args['name'], $args['key'], $args['is_checked'] );

			$output = sprintf( '<li class="%1$s" id="%2$s">', $args['class'], esc_attr( $args['id'] ) );
			$output .= sprintf( '<label>%1$s %2$s</label>', $field, $args['label'] );
			$output .= '</li>';

			return $output;
		}


		/**
		 * 
		 */

		public function _build_content( $args = array() )
		{ 
			$defaults = array( 
				'id'    => null, 
				'key'   => null, 
				'class' => false, 
				'content' => null
			);

			$args = wp_parse_args( $args, $defaults );

			if( is_null( $args['content'] ) || is_null( $args['key'] ) )
			{
				return false;

			} // endif

			if( is_null( $args['id'] ) )
			{
				$args['id'] = $this->_content_id( $args['key'] );

			} // endif

			return sprintf( '<div class="%1$s" id="%2$s_content">%3$s</div>', $args['class'], $args['id'], $args['content'] );
		}


		/**
		 * 
		 */

		public function media( $post_id = null, $size = 'project_large', $attr = array() )
		{ 
			return null;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Meta_Box_Project_Media();

?>