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
 * Get required settings page tabs
 * 
 * @return      array
 */

function maxson_portfolio_get_settings_required_tabs()
{ 
	$tabs = array( 
		'archive' => _x( 'Archive', 'Tools tab label', 'maxson' ), 
		'setup'   => _x( 'Setup', 'Tools tab label', 'maxson' )
	);

	return $tabs;
}


/**
 * Get optional settings page tabs
 * 
 * @return      array
 */

function maxson_portfolio_get_settings_optional_tabs()
{ 
	$tabs = apply_filters( 'maxson_portfolio_setting_page_tabs', array() );

	if( ! is_array( $tabs ) )
	{
		$tabs = array();

	} // endif

	return $tabs;
}


/**
 * Get settings page tabs
 * 
 * @return      array
 */

function maxson_portfolio_get_settings_tabs()
{ 
	$required_tabs = maxson_portfolio_get_settings_required_tabs();
	$optional_tabs = maxson_portfolio_get_settings_optional_tabs();

	$tabs = array_unique( array_merge( (array) $required_tabs, (array) $optional_tabs ) );

	return $tabs;
}


/**
 * Get settings active tab
 * 
 * @return      string
 */

function maxson_portfolio_get_settings_active_tab()
{ 
	$tab  = filter_input( INPUT_GET, 'tab' );
	$tabs = maxson_portfolio_get_settings_tabs();

	return ( isset( $tab ) && array_key_exists( $tab, $tabs ) ) ? $tab : key( $tabs );
}


/**
 * Display the settings form wrapper
 * 
 * @return      void
 */

function maxson_portfolio_the_settings_form( $active_tab = null )
{ 
	if( is_null( $active_tab ) || empty( $active_tab ) )
	{
		return false;

	} // endif

	$active_tab = maxson_portfolio_get_settings_active_tab();

	$tab_method = apply_filters( "maxson_portfolio_setting_page_{$active_tab}_method", 'post' );
	$tab_action = apply_filters( "maxson_portfolio_setting_page_{$active_tab}_action", 'options.php' );

	// We have to pass the active tab through so setting is registered properly
	$action_url = add_query_arg( array( 'tab' => $active_tab ), $tab_action );

	?>
	<form method="<?php echo esc_attr( $tab_method ); ?>" action="<?php echo esc_attr( $action_url ); ?>" name="form" id="maxson-portfolio-settings-options" enctype="multipart/form-data">
		<?php settings_fields( "maxson_portfolio_{$active_tab}" ); ?>
		<?php do_settings_sections( "maxson_portfolio_{$active_tab}" ); ?>
		<?php submit_button( __( 'Save Changes', 'maxson' ), 'primary', 'maxson_portfolio_settings_submit', true ); ?>
	</form>

	<?php
}


/**
 * Display settings page layout
 * 
 * @return      void
 */

function maxson_portfolio_settings_page()
{ 
	$tabs       = maxson_portfolio_get_settings_tabs();
	$active_tab = maxson_portfolio_get_settings_active_tab();

	?>
	<div class="wrap maxson-portfolio-wrap">
		<?php // screen_icon( 'options-general' ); ?>
		<h1><?php _e( 'Portfolio Settings', 'maxson' ); ?></h1>
		<?php settings_errors(); ?>
		<?php if( count( $tabs ) > 1 )
		{ 
			$output_tabs = '';

			foreach( $tabs as $key => $value )
			{ 
				$tab_url = add_query_arg( array( 
					'post_type' => 'portfolio_project', 
					'page'      => 'portfolio_settings', 
					'tab'       => $key
				), admin_url( 'edit.php' ) );

				$tab_class = ( $key == $active_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';

				$output_tabs .= sprintf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $tab_url ), esc_attr( $tab_class ), esc_html( $value ) );

			} // endforeach
					
			$output_tabs .= sprintf( '<a href="%1$s" class="nav-tab">%2$s</a>', esc_url( admin_url( 'options-media.php' ) ), esc_html__( 'Media', 'maxson' ) );

			$output_tabs .= sprintf( '<a href="%1$s" class="nav-tab">%2$s</a>', esc_url( admin_url( 'options-permalink.php#maxson_portfolio_permalink_settings' ) ), esc_html__( 'Permalinks', 'maxson' ) );

			if( ! empty( $output_tabs ) )
			{
				printf( '<nav class="nav-tab-wrapper">%1$s</nav>', $output_tabs );

			} // endif
		} // endif ?>
	
		<div class="metabox-holder">
			<?php do_action( "maxson_portfolio_settings_{$active_tab}", $active_tab ); ?>
		</div><!-- .metabox-holder -->
	</div><!-- .wrap -->

	<?php
}


/**
 * Settings for plugin-specific archive orderby options
 * 
 * @return      array
 */

function maxson_portfolio_get_archive_setting_orderby_options()
{ 
	return apply_filters( 'maxson_portfolio_setting_archive_orderby_options', array( 
		''           => __( 'Default', 'maxson' ), 
		'ID'         => __( 'Project ID', 'maxson' ), 
		'title'      => __( 'Project Title', 'maxson' ), 
		'date'       => __( 'Project Date', 'maxson' ), 
		'menu_order' => __( 'Project Menu Order', 'maxson' ), 
		'rand'       => __( 'Random', 'maxson' )
	) );
}


/**
 * Settings for plugin-specific archive order options
 * 
 * @return      array
 */

function maxson_portfolio_get_archive_setting_order_options()
{ 
	return apply_filters( 'maxson_portfolio_setting_archive_order_options', array( 
		''     => __( 'Default', 'maxson' ), 
		'ASC'  => __( 'Ascending order (lowest to highest)', 'maxson' ), 
		'DESC' => __( 'Descending order (highest to lowest)', 'maxson' )
	) );
}


/**
 * Settings for plugin-specific archive column options
 * 
 * @return      array
 */

function maxson_portfolio_get_archive_setting_column_count_options()
{ 
	return apply_filters( 'maxson_portfolio_setting_archive_column_count_options', array( 
	//	'0' => __( 'Automatic', 'maxson' ), 
		'1' => __( 'One Column (1)', 'maxson' ), 
		'2' => __( 'Two Columns (2)', 'maxson' ), 
		'3' => __( 'Three Columns (3)', 'maxson' ), 
		'4' => __( 'Four Columns (4)', 'maxson' ), 
	//	'5' => __( 'Five Columns (5)', 'maxson' ), 
		'6' => __( 'Six Columns (6)', 'maxson' )
	) );
}

?>