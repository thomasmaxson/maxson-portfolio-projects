<?php
/**
 * Plugin-specific Admin Menus
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

/**
 * Get required tools page tabs
 * 
 * @return      array
 */

function maxson_portfolio_get_tools_required_tabs()
{ 
	$tabs = array( 
		'tools' => _x( 'Tools', 'Tools tab label', 'maxson' )
	);

	// See: https://codex.wordpress.org/Roles_and_Capabilities#import
	if( current_user_can( 'import' ) || current_user_can( 'export' ) )
	{ 
		$tabs['import_export_settings'] = _x( 'Import/Export Settings', 'Tools tab label', 'maxson' );

	} // endif

	return $tabs;
}


/**
 * Get optional tools page tabs
 * 
 * @return      array
 */

function maxson_portfolio_get_tools_optional_tabs()
{ 
	$tabs = apply_filters( 'maxson_portfolio_tool_page_tabs', array() );

	if( ! is_array( $tabs ) )
	{
		$tabs = array();

	} // endif

	return $tabs;
}


/**
 * Get tools page tabs
 * 
 * @return      array
 */

function maxson_portfolio_get_tools_tabs()
{ 
	$required_tabs = maxson_portfolio_get_tools_required_tabs();
	$optional_tabs = maxson_portfolio_get_tools_optional_tabs();

	$tabs = array_unique( array_merge( (array) $required_tabs, (array) $optional_tabs ) );

	return $tabs;
}


/**
 * Get tools active tab
 * 
 * @return      string
 */

function maxson_portfolio_get_tools_active_tab()
{ 
	$tab  = filter_input( INPUT_GET, 'tab' );
	$tabs = maxson_portfolio_get_tools_tabs();

	return ( isset( $tab ) && array_key_exists( $tab, $tabs ) ) ? $tab : key( $tabs );
}


/**
 * Display the settings form wrapper
 * 
 * @return      void
 */

function maxson_portfolio_the_tools_form( $active_tab = null )
{ 
	if( is_null( $active_tab ) || empty( $active_tab ) )
		return false;

	$active_tab = maxson_portfolio_get_tools_active_tab();

	$tab_method = apply_filters( "maxson_portfolio_tool_page_{$active_tab}_method", 'post' );
	$tab_action = apply_filters( "maxson_portfolio_tool_page_{$active_tab}_action", 'options.php' );

	// We have to pass the active tab through so setting is registered properly
	$action_url = add_query_arg( array( 'tab' => $active_tab ), $tab_action );

	?>
	<form method="<?php echo esc_attr( $tab_method ); ?>" action="<?php echo esc_attr( $action_url ); ?>" name="form" id="maxson-portfolio-tools-options" enctype="multipart/form-data">
		<?php settings_fields( "maxson_portfolio_{$active_tab}" ); ?>
		<?php do_settings_sections( "maxson_portfolio_{$active_tab}" ); ?>
		<?php submit_button( __( 'Save Changes', 'maxson' ), 'primary', 'maxson_portfolio_tools_submit', true ); ?>
	</form>

	<?php
}


/**
 * Display tools page layout
 * 
 * @return      void
 */

function maxson_portfolio_tools_page()
{ 
	$tabs       = maxson_portfolio_get_tools_tabs();
	$active_tab = maxson_portfolio_get_tools_active_tab();

	?><div class="wrap maxson-portfolio-wrap">
		<?php // screen_icon( 'options-general' ); ?>
		<h1><?php _e( 'Portfolio Tools', 'maxson' ); ?></h1>
		<?php settings_errors(); ?>
		<?php if( count( $tabs ) > 1 )
		{ 
			$output_tabs = '';

			foreach( $tabs as $key => $value )
			{ 
				$url = add_query_arg( array( 
					'post_type' => 'portfolio_project', 
					'page'      => 'portfolio_tools', 
					'tab'       => $key
				), admin_url( 'edit.php' ) );

				$class = ( $key == $active_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';

				$output_tabs .= sprintf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $url ), esc_attr( $class ), esc_html( $value ) );

			} // endforeach

			printf( '<nav class="nav-tab-wrapper">%1$s</nav>', $output_tabs );

		} // endif ?>
	
		<div class="metabox-holder">
			<?php do_action( "maxson_portfolio_tools_{$active_tab}", $active_tab ); ?>
		</div><!-- .metabox-holder -->
	</div><!-- .wrap -->

	<?php
}

?>