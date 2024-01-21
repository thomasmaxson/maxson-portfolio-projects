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

			// Load correct list table classes for current screen.
			add_action( 'current_screen', array( $this, 'setup_screen' ) );
			add_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );

			add_filter( 'display_post_states', array( &$this, 'display_post_states' ), 10, 2 );

			add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );
			add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_updated_messages' ), 10, 2 );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 10, 2 );
			add_filter( 'hidden_meta_boxes', array( &$this, 'hidden_meta_boxes' ), 10, 2 );

			// add_filter( 'media_view_strings', array( &$this, 'modify_media_strings' ) );
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
			global $portfolio_projects_list_table;

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

					$portfolio_projects_list_table = new Maxson_Portfolio_Projects_Admin_List_Table_Portfolio_Projects();
					break;
			}

			// Ensure the table handler is only loaded once. Prevents multiple loads if a plugin calls check_ajax_referer many times
			remove_action( 'current_screen', array( $this, 'setup_screen' ) );
			remove_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );
		}


		/**
		 * Render row actions for older version of WordPress - since WordPress 4.3 we don't have to build the row actions
		 * 
		 * @param       WP_Post $post
		 * @return      void
		 */

		private function render_row_actions( $post, $title )
		{ 
			global $wp_version;

			if( version_compare( $wp_version, '4.3-beta', '>=' ) )
			{ 
				return;

			} // endif

			$actions = array();

			$post_type_object = get_post_type_object( $post->post_type );

			$edit_link    = get_edit_post_link( $post->ID );
			$trash_link   = get_delete_post_link( $post->ID );
			$delete_link  = get_delete_post_link( $post->ID, '', true );

			$edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );
			$delete_post  = current_user_can( $post_type_object->cap->delete_post, $post->ID );

			if( $edit_post && 'trash' != $post->post_status )
			{ 
				$actions['edit'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', get_edit_post_link( $post->ID, true ), esc_attr( __( 'Edit this item', 'maxson' ) ), __( 'Edit', 'maxson' ) );

				$actions['inline hide-if-no-js'] = sprintf( '<a href="#" class="editinline" title="%1$s">%2$s</a>', esc_attr( __( 'Edit this item inline', 'maxson' ) ), __( 'Quick&nbsp;Edit', 'maxson' ) );

			} // endif


			if( $delete_post )
			{ 
				if( 'trash' == $post->post_status )
				{  
					$untrash_href = wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID );

					$actions['untrash'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $untrash_href, esc_attr( __( 'Restore this item from the Trash', 'maxson' ) ), __( 'Restore', 'maxson' ) );

				} elseif( EMPTY_TRASH_DAYS )
				{ 
					$actions['trash'] = sprintf( '<a href="%1$s" title="%2$s" class="submitdelete">%3$s</a>', get_delete_post_link( $post->ID ), esc_attr( __( 'Move this item to the Trash', 'maxson' ) ), __( 'Trash', 'maxson' ) );

				} // endif

				if( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS )
				{ 
					$actions['delete'] = sprintf( '<a href="%1$s" title="%2$s" class="submitdelete">%3$s</a>', get_delete_post_link( $post->ID, '', true ), esc_attr( __( 'Delete this item permanently', 'maxson' ) ), __( 'Delete Permanently', 'maxson' ) );

				} // endif
			} // endif

			if( $post_type_object->public )
			{ 
				if( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) )
				{ 
					if( $edit_post )
					{ 
						$preview_href = add_query_arg( 'preview', 'true', get_permalink( $post->ID ) );

						$actions['view'] = sprintf( '<a href="%1$s" title="%2$s" rel="permalink" target="_blank">%3$s</a>', esc_url( $preview_href ), esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'maxson' ), $title ) ), __( 'Preview', 'maxson' ) );

					} // endif
				} elseif( 'trash' != $post->post_status )
				{ 
					$actions['view'] = sprintf( '<a href="%1$s" title="%2$s" rel="permalink" target="_blank">%3$s</a>', get_permalink( $post->ID ), esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'maxson' ), $title ) ), __( 'View', 'maxson' ) );

				} // endif
			} // endif


			$actions = apply_filters( 'post_row_actions', $actions, $post );

			echo '<div class="row-actions">';

			$i = 0;
			$action_count = sizeof( $actions );

			foreach( $actions as $action => $link )
			{ 
				++$i;
				$sep = ( $i == $action_count ) ? '' : ' | ';

				echo '<span class="' . $action . '">' . $link . $sep . '</span>';

			} // endforeach

			echo '</div>';
		}


		/**
		 * Display states used in the posts list table
		 * 
		 * @param       array   $post_states An array of post display states
	 	 * @param       WP_Post $post        The current post object
	 	 * @return      string
		 */

		public function display_post_states( $post_states, $post )
		{ 
			if( maxson_portfolio_is_archive_page( $post->ID ) )
			{ 
				$post_states['archive_for_portfolio_projects'] = _x( 'Portfolio Projects', 'Label for Portfolio Archive page', 'maxson' );

			} // endif

			return $post_states;
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

		public function bulk_updated_messages( $bulk_messages, $bulk_counts )
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
<<<<<<< Updated upstream
				$hidden = array_merge( $hidden, array( 'slugdiv', 'authordiv', 'postcustom', 'revisionsdiv', 'trackbacksdiv', ) );
=======
				$hidden = array_merge( $hidden, array( 'authordiv', 'postcustom', 'revisionsdiv', 'trackbacksdiv', ) );
>>>>>>> Stashed changes

			} // endif

			return $hidden;
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
				$strings['setFeaturedImage']      = sprintf( __( 'Set Featured %1$s image', 'maxson' ), strtolower( $name ) );

			} // endif

			return $strings;
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Post_Types();

?>