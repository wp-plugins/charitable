<?php 
/**
 * Charitable Template Hooks. 
 *
 * Action/filter hooks used for Charitable functions/templates
 * 
 * @package     Charitable/Functions/Templates
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Modifying the output of the_content(). 
 *
 * @see charitable_template_campaign_content
 * @see charitable_template_donation_form_content
 * @see charitable_template_donation_receipt_content
 */
add_filter( 'the_content', 'charitable_template_campaign_content' );
add_filter( 'the_content', 'charitable_template_donation_form_content' );
add_filter( 'the_content', 'charitable_template_donation_receipt_content' );

/**
 * Single campaign, before content.
 *
 * @see charitable_template_campaign_description
 * @see charitable_template_campaign_summary
 */
add_action( 'charitable_campaign_content_before', 'charitable_template_campaign_description', 4 );
add_action( 'charitable_campaign_content_before', 'charitable_template_campaign_summary', 6 );

/**
 * Single campaign, campaign summary. 
 *
 * @see charitable_template_campaign_percentage_raised
 * @see charitable_template_campaign_donation_summary
 * @see charitable_template_campaign_donor_count
 * @see charitable_template_campaign_time_left
 * @see charitable_template_donate_button
 */
add_action( 'charitable_campaign_summary', 'charitable_template_campaign_percentage_raised', 4 );
add_action( 'charitable_campaign_summary', 'charitable_template_campaign_donation_summary', 6 );
add_action( 'charitable_campaign_summary', 'charitable_template_campaign_donor_count', 8 );
add_action( 'charitable_campaign_summary', 'charitable_template_campaign_time_left', 10 );
add_action( 'charitable_campaign_summary', 'charitable_template_donate_button', 12 );

/** 
 * Single campaign, after content. 
 *
 * @see charitable_template_campaign_donation_form_in_page
 */
add_action( 'charitable_campaign_content_after', 'charitable_template_campaign_donation_form_in_page', 4 );

/**
 * Campaigns loop, before title.
 * 
 * @see charitable_template_campaign_loop_thumbnail
 */
add_action( 'charitable_campaign_content_loop_before_title', 'charitable_template_campaign_loop_thumbnail', 10 );

/**
 * Campaigns loop, after the main title.
 *
 * @see charitable_template_campaign_description
 * @see charitable_template_campaign_progress_bar
 * @see charitable_template_campaign_loop_donation_stats
 * @see charitable_template_campaign_donate_link
 */
add_action( 'charitable_campaign_content_loop_after', 'charitable_template_campaign_description', 4 );
add_action( 'charitable_campaign_content_loop_after', 'charitable_template_campaign_progress_bar', 6 );
add_action( 'charitable_campaign_content_loop_after', 'charitable_template_campaign_loop_donation_stats', 8 );
add_action( 'charitable_campaign_content_loop_after', 'charitable_template_campaign_loop_donate_link', 10 );

/** 
 * Donation receipt, after the page content (if there is any).
 *
 * @see charitable_template_donation_receipt_summary
 * @see charitable_template_donation_receipt_offline_payment_instructions
 */
add_action( 'charitable_donation_receipt', 'charitable_template_donation_receipt_summary', 4 );
add_action( 'charitable_donation_receipt', 'charitable_template_donation_receipt_offline_payment_instructions', 6 );
add_action( 'charitable_donation_receipt', 'charitable_template_donation_receipt_details', 8 );

/**
 * Footer, right before the closing body tag.
 *
 * @see charitable_template_campaign_modal_donation_window
 */
add_action( 'wp_footer', 'charitable_template_campaign_modal_donation_window' );

/**
 * Donation form, before the donor fields. 
 *
 * @see charitable_template_donation_form_login
 * @see charitable_template_donation_form_donor_details
 * @see charitable_template_donation_form_donor_fields_hidden_wrapper_start
 */
add_action( 'charitable_donation_form_donor_fields_before', 'charitable_template_donation_form_login', 4 );
add_action( 'charitable_donation_form_donor_fields_before', 'charitable_template_donation_form_donor_details', 6 );
add_action( 'charitable_donation_form_donor_fields_before', 'charitable_template_donation_form_donor_fields_hidden_wrapper_start', 8 );

/**
 * Donation form, after the donor fields. 
 *
 * @see charitable_template_donation_form_donor_fields_wrapper_end
 */
add_action( 'charitable_donation_form_donor_fields_after', 'charitable_template_donation_form_donor_fields_hidden_wrapper_end', 4 );


