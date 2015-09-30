<?php 

/**
 * Charitable Currency Functions. 
 *
 * @package     Charitable/Functions/Currency
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Return currency helper class.  
 *
 * @return  Charitable_Currency
 * @since   1.0.0
 */
function charitable_get_currency_helper() {
    return charitable()->get_currency_helper();
}

/**
 * Return the site currency.
 *
 * @return  string
 * @since   1.0.0
 */
function charitable_get_currency() {
    return charitable_get_option( 'currency', 'AUD' );
}

/**
 * Formats the monetary amount. 
 * 
 * @param   string $amount
 * @return  string
 * @since   1.1.5
 */
function charitable_format_money( $amount ) {
    return charitable_get_currency_helper()->get_monetary_amount( $amount );
}