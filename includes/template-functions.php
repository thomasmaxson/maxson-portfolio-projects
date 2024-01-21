<?php
/**
 * Plugin-specific template functions
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'portfolio_projects_page_title' ) )
{ 
	/**
	 * Page title function
	 * 
	 * @param       string      $before (optional) Text to place before output
	 * @param       string      $after  (optional) Text to place after output
	 * @param       bool        $echo   (optional) Return or display content
	 * @return      string|void
	 */

	function portfolio_projects_page_title( $before = '', $after = '', $echo = true )
	{ 
		if( is_tax() )
		{ 
			$page_title = single_term_title( '', false );

		//	if( get_query_var( 'paged' ) )
		//		$page_title .= sprintf( __( '&nbsp;&ndash;&nbsp;Page %1$s', 'maxson' ), get_query_var( 'paged' ) );

			if( ! empty( $page_title ) )
			{
				$page_title = $before . $page_title . $after;

			} // endif
		} elseif( maxson_portfolio_is_archive_page() || is_post_type_archive( 'portfolio_project' ) )
		{ 
			$page_title = portfolio_projects_archive_title( $before, $after );

		} else
		{ 
			$page_title = false;

		} // endif

		$page_title = apply_filters( 'maxson_portfolio_page_title', $page_title );

		if( ! $echo )
		{
			return $page_title;

		} // endif

		echo $page_title;
	}
} // endif


if( ! function_exists( 'portfolio_projects_archive_title' ) )
{ 
	/**
	 * Archive title function
	 * 
	 * @param       string      $before (optional) Text to place before output
	 * @param       string      $after  (optional) Text to place after output
	 * @return      string|void
	 */

	function portfolio_projects_archive_title( $before = '', $after = '' )
	{ 
		$archive_id = maxson_portfolio_get_archive_page_id();
		$page_title = get_the_title( $archive_id );

		if( ! empty( $page_title ) )
		{
			$page_title = $before . $page_title . $after;

		} // endif

		return apply_filters( 'maxson_portfolio_archive_title', $page_title, $before, $after, $archive_id );
	}
} // endif


if( ! function_exists( 'portfolio_projects_taxonomy_description' ) )
{ 
	/**
	 * Portfolio taxonomy description
	 * 
	 * @param       bool        $echo (optional) Return or display content
	 * @return      string|void
	 */

	function portfolio_projects_taxonomy_description( $echo = true )
	{ 
		if( is_portfolio_taxonomy() && get_query_var( 'paged' ) == 0 )
		{ 
			$description = do_shortcode( shortcode_unautop( wpautop( term_description() ) ) );

			if( $description )
			{ 
				$output = sprintf( '<div class="page-description">%1$s</div>', $description );

				if( ! $echo )
				{ 
					return $output;

				} // endif

				echo $output;

			} // endif
		} // endif
	}
} // endif


if( ! function_exists( 'maxson_portfolio_get_archive_setting_description' ) )
{ 
	/**
	 * Portfolio archive description
	 * 
	 * @param       bool        $echo (optional) Return or display content
	 * @return      string|void
	 */

	function maxson_portfolio_get_archive_setting_description( $echo = true )
	{ 
		if( is_post_type_archive( 'portfolio_project' ) && get_query_var( 'paged' ) == 0 )
		{ 
			$archive_id = maxson_portfolio_get_option( 'archive_page_id' );

			if( $archive_page = get_post( $archive_id ) )
			{ 
				$description = do_shortcode( shortcode_unautop( wpautop( $archive_page->post_content ) ) );

				if( $description )
				{ 
					$output = sprintf( '<div class="page-description">%1$s</div>', $description );

					if( ! $echo )
					{
						return $output;

					} // endif

					echo $output;

				} // endif
			} // endif
		} // endif
	}
} // endif







if( ! function_exists( 'maxson_portfolio_get_project_teaser_thumbnail' ) )
{ 
	/**
	 * Get the project teaser thumbnail
	 */

	 function maxson_portfolio_get_project_teaser_thumbnail()
	 { 
		echo maxson_portfolio_template( 'project-teaser/thumbnail.php' );
	}
}


if( ! function_exists( 'maxson_portfolio_get_project_teaser_callout' ) )
{ 
	/**
	 * Get the project teaser callout
	 */

	 function maxson_portfolio_get_project_teaser_callout()
	 { 
		echo maxson_portfolio_template( 'project-teaser/callout-label.php' );
	}
}


if( ! function_exists( 'maxson_portfolio_get_project_teaser_terms' ) )
{ 
	/**
	 * Get the project teaser callout
	 */

	 function maxson_portfolio_get_project_teaser_terms()
	 { 
		echo maxson_portfolio_template( 'project-teaser/terms.php' );
	}
}

?>