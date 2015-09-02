<?php 

/**
 * Charitable Donation Functions. 
 *
 * Donation related functions.
 * 
 * @package     Charitable/Functions/Donation
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the given donation. 
 *
 * This will first attempt to retrieve it from the object cache to prevent duplicate objects.
 *
 * @param   int     $donation_id
 * @param   boolean $foce
 * @return  Charitable_Donation
 * @since   1.0.0
 */
function charitable_get_donation( $donation_id, $force = false ) {
    $donation = wp_cache_get( $donation_id, 'charitable_donation', $force );

    if ( ! $donation ) {
        $donation = new Charitable_Donation( $donation_id );
        wp_cache_set( $donation_id, $donation, 'charitable_donation' );            
    }

    return $donation;
}

/**
 * Get the gateway used for the donation.
 *
 * @param   int     $donation_id
 * @return  string
 * @since   1.0.0
 */
function charitable_get_donation_gateway( $donation_id ) {
    return get_post_meta( $donation_id, 'donation_gateway', true );
}

/**
 * Returns the donation for the current request.
 * 
 * @return  Charitable_Donation
 * @since   1.0.0
 */
function charitable_get_current_donation() {
    return charitable_get_helper( 'request' )->get_current_donation();   
}