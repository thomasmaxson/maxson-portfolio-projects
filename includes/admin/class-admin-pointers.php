<?php
/**
 * Plugin-specific Admin Pointers
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// https://github.com/wp-premium/wordpress-seo-premium/blob/master/admin/class-pointers.php
// http://wordpress.stackexchange.com/questions/162745/create-wp-tutorial-for-users-with-admin-pointer-using-next-button-for-navigation

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Pointers' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Pointers { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			global $wp_version;

			// Pre 3.3 has no wp_pointer functionality
			if( version_compare( $wp_version, '3.4', '<' ) )
			{
				return false;

			} // endif

			add_action( 'admin_enqueue_scripts', array( &$this, 'setup_pointers' ) );
		}


		/**
		 * Setup screen specific pointers
		 * 
		 * @return      void
		 */

		public function setup_pointers()
		{ 
			if( ! $screen = get_current_screen() )
			{ 
				return;

			} // endif

			switch( $screen->id )
			{ 
				case 'portfolio_project': 
					$pointers = $this->get_project_pointers();

					$this->init_pointers( $pointers );

					break;

			} // endswitch
		}


		/**
		 * Get project pointers
		 * 
		 * @return      void
		 */

		public static function get_project_pointers()
		{ 
			if( ! isset( $_GET['tutorial'] ) || ! current_user_can( 'manage_options' ) )
			{ 
				return array();

			} // endif

			$pointers = array( 
				'portfolio_projects_project_title' => array( 
					'target'  => '#title', 
					'trigger' => array( 
						'target' => '#title', 
						'event'  => 'focus blur'
					), 
					'options'      => array( 
						'content' => '<h3>' . esc_html__( 'Project Title', 'maxson' ) . '</h3><p>' . esc_html__( 'Give your project a name. This is a required field and will be what your viewers will see in your portfolio.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'top', 
							'align' => 'left'
						)
					)
				), 
				'portfolio_projects_project_content' => array( 
					'target'  => '#wp-content-editor-container', 
					'trigger' => array( 
						'target' => '.wp-editor-area', 
						'event'  => 'focus blur'
					), 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Description', 'maxson' ) . '</h3><p>' . esc_html__( 'This is the main body of your project description. Describe your project in detail here.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'bottom', 
							'align' => 'middle'
						)
					)
				), 
				'portfolio_projects_project_details' => array( 
					'target'  => '#meta_box_project_details', 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Details', 'maxson' ) . '</h3><p>' . esc_html__( 'Enter specific details about your project.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'bottom', 
							'align' => 'left'
						)
					)
				)
			);


			if( taxonomy_exists( 'portfolio_type' ) )
			{ 
				$pointers['portfolio_projects_project_media_type'] = array( 
					'target'  => '#project-media-type', 
					'trigger' => array( 
						'target' => '.project-radio-button-group input[type="radio"]', 
						'event'  => 'change blur click'
					), 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Choose Project Media Type', 'maxson' ) . '</h3><p>' . esc_html__( 'Choose a type of media for your project:', 'maxson' ) . '</p><p>' . esc_html__( 'None for a single image.', 'maxson' ) . '<br>' . esc_html__( 'Gallery is for multiple images.', 'maxson' ) . '<br>' . esc_html__( 'Video is for any type of video file.', 'maxson' ) . '<br>' . esc_html__( 'Audio is for any type of audio file.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'bottom', 
							'align' => 'left'
						)
					)
				);

			} // endif


			if( post_type_supports( self::POST_TYPE, 'excerpt' ) )
			{ 
				$pointers['portfolio_projects_project_excerpt'] = array( 
					'target'  => '#postexcerpt', 
					'trigger' => array( 
						'target' => '#postexcerpt', 
						'event'  => 'focus blur'
					), 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Short Description', 'maxson' ) . '</h3><p>' . esc_html__( 'Add a quick summary for your project here.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'bottom', 
							'align' => 'left'
						)
					)
				);

			} // endif


			if( post_type_supports( self::POST_TYPE, 'thumbnail' ) )
			{ 
				$pointers['portfolio_projects_project_featured_image'] = array( 
					'target'  => '#postimagediv', 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Featured Image', 'maxson' ) . '</h3><p>' . esc_html__( 'Upload or assign an image to your project here. This image will be shown in your portfolio archive.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'right', 
							'align' => 'top'
						)
					)
				);

			} // endif


			if( maxson_portfolio_get_option( 'setup_portfolio_role' ) )
			{ 
				$pointers['portfolio_projects_project_role'] = array( 
					'target'  => '#tagsdiv-portfolio_role', 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Roles', 'maxson' ) . '</h3><p>' . esc_html__( 'Optionally assign the role you had when you were apart of this project.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'right', 
							'align' => 'top'
						)
					)
				);

			} // endif


			if( maxson_portfolio_get_option( 'setup_portfolio_tag' ) )
			{ 
				$pointers['portfolio_projects_project_tag'] = array( 
					'target'  => '#tagsdiv-portfolio_tag', 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Tags', 'maxson' ) . '</h3><p>' . esc_html__( 'You can "tag" your projects here. Tags are a method of labeling your projects to make them easier to find.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'right', 
							'align' => 'top'
						)
					)
				);

			} // endif


			if( maxson_portfolio_get_option( 'setup_portfolio_category' ) )
			{ 
				$pointers['portfolio_projects_project_category'] = array( 
					'target'  => '#portfolio_categorydiv', 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Categories', 'maxson' ) . '</h3><p>' . esc_html__( 'Optionally assign categories to your projects to make them easier to browse through and file in your portfolio.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'right', 
							'align' => 'top'
						)
					)
				);

			} // endif


			if( maxson_portfolio_get_option( 'setup_promoted' ) )
			{ 
				$pointers['portfolio_projects_project_promoted'] = array( 
					'target'  => '#portfolio-projects-promoted-pointer', 
					'trigger' => array( 
						'target' => '#portfolio-projects-promoted-pointer input[type="checkbox"]', 
						'event'  => 'change blur click'
					), 
					'options' => array( 
						'content' => '<h3>' . esc_html__( 'Project Promoted', 'maxson' ) . '</h3><p>' . esc_html__( 'Add promoted flags to your projects.', 'maxson' ) . '</p>', 
						'position'  => array( 
							'edge'  => 'right', 
							'align' => 'middle'
						)
					)
				);

			} // endif


			$pointers['portfolio_projects_project_submit'] = array( 
				'target'  => '#major-publishing-actions', 
				'options' => array( 
					'content' => '<h3>' . esc_html__( 'Publish your Project', 'maxson' ) . '</h3><p>' . esc_html__( 'When you are finished editing your project, hit the "Publish" button to publish your project and add it to your portfolio.', 'maxson' ) . '</p>', 
					'position'  => array( 
						'edge'  => 'right', 
						'align' => 'middle'
					)
				)
			);

			return $pointers;
		}


		/**
		 * Enqueue pointers and scripts
		 * 
		 * @param       $pointers (required)
		 * @return      void
		 */

		public function init_pointers( $pointers )
		{ 
			$pointers = wp_json_encode( array( 'pointers' => $pointers ) );

			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );

			maxson_portfolio_enqueue_js( "
jQuery( function( $ ){ 
	var maxson_portfolio_pointers = {$pointers}, 
		pointers     = maxson_portfolio_pointers.pointers,
		pointer_keys = Object.keys( pointers );

	setTimeout( init_maxson_portfolio_pointers, 800 );

	function init_maxson_portfolio_pointers()
	{ 
		$.each( pointers, function( i ){ 
			show_maxson_portfolio_pointer( i );
			return false;
		});
	}

	function show_maxson_portfolio_pointer( id )
	{ 
		var pointer = pointers[ id ];

		var options = $.extend( pointer.options, { 
			close: function(){ 
				var current_index = pointer_keys.indexOf( id ),
					next_index    = current_index + 1,
					next_key      = false;

				if( next_index <= pointer_keys.length )
				{ 
					next_key = pointer_keys[ next_index ];

				} // endif

				if( next_key )
				{ 
					show_maxson_portfolio_pointer( next_key );

				} // endif
			}
		} );

		var this_pointer = $( pointer.target ).pointer( options );
		this_pointer.pointer( 'open' );

		if( pointer.trigger )
		{ 
			$( pointer.trigger.target ).on( pointer.trigger.event, function(){ 
				setTimeout( function(){ 
					this_pointer.pointer( 'close' );
				}, 400 );
			});

		} // endif
	}
});
				" );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Pointers();

?>