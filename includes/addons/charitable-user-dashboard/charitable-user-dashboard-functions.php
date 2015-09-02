<?php 

/**
 * Charitable User Dashboard Functions. 
 * 
 * @package 	Charitable/Functions/User Dashboard
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the Charitable_User_Dashboard object.
 *
 * @return 	Charitable_User_Dashboard
 * @since 	1.0.0
 */
function charitable_user_dashboard() {

	return charitable()->get_registered_object( 'Charitable_User_Dashboard' );

}