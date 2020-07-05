<?php
/**
 * Admin Notice, Install Pages
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'get_plugin_data' ) )
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( MAXSON_PORTFOLIO_FILE );

$install_url = add_query_arg( array( 
	'maxson_portfolio_action' => 'install_pages'
), admin_url( 'edit.php' ) );

$install_nonce_url = wp_nonce_url( $install_url, 'install_pages' );


$skip_install_url = add_query_arg( array( 
	'maxson_portfolio_action' => 'skip_install_pages'
), admin_url( 'edit.php' ) );

$skip_install_nonce_url = wp_nonce_url( $skip_install_url, 'skip_install_pages' );

?>
<div id="maxson-portfolio-message" class="maxson-portfolio-message updated settings-error notice is-dismissible custom-dismissible">
	<p><?php printf( __( 'Thank you for installing %1$s%2$s%3$s.', 'maxson' ), '<strong>', $plugin_data['Name'], '</strong>' ); ?></p>
	<p><a href="<?php echo esc_url( $install_nonce_url ); ?>" class="button button-primary"><?php _e( 'Install Pages', 'maxson' ); ?></a></p>
	<a href="<?php echo esc_url( $skip_install_nonce_url ); ?>" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'maxson' ); ?></span></a>
</div>