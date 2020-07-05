<?php
/** 
 * Plugin Name: Portfolio Projects by Maxson
 * Description: The easiest way to manage you Portfolio Projects with WordPress. Manage, edit, and create new portfolio projects. Display your portfolio projects using widgets and template tags.
 * Tags: maxson, portfolio projects, portfolio, projects, custom post type, custom taxonomy, images, custom fields
 * Author: Thomas Maxson
 * Version: 2.4.0
 * Author URI: http://thomasmaxson.com/
 * Text Domain: maxson
 * Domain Path: /languages/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * @author      Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio_Projects
 * @license     GPL-2.0+
 * @version		1.0
 * 
 * Copyright 2008-2019 Thomas Maxson (email: hello at thomasmaxson dot com)
 * 
 * Maxson Portfolio Projects ("Projects") is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.
 * 
 * Projects is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * A copy of the GNU General Public License has been included with Projects.
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	header( 'Status: 403 Forbidden' ); 
	header( 'HTTP/1.1 403 Forbidden' );
	exit();

} // endif

final class Maxson_Portfolio_Projects
{ 
	/**
	 * @var         Single instance of the class
	 */

	private static $_instance;


	/**
	 * Ensure only one instance is loaded or can be loaded
	 * 
	 * @return      main instance
	 */

	public static function instance()
	{ 
		if( ! isset( self::$_instance ) && ! ( self::$_instance instanceof self ) )
		{ 
			self::$_instance = new self();

		} // endif

		return self::$_instance;
	}


	/**
	 * Throw error on object clone
	 * Singleton design pattern. Only one object, so no clones for you
	 * 
	 * @return      void
	 */

	public function __clone()
	{ 
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'maxson' ), '1.0' );
	}


	/**
	 * Disable unserializing of the class
	 * 
	 * @return      void
	 */

	public function __wakeup()
	{ 
		doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'maxson' ), '1.0' );
	}


	/**
	 * Constructor
	 * 
	 * @return      void
	 */

	public function __construct()
	{ 
		$this->define_constants();
		$this->includes();
		$this->plugin_hooks();

		do_action( 'maxson_portfolio_loaded' );
	}


	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * 
	 * @return      bool
	 */

	private function is_request( $type )
	{ 
		switch( $type )
		{ 
			case 'ajax': 
				return defined( 'DOING_AJAX' );
				break;

			case 'cron': 
				return defined( 'DOING_CRON' );
				break;

			case 'admin': 
				return is_admin();
				break;

			case 'frontend': 
				return ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) );
				break;
		}
	}


	/**
	 * Check the active theme
	 * 
	 * @param       string $theme Theme slug to check
	 * @return      bool
	 */

	private function is_active_theme( $theme )
	{ 
		return $theme === get_template();
	}


	/**
	 * Define plugin hooks
	 * 
	 * @return      void
	 */

	private function plugin_hooks()
	{ 
		load_plugin_textdomain( 'maxson', false, MAXSON_PORTFOLIO_DIRNAME . '/languages/' );

		add_action( 'current_screen', array( &$this, 'conditonal_includes' ) );

		register_activation_hook( __FILE__, array( 'Maxson_Portfolio_Projects_Install', 'install' ) );
		register_deactivation_hook( __FILE__, array( 'Maxson_Portfolio_Projects_Install', 'uninstall' ) );

		add_action( 'init', array( &$this, 'init' ), 0 );
	}


	/**
	 * Define plugin constants
	 * 
	 * @return      void
	 */

	private function define_constants()
	{ 
		if( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$this->define( 'MAXSON_PORTFOLIO_FILE', __FILE__ );

		$plugin_data = get_plugin_data( MAXSON_PORTFOLIO_FILE );

		$this->define( 'MAXSON_PORTFOLIO_BASENAME', plugin_basename( MAXSON_PORTFOLIO_FILE ) );
		$this->define( 'MAXSON_PORTFOLIO_PLUGIN_URL', trailingslashit( plugin_dir_url( MAXSON_PORTFOLIO_FILE ) ) );
		$this->define( 'MAXSON_PORTFOLIO_PLUGIN_PATH', trailingslashit( plugin_dir_path( MAXSON_PORTFOLIO_FILE ) ) );
		$this->define( 'MAXSON_PORTFOLIO_DIRNAME', trailingslashit( dirname( MAXSON_PORTFOLIO_FILE ) ) );

		$this->define( 'MAXSON_PORTFOLIO_INCLUDES', MAXSON_PORTFOLIO_DIRNAME . trailingslashit( 'includes' ) );
		$this->define( 'MAXSON_PORTFOLIO_INCLUDES_ADMIN', MAXSON_PORTFOLIO_INCLUDES . trailingslashit( 'admin' ) );
		$this->define( 'MAXSON_PORTFOLIO_INCLUDES_BLOCKS', MAXSON_PORTFOLIO_INCLUDES . trailingslashit( 'blocks' ) );

	//	$this->define( 'MAXSON_PORTFOLIO_NAME', $plugin_data['Name'] );
		$this->define( 'MAXSON_PORTFOLIO_VERSION', $plugin_data['Version'] );

		// Moved to Status Page
	//	define( 'MAXSON_PORTFOLIO_DEBUG', false );
	//	define( 'MAXSON_PORTFOLIO_DEBUG_TEMPLATE', false );

		// Deprecated
	//	define( 'MAXSON_PORTFOLIO_LOAD_CSS', true );
	//	define( 'MAXSON_PORTFOLIO_LOAD_JS', true );
	}


	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 * @return      void
	 */

	private function define( $name, $value )
	{ 
		if( ! defined( $name ) )
		{ 
			define( $name, $value );

		} // endif
	}


	/**
	 * Include required plugin files used for admin and frontend pages
	 * 
	 * @return      void
	 */

	public function includes()
	{ 
		$includes_folder  = MAXSON_PORTFOLIO_INCLUDES;
		$admin_folder     = MAXSON_PORTFOLIO_INCLUDES_ADMIN;
	//	$blocks_folder    = MAXSON_PORTFOLIO_INCLUDES_BLOCKS;
		$metaboxes_folder = $admin_folder . trailingslashit( 'meta-boxes' );

		// Functions
		include_once( "{$includes_folder}functions-core.php"        );
		include_once( "{$includes_folder}functions-conditional.php" );
		include_once( "{$includes_folder}functions-deprecated.php"  );
		include_once( "{$includes_folder}functions-media.php"       );
		include_once( "{$includes_folder}functions-term.php"        );
		include_once( "{$includes_folder}functions-query.php"       );

		// Walkers
		include_once( "{$includes_folder}walkers/class-cat-dropdown-walker.php" );
		include_once( "{$includes_folder}walkers/class-cat-list-walker.php"     );

		// Classes
		include_once( "{$includes_folder}class-install.php"           );
		include_once( "{$includes_folder}class-meta.php"              );
		include_once( "{$includes_folder}class-media.php"             );
		include_once( "{$includes_folder}class-permalinks.php"        );
		include_once( "{$includes_folder}class-post-types.php"        );
		include_once( "{$includes_folder}class-query.php"             );
	//	include_once( "{$includes_folder}class-rest.php"              );
		include_once( "{$includes_folder}class-taxonomies.php"        );
		include_once( "{$includes_folder}class-template-loader.php"   );
		include_once( "{$includes_folder}class-widgets.php"           );

		// Blocks
	//	if( function_exists( 'gutenberg_init' ) && 
	//		function_exists( 'register_block_type' ) )
	//	{ 
	//		include_once( "{$blocks_folder}class-blocks.php"          );
	//		include_once( "{$blocks_folder}archive/index.php"         );

	//	} // endif

		// Admin Functions
		include_once( "{$admin_folder}functions-admin.php"            );
		include_once( "{$admin_folder}functions-admin-hooks.php"      );
		include_once( "{$admin_folder}functions-admin-settings.php"   );
		include_once( "{$admin_folder}functions-admin-tools.php"      );

		// Project Factory
		include_once( "{$includes_folder}class-project-factory.php"   );
		include_once( "{$includes_folder}class-project.php"           );
			include_once( "{$includes_folder}class-project-audio.php"     );
			include_once( "{$includes_folder}class-project-gallery.php"   );
			include_once( "{$includes_folder}class-project-video.php"     );

		//  Do not conditionally load Customizer code with an is_admin() check
		include_once( "{$includes_folder}admin/class-admin-customize.php"  );


		if( $this->is_request( 'frontend' ) )
		{ 
			// Template
			include_once( "{$includes_folder}template-functions.php" );
		//	include_once( "{$includes_folder}template-hooks.php"     );

			include_once( "{$includes_folder}class-frontend.php" );
			include_once( "{$includes_folder}class-ssl.php"      );

		//	if( $this->is_active_theme( 'twentyseventeen' ) )
		//	{ 
		//		include_once( 'includes/theme-support/class-twentyseventeen.php' );

		//	} // endif
		} // endif


		if( $this->is_request( 'ajax' ) )
		{ 
			include_once( "{$admin_folder}class-admin-ajax.php" );

		} // endif


		if( $this->is_request( 'admin' ) )
		{ 
			include_once( "{$admin_folder}class-admin.php"                 );
			include_once( "{$admin_folder}class-admin-assets.php"          );
			include_once( "{$admin_folder}class-admin-contextual-help.php" );
			include_once( "{$admin_folder}class-admin-media.php"           );
			include_once( "{$admin_folder}class-admin-menus.php"           );
			include_once( "{$admin_folder}class-admin-meta-boxes.php"      );
			include_once( "{$admin_folder}class-admin-notices.php"         );
			include_once( "{$admin_folder}class-admin-permalinks.php"      );
			include_once( "{$admin_folder}class-admin-pointers.php"        );
			include_once( "{$admin_folder}class-admin-post-types.php"      );
			include_once( "{$admin_folder}class-admin-settings.php"        );
			include_once( "{$admin_folder}class-admin-taxonomies.php"      );
			include_once( "{$admin_folder}class-admin-tools.php"           );
			include_once( "{$admin_folder}class-admin-users.php"           );

			// Meta Boxes
			include_once( "{$metaboxes_folder}class-post-meta-box-project-details.php"  );
			include_once( "{$metaboxes_folder}class-post-meta-box-project-excerpt.php"  );
			include_once( "{$metaboxes_folder}class-post-meta-box-project-media.php"    );
			include_once( "{$metaboxes_folder}class-post-meta-box-project-promoted.php" );

			// Meta Boxes, Media Specific
			include_once( "{$metaboxes_folder}media/class-meta-box-project-audio.php"   );
			include_once( "{$metaboxes_folder}media/class-meta-box-project-gallery.php" );
			include_once( "{$metaboxes_folder}media/class-meta-box-project-video.php"   );

		} // endif
	}


	/**
	 * Include admin files conditionally
	 * 
	 * @return 		void
	 */

	public function conditonal_includes()
	{ 
		$admin_folder = MAXSON_PORTFOLIO_INCLUDES_ADMIN;

		$screen = get_current_screen();

		switch( $screen->id )
		{ 
			case 'dashboard': 
				include_once( "{$admin_folder}class-admin-dashboard.php" );
				break;

		} // endswitch
	}


	/**
	 * Init Packages when WordPress Initializes
	 * 
	 * @return      void
	 */

	public function init()
	{ 
		do_action( 'maxson_portfolio_before_init' );

		// Load class instances
		$this->project_factory = new Maxson_Portfolio_Projects_Factory();

		// Init action
		do_action( 'maxson_portfolio_init' );
	}


	/**
	 * Get the plugin url.
	 * 
	 * @return      string
	 */

	public function plugin_url()
	{ 
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}


	/**
	 * Get the plugin path.
	 * 
	 * @return      string
	 */

	public function plugin_path()
	{ 
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Get the template path.
	 * 
	 * @return      string
	 */

	public function template_path()
	{ 
		return apply_filters( 'maxson_portfolio_template_path', 'portfolio' );
	}


	/**
	 * Get the upload path.
	 * 
	 * @return      string
	 */

	public function upload_folder()
	{ 
		return apply_filters( 'maxson_portfolio_upload_folder', 'portfolio' );
	}

} // endclass


/**
 * Returns the main instance of class to prevent the need to use globals
 * 
 * @return      object
 */

function Portfolio_Projects()
{ 
	return Maxson_Portfolio_Projects::instance();
}

Portfolio_Projects();

?>