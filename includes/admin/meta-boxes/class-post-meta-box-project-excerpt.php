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


if( ! class_exists( 'Maxson_Portfolio_Projects_Meta_Box_Excerpt' ) )
{ 
	class Maxson_Portfolio_Projects_Meta_Box_Excerpt { 

		/**
		 * Prints the details meta box content
		 * 
		 * @param 		$post The object for the current post/page
		 * @return      void
		 */

		public static function output( $post )
		{ 
			$name  = 'excerpt';
			$value = $post->post_excerpt;

			$settings = apply_filters( 'maxson_portfolio_short_description_settings', array( 
				'media_buttons' => false, 
				'teeny'         => false, 
				'textarea_name' => 'excerpt', 
				'quicktags'     => array( 
					'buttons' => 'em,strong,link,block,del,ul,ol,li,code'
				), 
				'tinymce'       => array( 
					'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator', 
					'theme_advanced_buttons2' => '', 
					'theme_advanced_buttons3' => '', 
					'theme_advanced_buttons4' => ''
				), 
				'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>' 
			) );

			wp_editor( htmlspecialchars_decode( $value ), $name, $settings );
		}

	} // endclass
} // endif

?>