<?php
/**
 * Admin Notice, Post Meta Warning Messages
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;

} // endif


?>
<div id="maxson-portfolio-message" class="maxson-portfolio-message warning notice notice-warning is-dismissible">
	<?php foreach( $warnings as $warning )
	{ 
		echo wpautop( wp_kses_post( $warning ) );

	} // endforeach ?>
</div>