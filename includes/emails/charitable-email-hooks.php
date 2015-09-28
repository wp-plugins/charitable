<?php 
/**
 * Charitable Email Hooks. 
 *
 * Action/filter hooks used for Charitable emails. 
 * 
 * @package     Charitable/Functions/Emails
 * @version     1.0.3
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Send the Donation Receipt email.
 *
 * This email is sent to the donor, immediately after they have finished making their donation.
 *
 * @see Charitable_Email_Donation_Receipt::send_with_donation_id()
 */
add_action( 'charitable_after_save_donation', array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );
add_action( 'charitable_after_update_donation', array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );

/**
 * Send the Donation Notification email. 
 *
 * This email is sent to the website admin or other recipients, after the donation has been made.
 *
 * @see Charitable_Email_New_Donation::send_with_donation_id()
 */
add_action( 'charitable_after_save_donation', array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );
add_action( 'charitable_after_update_donation', array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );

/**
 * Send the Campaign Ended email.
 *
 * This email can be sent to any recipients, within 24 hours after a campaign has reached its end date.
 *
 * @see Charitable_Email_Campaign_End::send_with_campaign_id()
 */
add_action( 'charitable_campaign_end', array( 'Charitable_Email_Campaign_End', 'send_with_campaign_id' ) );