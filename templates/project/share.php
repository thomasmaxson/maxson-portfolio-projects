<?php
/**
 * Single Project Share
 * Sharing plugins can hook into here or you can add your own code directly
 * 
 * @author      Thomas Maxson
 * @package     Portfolio_Projects/templates
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


do_action( 'maxson_portfolio_share' );

?>