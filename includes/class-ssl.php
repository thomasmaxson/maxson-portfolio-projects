<?php
/**
 * Plugin-specific project security
 * 
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio/includes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_SSL' ) )
{ 
	class Maxson_Portfolio_Projects_SSL { 

		/**
		 * Construct
		 * 
		 * @return      void
		 */

		public function __construct()
		{ 
			$filters = array( 
				'post_thumbnail_html', 
				'wp_get_attachment_image_attributes', 
				'wp_get_attachment_url', 
				'option_stylesheet_url', 
				'option_template_url', 
				'script_loader_src', 
				'style_loader_src', 
				'template_directory_uri', 
				'stylesheet_directory_uri', 
				'site_url'
			);

			if( ! is_admin() )
			{ 
				foreach( $filters as $filter )
				{ 
					add_filter( $filter, array( &$this, 'force_https_url' ), 999 );

				} // endforeach
			} // endif

			add_filter( 'page_link', array( &$this, 'force_https_page_link' ), 10, 2 );
			add_action( 'template_redirect', array( &$this, 'force_https_template_redirect' ) );
		}


		/**
		 * force_https_url function.
		 *
		 * @param       mixed $content
		 * @return      string
		 */

		public static function force_https_url( $content )
		{ 
			if( ! is_ssl() && maxson_portfolio_site_is_https() )
			{ 
				if( is_array( $content ) )
				{ 
					$content = array_map( 'Maxson_Portfolio_Projects_SSL::force_https_url', $content );

				} else
				{
					$content = str_replace( 'http:', 'https:', $content );

				} // endif
			} // endif

			return $content;
		}


		/**
		 * Force a post link to be SSL if needed.
		 *
		 * @return string
		 */

		public function force_https_page_link( $link, $page_id )
		{ 
			if( is_ssl() && ! maxson_portfolio_site_is_https() )
			{  
				$link = str_replace( 'http:', 'https:', $link );

			} // endif

			return $link;
		}


		/**
		 * Template redirect - if we end up on a page ensure it has the correct http/https url.
		 */

		public function force_https_template_redirect()
		{ 
			if( ! is_ssl() && maxson_portfolio_site_is_https() )
			{
				if( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) )
				{ 
					wp_safe_redirect( preg_replace( '|^http://|', 'https://', $_SERVER['REQUEST_URI'] ) );
					exit;

				} else
				{ 
					wp_safe_redirect( 'https://' . ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI'] );
					exit;

				} // endif
			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_SSL();

?>