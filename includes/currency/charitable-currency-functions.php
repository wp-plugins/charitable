<?php 

/**
 * Charitable Currency Functions. 
 *
 * @package     Charitable/Functions/Currency
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
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