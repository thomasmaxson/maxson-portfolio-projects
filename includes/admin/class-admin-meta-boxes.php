<?php
/**
 * Admin Meta Boxes
 *
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio_Projects/includes/Admin
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Meta_Boxes' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Meta_Boxes { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		private static $saved_meta_boxes = false;

		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'add_meta_boxes', array( &$this, 'remove' ) );
			add_action( 'add_meta_boxes', array( &$this, 'replace' ) );
			add_action( 'add_meta_boxes', array( &$this, 'add' ) );

			add_action( 'save_post', array( &$this, 'save_quick_edit_meta' ), 10, 2 );
			add_action( 'save_post', array( &$this, 'save_post_meta' ), 1, 2 );

			// Save meta: details
			add_action( 'maxson_portfolio_save_meta_box', 
				array( 'Maxson_Portfolio_Projects_Meta_Box_Project_Details', 'save' ), 10, 2 );

			add_action( 'maxson_portfolio_save_meta_box', array( &$this, 'delete_transients' ), 10, 2 );
		}


		/**
		 * Remove meta boxes
		 * 
		 * @return      void
		 */

		public function remove( $post )
		{ 
			remove_meta_box( 'tagsdiv-portfolio_type', self::POST_TYPE, 'side' );

			remove_meta_box( 'pageparentdiv', self::POST_TYPE, 'side' );
		}


		/**
		 * Rename meta boxes
		 * 
		 * @return      void
		 */

		public function replace( $post )
		{ 
			if( post_type_supports( self::POST_TYPE, 'excerpt' ) &&
				! get_current_screen()->is_block_editor() )
			{ 
				remove_meta_box( 'postexcerpt', self::POST_TYPE, 'normal' );

				add_meta_box( 'postexcerpt', esc_html__( 'Excerpt', 'maxson' ), 
					array( 'Maxson_Portfolio_Projects_Meta_Box_Excerpt', 'output' ), self::POST_TYPE, 'normal' );

			} // endif


			if( post_type_supports( self::POST_TYPE, 'comment' ) )
			{ 
				remove_meta_box( 'commentsdiv', self::POST_TYPE, 'normal' );

				add_meta_box( 'commentsdiv', esc_html__( 'Reviews', 'maxson' ), 'post_comment_meta_box', self::POST_TYPE, 'normal' );

			} // endif
		}


		/**
		 * Add meta boxes
		 * 
		 * @return      void
		 */

		public function add( $post )
		{ 
			add_meta_box( 'meta_box_project_details', esc_html__( 'Project Details', 'maxson' ), 
				array( 'Maxson_Portfolio_Projects_Meta_Box_Project_Details', 'output' ), self::POST_TYPE, 'normal', 'high' );
		}


		/**
		 * On save, delete all transients
		 * 
		 * @param       int    $post_id
		 * @param       object $post
		 * 
		 * @return      void
		 */
 
		public function delete_transients( $post_id, $post )
		{ 
			$transients = maxson_portfolio_get_transients( 'all' );

			maxson_portfolio_delete_transients( $transients );
		}


		/**
		 * Quick edit saving
		 * 
		 * @param       int    $post_id
		 * @param       object $post
		 * 
		 * @return      void
		 */

		public function save_quick_edit_meta( $post_id, $post )
		{ 
			if( empty( $post_id ) || empty( $post ) )
			{
				return;

			} // endif

			if( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) )
			{
				return;

			} // endif

			if( self::POST_TYPE != $post->post_type )
			{
				return;

			} // endif

			if( ! current_user_can( 'edit_posts', $post_id ) )
			{
				return;

			} // endif

			// Check nonce
			if( isset( $_REQUEST['maxson_portfolio_quick_edit_nonce'] ) )
			{
				if( ! wp_verify_nonce( $_REQUEST['maxson_portfolio_quick_edit_nonce'], 'maxson_portfolio_quick_edit_nonce' ) )
					return $post_id;

				if( isset( $_REQUEST['project_promoted_label'] ) )
				{ 
					if( ! empty( $_REQUEST['project_promoted_label'] ) )
					{ 
						update_post_meta( $post_id, '_promoted', true );
						update_post_meta( $post_id, '_promoted_label', trim( $_REQUEST['project_promoted_label'] ) );

					} else
					{ 
						delete_post_meta( $post_id, '_promoted' );
						delete_post_meta( $post_id, '_promoted_label' );

					} // endif
				} // endif
			} // endif
		}


		/**
		 * Check if we're saving, the trigger an action based on the post type
		 * 
		 * @param       int    $post_id
		 * @param       object $post
		 * 
		 * @return      void
		 */
 
		public function save_post_meta( $post_id, $post )
		{ 
			// $post_id and $post are required
			if( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes )
			{
				return;

			} // endif

			// Dont' save meta boxes for revisions or autosaves
			if( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) )
			{
				return;

			} // endif

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
			if( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id )
			{
				return;

			} // endif

			// Check user has permission to edit
			if( ! current_user_can( 'edit_posts', $post_id ) )
			{
				return;

			} // endif

			// We need this to run once to avoid endless loops while saving
			self::$saved_meta_boxes = true;

			if( $post->post_type == self::POST_TYPE )
			{ 
				do_action( 'maxson_portfolio_save_meta_box', $post_id, $post );

			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Meta_Boxes();

?>