<?php
/**
 * Plugin-specific template loader
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


if( ! class_exists( 'Maxson_Portfolio_Projects_Template_Loader' ) )
{ 
	class Maxson_Portfolio_Projects_Template_Loader { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_filter( 'template_include', array( &$this, 'template_loader' ), 99 );

			add_filter( 'archive-portfolio_project_template', array( &$this, 'archive_template' ) );
			add_filter( 'single-portfolio_project_template', array( &$this, 'single_template' ) );
		}


		/**
		 * Load a template
		 * Handles template usage so that we can use our own templates instead of the themes
		 * 
		 * Templates are in the 'templates' folder. Projects look for theme overrides in /theme/portfolio/ by default
		 * 
		 * @param       mixed $template
		 * @return      string
		 */

		public function template_loader( $template )
		{ 
			global $post;

			$find = array( 'portfolio-projects.php' );
			$file = '';

			$template_path = Portfolio_Projects()->template_path();
			$plugin_path   = MAXSON_PORTFOLIO_PLUGIN_PATH;

			if( is_project() )
			{ 
				$file = 'single-project.php';

				$find[] = $file;
				$find[] = "{$template_path}/{$file}";


			} elseif( is_portfolio_taxonomy() )
			{ 
				$term_obj = get_queried_object();

				$taxonomy = $term_obj->taxonomy;
				$slug     = $term_obj->slug;

				$file = "taxonomy-{$taxonomy}.php";

				$find[] = "taxonomy-{$taxonomy}-{$slug}.php";
				$find[] = "{$template_path}/taxonomy-{$taxonomy}-{$slug}.php";

				$find[] = "taxonomy-{$taxonomy}.php";
				$find[] = "{$template_path}/taxonomy-{$taxonomy}.php";

				$find[] = $file;
				$find[] = "{$template_path}/{$file}";

			} elseif( maxson_portfolio_is_archive_page() || is_post_type_archive( self::POST_TYPE ) )
			{ 
				$file = 'archive-portfolio.php';

				$find[] = $file;
				$find[] = "{$template_path}/{$file}";

			} // endif


			if( $file )
			{ 
				$template = locate_template( array_unique( $find ), false );

				if( ! $template )
					$template = "{$plugin_path}/templates/{$file}";

			} // endif

			return $template;
		}


		/**
		 * Change the template name for archive portfolio projects
		 * 
		 * @param       string $template Path to the template. See {@see locate_template()}.
		 *
		 * @return      string
		 */

		public function archive_template( $template )
		{ 
			return 'archive-portfolio';
		}
			

		/**
		 * Change the template name for single portfolio projects
		 * 
		 * @param       string $template Path to the template. See {@see locate_template()}.
		 *
		 * @return      string
		 */

		public function single_template( $template )
		{ 
			return 'single-project';
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Template_Loader();

?>