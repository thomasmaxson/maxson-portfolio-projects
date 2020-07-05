<?php
/**
 * Plugin-specific Admin Customizer
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Customize' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Customize { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'customize_register', array( &$this, 'register' ) );
		}


		/**
		 * Plugin-specific admin options
		 * 
		 * @return      void
		 */

		public function register( $wp_customize )
		{ 
			$panel_priority   = 0;
			$section_priority = 0;

			$wp_customize->add_panel( 'maxson_portfolio', array( 
				'capability'  => 'edit_theme_options', 
				'title'       => __( 'Portfolio Projects', 'maxson' ), 
				'description' => __( 'This panel allows you to customize the global settings that control the appearance and layout of your Portfolio Projects.', 'maxson' ), 
				'active_callback' => ( ( function_exists( 'is_portfolio' ) ) ? is_portfolio() : false )
			) );


			$section = 'archive';

			$wp_customize->add_section( "maxson_portfolio_{$section}", array( 
				'title'    => __( 'Archive', 'maxson' ), 
				'priority' => ( $panel_priority + 10 ), 
				'panel'    => 'maxson_portfolio'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_archive_page_id', array( 
				'type'       => 'option', 
				'default'    => '', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'absint'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_archive_page_id', array( 
				'section'        => "maxson_portfolio_{$section}", 
				'priority'       => ( $section_priority + 10 ), 
				'type'           => 'dropdown-pages', 
				'label'          => __( 'Portfolio Archive Page', 'maxson' ), 
				'description'    => __( 'The base page can also be used in your portfolio projects permalinks.', 'maxson' ), 
				'allow_addition' => true,
				'settings'       => 'maxson_portfolio_archive_page_id'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_archive_limit', array( 
				'type'       => 'option', 
				'default'    => '', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_archive_limit', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Limit', 'maxson' ), 
				'settings' => 'maxson_portfolio_archive_limit', 
				'active_callback' => 'maxson_portfolio_has_archive_page_id'
			) );


			if( function_exists( 'maxson_portfolio_get_archive_setting_order_options' ) )
			{ 
				$choices_order = maxson_portfolio_get_archive_setting_order_options();

				$section_priority = ( $section_priority + 10 );

				$wp_customize->add_setting( 'maxson_portfolio_archive_order', array( 
						'type'       => 'option', 
					'default'    => key( $choices_order ), 
					'capability' => 'edit_theme_options', 
					'sanitize_callback' => 'sanitize_text_field'
				) );

				$wp_customize->add_control( 'maxson_portfolio_customizer_archive_order', array( 
					'section'  => "maxson_portfolio_{$section}", 
					'priority' => $section_priority, 
					'type'     => 'radio', 
					'label'    => __( 'Order', 'maxson' ), 
					'settings' => 'maxson_portfolio_archive_order', 
					'choices'  => $choices_order, 
					'active_callback' => 'maxson_portfolio_has_archive_page_id'
				) );

			} // endif


			if( function_exists( 'maxson_portfolio_get_archive_setting_orderby_options' ) )
			{ 
				$choices_orderby = maxson_portfolio_get_archive_setting_orderby_options();

				$section_priority = ( $section_priority + 10 );

				$wp_customize->add_setting( 'maxson_portfolio_archive_orderby', array( 
					'type'       => 'option', 
					'default'    => key( $choices_orderby ), 
					'capability' => 'edit_theme_options', 
					'sanitize_callback' => 'sanitize_text_field'
				) );

				$wp_customize->add_control( 'maxson_portfolio_customizer_archive_orderby', array( 
					'section'  => "maxson_portfolio_{$section}", 
					'priority' => $section_priority, 
					'type'     => 'radio', 
					'label'    => __( 'Orderby', 'maxson' ), 
					'settings' => 'maxson_portfolio_archive_orderby', 
					'choices'  =>  $choices_orderby, 
					'active_callback' => 'maxson_portfolio_has_archive_page_id'
				) );

			} // endif


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_archive_thumbnail', array( 
				'type'       => 'option', 
				'default'    => true, 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_archive_thumbnail', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'checkbox', 
				'label'    => __( 'Thumbnail is required to display projects', 'maxson' ), 
				'settings' => 'maxson_portfolio_archive_thumbnail', 
				'active_callback' => 'maxson_portfolio_has_archive_page_id'
			) );




			$section_priority = 0;
			$section = 'setup';

			$wp_customize->add_section( "maxson_portfolio_{$section}", array( 
				'title'    => __( 'Setup', 'maxson' ), 
				'priority' => ( $panel_priority + 10 ), 
				'panel'    => 'maxson_portfolio'
			) );


		//	$section_priority = ( $section_priority + 10 );

		//	$wp_customize->add_setting( 'maxson_portfolio_setup_promoted', array( 
			//	'transport'  => 'refresh', 
		//		'type'       => 'option', 
		//		'default'    => true, 
		//		'capability' => 'edit_theme_options', 
		//		'sanitize_callback' => 'sanitize_text_field'
		//	) );

		//	$wp_customize->add_control( 'maxson_portfolio_customizer_activate_promoted', array( 
		//		'section'  => "maxson_portfolio_{$section}", 
		//		'priority' => $section_priority, 
		//		'type'     => 'checkbox', 
		//		'label'    => __( 'Activate promoted projects', 'maxson' ), 
		//		'settings' => 'maxson_portfolio_setup_promoted'
		//	) );


			$section_priority = ( $section_priority + 10 );

			$taxonomies = maxson_portfolio_get_taxonomy_types();

			if( ! empty( $taxonomies ) )
			{ 
				$private_taxonomies = apply_filters( 'maxson_portfolio_private_taxonomies', array( 'type' ) );

				foreach( $taxonomies as $key => $label )
				{ 
					if( in_array( $key, $private_taxonomies ) )
					{
						continue;

					} // endif

					$section_priority = ( $section_priority + 10 );

					$wp_customize->add_setting( "maxson_portfolio_setup_portfolio_{$key}", array( 
						'type'       => 'option', 
						'default'    => true, 
						'capability' => 'edit_theme_options', 
						'sanitize_callback' => 'sanitize_text_field'
					) );

					$wp_customize->add_control( "maxson_portfolio_customizer_activate_taxonomy_{$key}", array( 
						'section'  => "maxson_portfolio_{$section}", 
						'priority' => $section_priority, 
						'type'     => 'checkbox', 
						'label'    => sprintf( __( 'Activate portfolio %1$s taxonomy', 'maxson' ), strtolower( $label ) ), 
						'settings' =>  "maxson_portfolio_setup_portfolio_{$key}"
					) );

				} // endforeach
			} // endif


			$section_priority = 0;
			$section = 'media';

			$wp_customize->add_section( "maxson_portfolio_{$section}", array( 
				'title'    => __( 'Media', 'maxson' ), 
				'priority' => ( $panel_priority + 10 ), 
				'panel'    => 'maxson_portfolio'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_thumbnail_width', array( 
				'type'       => 'option', 
				'default'    => '300', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_thumbnail_width', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Thumbnail Width', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_thumbnail_width'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_thumbnail_height', array( 
				'type'       => 'option', 
				'default'    => '300', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_thumbnail_height', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Thumbnail Height', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_thumbnail_height'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_thumbnail_crop', array( 
				'type'       => 'option', 
				'default'    => false, 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_thumbnail_crop', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'checkbox', 
				'label'    => __( 'Crop portfolio project thumbnail to exact dimensions (normally thumbnail images are proportional)', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_thumbnail_crop'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_medium_width', array( 
				'type'       => 'option', 
				'default'    => '300', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_medium_width', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Medium Max Width', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_medium_width'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_medium_height', array( 
				'type'       => 'option', 
				'default'    => '300', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_medium_height', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Medium Max Height', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_medium_height'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_large_width', array( 
				'type'       => 'option', 
				'default'    => '300', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_large_width', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Large Max Width', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_large_width'
			) );


			$section_priority = ( $section_priority + 10 );

			$wp_customize->add_setting( 'maxson_portfolio_media_large_height', array( 
				'type'       => 'option', 
				'default'    => '300', 
				'capability' => 'edit_theme_options', 
				'sanitize_callback' => 'sanitize_text_field'
			) );

			$wp_customize->add_control( 'maxson_portfolio_customizer_media_large_height', array( 
				'section'  => "maxson_portfolio_{$section}", 
				'priority' => $section_priority, 
				'type'     => 'number', 
				'label'    => __( 'Large Max Height', 'maxson' ), 
				'settings' => 'maxson_portfolio_media_large_height'
			) );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Customize();

?>