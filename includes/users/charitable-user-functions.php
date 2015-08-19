<?php 

/**
 * Charitable User Functions. 
 *
 * User related functions.
 * 
 * @package     Charitable/Functions/User
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns a Charitable_User object for the given user. 
 *
 * This will first attempt to retrieve it from the object cache to prevent duplicate objects.
 *
 * @param   int     $user_id
 * @param   boolean $foce
 * @return  Charitable_User
 * @since   1.0.0
 */
function charitable_get_user( $user_id, $force = false ) {
    $user = wp_cache_get( $user_id, 'charitable_user', $force );

    if ( ! $user ) {
        $user = new Charitable_User( $user_id );
        wp_cache_set( $user_id, $user, 'charitable_user' );            
    }

    return $user;
}