<?php
/**
 * Plugin-specific Admin Settings
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Settings' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Settings { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			$active_tab    = maxson_portfolio_get_settings_active_tab();
			$required_tabs = maxson_portfolio_get_settings_required_tabs();

			if( array_key_exists( $active_tab, $required_tabs ) )
			{ 
				add_action( 'admin_menu', array( &$this, "{$active_tab}_register_settings" ) );
				add_action( "maxson_portfolio_settings_{$active_tab}", array( &$this, 'settings_form' ), 10, 1 );

			} // endif
		}


		/**
		 * Plugin-specific admin settings page
		 * 
		 * @return      void
		 */

		public function settings_form( $active_tab = null )
		{ 
			maxson_portfolio_the_settings_form( $active_tab );
		}


		/**
		 * Plugin-specific admin register settings
		 * 
		 * @return      void
		 */

		public function archive_register_settings()
		{ 
			global $wp_version;

			$tab     = 'archive';
			$section = 'general';

			add_settings_section( "maxson_portfolio_{$tab}_{$section}", 
				__( 'General', 'maxson' ), array( &$this, 'setting_general_description' ), "maxson_portfolio_{$tab}" );

		//	if( version_compare( $wp_version, '4.7', '>=' ) )
		//	{ 
		//		register_setting( "maxson_portfolio_{$tab}", 'maxson_portfolio_archive_page_id', array( 
		//			'type'              => 'int', 
			//		'group'             => $option_group, 
		//			'description'       => __( 'Portfolio archive settings', 'maxson' ), 
		//			'sanitize_callback' => null, 
		//			'show_in_rest'      => true
		//		) );

		//	} else
		//	{ 
				register_setting( "maxson_portfolio_{$tab}", 'maxson_portfolio_archive_page_id', 'absint' );

		//	} // endif

			add_settings_field( "maxson_portfolio_setting_{$tab}_page_id", __( 'Portfolio archive page', 'maxson' ), 
				array( &$this, 'setting_archive_page_id' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );


			$section = 'query';

			add_settings_section( "maxson_portfolio_{$tab}_{$section}", 
				__( 'Query', 'maxson' ), '', "maxson_portfolio_{$tab}" );

			register_setting( "maxson_portfolio_{$tab}", 'maxson_portfolio_archive_limit' );

			add_settings_field( "maxson_portfolio_setting_{$tab}_limit", __( 'Archive shows at most', 'maxson' ), 
				array( &$this, 'setting_archive_limit' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );


			register_setting( "maxson_portfolio_{$tab}", 'maxson_portfolio_archive_order' );

			add_settings_field( "maxson_portfolio_setting_{$tab}_order", __( 'Archive order', 'maxson' ), 
				array( &$this, 'setting_archive_order' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );


			register_setting( "maxson_portfolio_{$tab}", 'maxson_portfolio_archive_orderby' );

			add_settings_field( "maxson_portfolio_setting_{$tab}_orderby", __( 'Archive orderby', 'maxson' ), 
				array( &$this, 'setting_archive_orderby' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );


			register_setting( "maxson_portfolio_{$tab}", 'maxson_portfolio_archive_thumbnail' );

			add_settings_field( "maxson_portfolio_setting_{$section}_thumbnail", __( 'Thumbnail required', 'maxson' ), 
				array( &$this, 'setting_archive_require_thumbnail' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}" );
		}


		/**
		 * Plugin-specific admin register settings
		 * 
		 * @return      void
		 */

		public function setup_register_settings()
		{ 
			global $wp_version;

			$tab = 'setup';

			$taxonomies = maxson_portfolio_get_taxonomy_types();

			if( ! empty( $taxonomies ) )
			{ 
				$section = 'taxonomy';

				$private_taxonomies = apply_filters( 'maxson_portfolio_private_taxonomies', array( 'type' ) );

				add_settings_section( "maxson_portfolio_{$tab}_{$section}", 
					__( 'Portfolio Taxonomies', 'maxson' ), '__return_FALSE', "maxson_portfolio_{$tab}" );

				foreach( $taxonomies as $key => $label )
				{ 
					if( in_array( $key, $private_taxonomies ) )
					{
						continue;

					} // endif

					register_setting( "maxson_portfolio_{$tab}", "maxson_portfolio_setup_portfolio_{$key}" );

					add_settings_field( "maxson_portfolio_setting_{$tab}_activate_{$key}_taxonomy", 
						sprintf( __( 'Portfolio %1$s', 'maxson' ), $label ), 
						array( &$this, 'setting_activate_taxonomy' ), "maxson_portfolio_{$tab}", "maxson_portfolio_{$tab}_{$section}", array( 
							'taxonomy' => $key, 
							'desc'     => sprintf( __( 'Activate portfolio %1$s taxonomy', 'maxson' ), strtolower( $label ) )
						) );

				} // endforeach
			} // endif
		}


		public static function setting_general_description()
		{ 
			echo wpautop( __( 'Use the page selected below to handle the display your portfolio archive. If this page is deleted or removed in some way, it can be manually recreated from the Pages menu.', 'maxson' ) );
		}


		/**
		 * Add/Remove option flag to install archive page
		 * 
		 * @return      void
		 */

		public static function update_maxson_portfolio_archive_page_id( $new_value, $old_value )
		{ 
			if( $old_value != $new_value )
			{ 
				if( '' !== $new_value )
				{ 
					$post_parent_ID = $new_value;

					update_option( 'maxson_portfolio_install_pages_notice', true );

				} else
				{ 
					$post_parent_ID = 0;

					delete_option( 'maxson_portfolio_install_pages_notice' );

				} // endif

				if( apply_filters( 'maxson_portfolio_save_archive_as_project_parent', true ) )
				{ 
					$args = array( 
						'post_status'    => 'any', 
						'post_type'      => self::POST_TYPE, 
						'posts_per_page' => -1, 
						'fields'         => 'ids'
					);

					$project_ids = get_posts( $args );

					if( $project_ids )
					{ 
						foreach( $project_ids as $project_id )
						{ 
							wp_update_post( array( 
								'ID'          => $project_id, 
								'post_parent' => $post_parent_ID
							) );

						} // endforeach
					} // endif
				} // endif
			} // endif

			return $new_value;
		}


		/** 
		 * Plugin-specific per page count option callback
		 * 
		 * @return      string
		 */

		public function setting_archive_page_id()
		{ 
			if( get_pages() )
			{ 
				$url        = admin_url( 'options-permalink.php#maxson_portfolio_permalink_settings' );
				$link_start = sprintf( '<a href="%1$s">', $url );
				$link_end   = '</a>';

				$name  = 'maxson_portfolio_archive_page_id';
				$desc  = sprintf( __( 'The base page can also be used in your %1$sportfolio permalinks.%2$s', 'maxson' ), $link_start, $link_end );
				$value = maxson_portfolio_get_archive_page_id();

				$args = array( 
					'name'              => $name, 
					'selected'          => $value, 
					'show_option_none'  => __( 'Select a page', 'maxson' ), 
					'show_option_value' => ''
				);

				echo '<div class="chosen-portfolio-select">';
					wp_dropdown_pages( $args );

					if( ! empty( $value ) && -1 != $value && get_post( $value ) )
					{ 
						printf( '<a href="%1$s">%2$s</a>', get_edit_post_link( $value ), __( 'Edit Portfolio Archive', 'maxson' ) );

					} // endif
				echo '</div><!-- .chosen-portfolio-select -->';

			} else
			{ 
				$url        = add_query_arg( array( 
					'post_type' => 'page'
				), admin_url( 'post-new.php' ) );
				$link_start = sprintf( '<a href="%1$s">', $url );
				$link_end   = '</a>';

				$desc = sprintf( __( '%1$sCreate a page%2$s and select it to customize your portfolio archive.', 'maxson' ), $link_start, $link_end );

			} // endif

			printf( '<p class="description">%1$s</p>', $desc );
		}


		/**
		 * Plugin-specific archive limit option callback
		 * 
		 * @return      string
		 */

		public function setting_archive_limit()
		{ 
			$templates = array();

			if( post_type_exists( self::POST_TYPE ) )
			{
				$templates[] = 'archive-portfolio.php';

			} // endif

			// Get all taxonomies for post type
			$taxonomies = get_object_taxonomies( array( self::POST_TYPE ) );

			foreach( $taxonomies as $taxonomy )
			{ 
				if( taxonomy_exists( $taxonomy ) )
				{ 
					$taxonomy_obj = get_taxonomy( $taxonomy );

					// Confirm taxonomy is public
					if( ! $taxonomy_obj->public )
						continue;

					$templates[] = "taxonomy-{$taxonomy}.php";

				} // endif
			} // endforeach

			if( ! empty( $templates ) )
			{ 
				$url        = admin_url( 'options-reading.php#posts_per_page' );
				$link_start = sprintf( '<a href="%1$s">', $url );
				$link_end   = '</a>';

				$templates = '<code>' . join( '</code>, <code>', $templates ) . '</code>';

				$name  = 'maxson_portfolio_archive_limit';
				$desc  = sprintf( __( 'Set the maximum number of projects to show on archive template(s) (%1$s). If empty, "%2$sblog pages show at most%3$s" setting will be used.', 'maxson' ), $templates, $link_start, $link_end );
				$value = maxson_portfolio_get_archive_limit();

				printf( '<input type="number" name="%1$s" class="small-text" value="%2$s"> %3$s', esc_attr( $name ), esc_attr( $value ), _x( 'projects', 'Portfolio archive limit input label', 'maxson' ) );
				printf( '<p class="description">%1$s</p>', $desc );

			} // endif
		}


		/** 
		 * Plugin-specific archive order option callback
		 * 
		 * @return      string
		 */

		public function setting_archive_order()
		{ 
			$name    = 'maxson_portfolio_archive_order';
			$desc    = __( 'Portfolio order', 'maxson' );
			$value   = maxson_portfolio_get_option( 'archive_order' );
			$options = maxson_portfolio_get_archive_setting_order_options();

			echo '<fieldset class="portfolio-project-radiolist">';
				printf( '<legend class="screen-reader-text">%1$s</legend>', $desc );

				foreach( $options as $key => $label )
				{ 
					$checked = checked( $value, $key, false );

					printf( '<p><label><input type="radio" name="%1$s" value="%2$s"%3$s> %4$s</label></p>', esc_attr( $name ), esc_attr( $key ), $checked, $label );

				} // endforeach
			echo '</fieldset>';
		}


		/** 
		 * Plugin-specific archive orderby option callback
		 * 
		 * @return      string
		 */

		public function setting_archive_orderby()
		{ 
			$name    = 'maxson_portfolio_archive_orderby';
			$desc    = __( 'Portfolio orderby', 'maxson' );
			$value   = maxson_portfolio_get_option( 'archive_orderby' );
			$options = maxson_portfolio_get_archive_setting_orderby_options();

			echo '<fieldset class="portfolio-project-radiolist">';
				printf( '<legend class="screen-reader-text">%1$s</legend>', $desc );

				foreach( $options as $key => $label )
				{ 
					$checked = checked( $value, $key, false );

					printf( '<p><label><input type="radio" name="%1$s" value="%2$s"%3$s> %4$s</label></p>', esc_attr( $name ), esc_attr( $key ), $checked, $label );

				} // endforeach
			echo '</fieldset>';
		}


		/** 
		 * Plugin-specific archive orderby option callback
		 * 
		 * @return      string
		 */

		public function setting_archive_require_thumbnail()
		{ 
			$name  = 'maxson_portfolio_archive_thumbnail';
			$desc  = __( 'Featured thumbnail must be set for project to show', 'maxson' );
			$value = maxson_portfolio_get_option( 'archive_thumbnail' );

			$is_checked = checked( $value, '1', false );

			printf( '<input type="checkbox" name="%1$s" id="%1$s" value="1"%2$s>', esc_attr( $name ), $is_checked );
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $name ), $desc );
		}


		/**
		 * Plugin-specific settings field callback
		 * 
		 * @return      void
		 */

		public function setting_activate_promoted_meta()
		{ 
			$id    = 'maxson-portfolio-setup-promoted';
			$name  = 'maxson_portfolio_setup_promoted';
			$desc  = __( 'Activate promoted projects', 'maxson' );
			$value = maxson_portfolio_get_option( 'setup_promoted' );

			$is_checked = checked( $value, '1', false );

			printf( '<input type="checkbox" name="%1$s" id="%2$s" value="1"%3$s> <label for="%2$s">%4$s</label>', esc_attr( $name ), esc_attr( $id ), $is_checked, esc_html( $desc ) );
		}


		/**
		 * Plugin-specific settings field callback
		 * 
		 * @return      void
		 */

		public function setting_activate_taxonomy( $args = array() )
		{ 
			$taxonomy = $args['taxonomy'];
			$desc     = $args['desc'];

			$id    = "maxson-portfolio-setup-portfolio-{$taxonomy}";
			$name  = "maxson_portfolio_setup_portfolio_{$taxonomy}";
			$value = maxson_portfolio_get_option( "setup_portfolio_{$taxonomy}", false );

			$is_checked  = checked( $value, '1', false );

			printf( '<input type="checkbox" name="%1$s" id="%2$s" value="1"%3$s> <label for="%2$s">%4$s</label>', esc_attr( $name ), esc_attr( $id ), $is_checked, esc_html( $desc ) );
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Settings();

?>