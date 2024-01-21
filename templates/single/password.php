<?php
/**
 * Project password protected
 * 
 * Override this template by copying it to yourtheme/portfolio/single-project/password-required.php
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

if( post_password_required() )
{ 
	echo get_the_password_form();
	return;

} // endif

?>