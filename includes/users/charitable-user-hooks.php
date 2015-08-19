<?php 
/**
 * Charitable User Hooks. 
 *
 * Action/filter hooks used for Charitable user registrations & profile changes. 
 * 
 * @package     Charitable/Functions/Donations
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Save a profile. 
 *
 * @see Charitable_Profile_Form::update_profile()
 */
add_action( 'charitable_update_profile', array( 'Charitable_Profile_Form', 'update_profile' ) );     

/**
 * Save a user after registration. 
 *
 * @see Charitable_Registration_Form::save_registration()
 */
add_action( 'charitable_save_registration', array( 'Charitable_Registration_Form', 'save_registration' ) );