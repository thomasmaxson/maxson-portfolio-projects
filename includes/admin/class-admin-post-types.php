<?php
/**
 * Plugin-Specfic Admin Post Type
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Post_Types' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Post_Types {

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			$post_type = self::POST_TYPE;

			// Load correct list table classes for current screen
			add_action( 'current_screen', array( &$this, 'setup_screen' ) );
			add_action( 'check_ajax_referer', array( &$this, 'setup_screen' ) );

			// Admin notices
			add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );
			add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_post_updated_messages' ), 10, 2 );

			// Extra post data and screen elements
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 10, 2 );

			// Hide Meta Boxes
			// Permanent View Hide
		//	add_filter( 'hidden_meta_boxes', array( &$this, 'hidden_meta_boxes' ), 10, 2 );
			// Default View Hide
			add_filter( 'default_hidden_meta_boxes', array( &$this, 'hidden_meta_boxes' ), 10, 2 );

			// Show archive notice
			add_action( 'edit_form_top', array( &$this, 'show_portfolio_archive_notice' ) );

			// Add a post display state for special pages
			add_filter( 'display_post_states', array( &$this, 'add_display_post_states' ), 10, 2 );

			// Bulk / quick edit
			add_filter( 'bulk_actions-edit-portfolio_project', array( &$this, 'bulk_actions' ) );
			add_action( 'quick_edit_custom_box', array( &$this, 'quick_edit' ), 10, 2 );



			add_action( 'pre_get_posts', array( &$this, 'column_orderby' ) );
			add_filter( 'media_view_strings', array( &$this, 'modify_media_strings' ) );
		}


		/**
		 * Should we bail out of this method?
		 * 
		 * @return 		bool
		 */

		private function bail()
		{ 
			return ( ! isset( get_current_screen()->post_type ) || ( self::POST_TYPE !== get_current_screen()->post_type )  ) ? true : false;
		}


		/**
		 * Looks at the current screen and loads the correct list table handler
		 */

		public function setup_screen()
		{ 
			global $maxson_pp_list_table;

			$screen_id = false;

			if( function_exists( 'get_current_screen' ) )
			{ 
				$screen    = get_current_screen();
				$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';

			} // endif


			if( ! empty( $_REQUEST['screen'] ) )
			{ 
				$screen_id = sanitize_text_field( wp_unslash( $_REQUEST['screen'] ) );

			} // endif


			switch( $screen_id )
			{ 
				case 'edit-portfolio-project': 
				case 'edit-portfolio_project': 
					include_once( 'list-tables/class-admin-list-table-portfolio-projects.php' );

					$maxson_pp_list_table = new Maxson_Portfolio_Projects_Admin_List_Table_Portfolio_Projects();
					break;
			}

			// Ensure the table handler is only loaded once. Prevents multiple loads if a plugin calls check_ajax_referer many times
			remove_action( 'current_screen', array( $this, 'setup_screen' ) );
			remove_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );
		}


		/**
		 * Change messages when a post type is updated
		 * 
		 * @param       array $messages
		 * @return      array
		 */

		public function post_updated_messages( $messages )
		{ 
			global $post, $post_ID;

			$post_object = get_post_type_object( self::POST_TYPE );

			$scheduled_date = date_i18n( __( 'M j, Y @ G:i', 'maxson' ), strtotime( $post->post_date ) );

			$messages[self::POST_TYPE] = array( 
				0  => '', // Unused. Messages start at index 1.
				1  => sprintf( __( 'Project updated. <a href="%1$s">View project</a>', 'maxson' ), esc_url( get_permalink( $post_ID ) ) ), 
				2  => __( 'Custom field updated.', 'maxson' ), 
				3  => __( 'Custom field deleted.', 'maxson' ), 
				4  => __( 'Project updated.', 'maxson' ), 
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Project restored to revision from %1$s', 'maxson' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, 
				6  => sprintf( __( 'Project published. <a href="%1$s">View project</a>', 'maxson' ), esc_url( get_permalink( $post_ID ) ) ), 
				7  => __( 'Project saved.', 'maxson' ), 
				8  => sprintf( __( 'Project submitted. <a target="_blank" href="%1$s">Preview project</a>', 'maxson' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ), 
				9  => sprintf( __( 'Project scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview project</a>', 'maxson' ), $scheduled_date, esc_url( get_permalink( $post_ID ) ) ), 
				10 => sprintf( __( 'Project draft updated. <a target="_blank" href="%1$s">Preview project</a>', 'maxson' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) )
			);

			if( ! $post_object->public )
			{ 
				$messages[self::POST_TYPE][1] = __( 'Project updated.', 'maxson' );
				$messages[self::POST_TYPE][6] = __( 'Project published.', 'maxson' );
				$messages[self::POST_TYPE][8] = __( 'Project submitted.', 'maxson' );
				$messages[self::POST_TYPE][9] = sprintf( __( 'Project scheduled for: <strong>%1$s</strong>.', 'maxson' ), $scheduled_date );
				$messages[self::POST_TYPE][10] = __( 'Project item draft updated.', 'maxson' );

			} // endif

			return $messages;
		}


		/**
		 * Change bulk update messages when a post type is updated
		 * 
		 * @param       array $bulk_messages Array of messages, each keyed by the corresponding post type
		 * @param       array $bulk_counts   Array of item counts for each message
		 * @return      array
		 */

		public function bulk_post_updated_messages( $bulk_messages, $bulk_counts )
		{ 
			$bulk_messages[self::POST_TYPE] = array( 
				'updated'   => _n( '%s project updated.', '%s projects updated.', $bulk_counts['updated'], 'maxson' ), 
				'locked'    => _n( '%s project not updated, somebody is editing it.', '%s projects not updated, somebody is editing them.', $bulk_counts['locked'], 'maxson' ), 
				'deleted'   => _n( '%s project permanently deleted.', '%s projects permanently deleted.', $bulk_counts['deleted'], 'maxson' ), 
				'trashed'   => _n( '%s project moved to Trash.', '%s projects moved to Trash.', $bulk_counts['trashed'], 'maxson' ), 
				'untrashed' => _n( '%s project restored from the Trash.', '%s projects restored from the Trash.', $bulk_counts['untrashed'], 'maxson' ), 
			);

			return $bulk_messages;
		}


		/**
		 * Change title boxes in admin
		 * 
		 * @param       string $text
		 * @param       object $post
		 * @return      string
		 */

		public function enter_title_here( $text, $post )
		{ 
			if( $post->post_type == self::POST_TYPE )
			{ 
				$text = _x( 'Enter project title here', 'Post type post title placeholder', 'maxson' );

			} // endif

			return $text;
		}


		/**
		 * Hidden default meta boxes
		 * 
		 * @param       array  $hidden An array of meta boxes hidden by default
		 * @param       object $screen WP_Screen object of the current screen
		 * @return      array
		 */

		public function hidden_meta_boxes( $hidden, $screen )
		{ 
			if( self::POST_TYPE === $screen->post_type && 'post' === $screen->base )
			{ 
				$hidden = array_merge( $hidden, array( 'slugdiv', 'authordiv', 'postcustom', 'revisionsdiv', 'trackbacksdiv', ) );

			} // endif

			return $hidden;
		}


		/**
		 * Show a notice above the CPT archive
		 *
		 * @param WP_Post $post The current post object.
		 */

		public function show_portfolio_archive_notice( $post )
		{ 
			if( $post && maxson_portfolio_is_archive_page( $post->ID ) )
			{ 
				echo '<div class="notice notice-info">';
				echo '<p>' . wp_kses_post( __( 'This is the Portfolio Projects Archive page. This page is a special archive that lists your Portfolio Projects.', 'maxson' ) ) . '</p>';
				echo '</div>';

			} // endif
		}


		/**
		 * Add a post display state for specific pages in the page list table
		 * 
		 * @param       array   $post_states An array of post display states
		 * @param       WP_Post $post        The current post object
		 * @return 		array
		 */

		public function add_display_post_states( $post_states, $post )
		{ 
			if( maxson_portfolio_is_archive_page( $post->ID ) )
			{ 
				$post_states['maxson_portfolio_page_for_archive'] = __( 'Portfolio Projects Archive', 'maxson' );

			} // endif

			return $post_states;
		}


		/**
		 * Remove edit from the bulk actions.
		 * 
		 * @param       array $actions
		 * @return 		array
		 */

		public function bulk_actions( $actions )
		{ 
			if( isset( $actions['edit'] ) )
			{ 
				unset( $actions['edit'] );

			} // endif

			return $actions;
		}


		/**
		 * Custom quick edit
		 * 
		 * @param       mixed $column_name
		 * @param       mixed $post_type
		 * @return 		void
		 */

		public function quick_edit( $column_name, $post_type )
		{ 
			if( self::POST_TYPE == $post_type )
			{ 
				switch( $column_name )
				{ 
					case 'promoted': 
						include( MAXSON_PORTFOLIO_DIRNAME . '/includes/admin/views/html-quick-edit-promoted.php' );
						break;

					default: 
						return false;
						break;

				} // endswitch
			} // endif
		}


		/**
		 * Setup data for custom column query 
		 * 
		 * @param       string $query
		 * @return      void
		 */

		public function column_orderby( $query )
		{ 
			if( is_admin() )
			{ 
				$orderby = $query->get( 'orderby' );

				switch( $orderby )
				{ 
					case 'project_promoted': 
						// reverse display order - it's backwards from what you'd expect it to be.
						$order = ( 'DESC' == strtoupper( $query->get( 'order' ) ) ) ? 'ASC' : 'DESC';

						$query->set( 'orderby', 'meta_value' );
						$query->set( 'order', $order );
						$query->set( 'meta_query', array( 
							'relation' => 'OR', 
							'project_promoted_no_meta' => array( 
								'key'     => '_promoted', 
								'compare' => 'NOT EXISTS'
							), 
							'project_promoted_has_meta' => array( 
								'key'     => '_promoted', 
								'compare' => 'EXISTS'
							)
						) );
						break;

				} // endswitch
			} // endif
		}


		/**
		 * Change label for insert buttons
		 * 
		 * @param       array $strings
		 * @return      array
		 */

		public function modify_media_strings( $strings )
		{ 
			global $post_type;

			if( $post_type == self::POST_TYPE )
			{ 
				$obj  = get_post_type_object( self::POST_TYPE );
				$name = $obj->labels->singular_name;

				$strings['insertIntoPost']        = sprintf( __( 'Insert into %1$s', 'maxson' ), $name );
				$strings['uploadedToThisPost']    = sprintf( __( 'Uploaded to this %1$s', 'maxson' ), $name );
				$strings['setFeaturedImageTitle'] = sprintf( __( 'Set Featured %1$s Image', 'maxson' ), $name );
				$strings['setFeaturedImage']      = sprintf( __( 'Set promoted %1$s image', 'maxson' ), strtolower( $name ) );

			} // endif

			return $strings;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Post_Types();

?>