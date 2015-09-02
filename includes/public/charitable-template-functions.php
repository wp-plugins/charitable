<?php 
/**
 * Charitable Template Functions. 
 *
 * Functions used with template hooks.
 * 
 * @package     Charitable/Functions/Templates
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**********************************************/ 
/* SINGLE CAMPAIGN CONTENT
/**********************************************/

if ( ! function_exists( 'charitable_template_campaign_content' ) ) :
    /**
     * Display the campaign content.
     *
     * This is used on the_content filter.
     *
     * @param   string  $content
     * @return  string 
     * @since   1.0.0
     */ 
    function charitable_template_campaign_content( $content ) {
        if ( Charitable::CAMPAIGN_POST_TYPE != get_post_type() ) {
            return $content;
        }        

        if ( charitable_is_page( 'campaign_donation_page' ) ) {
            return $content;
        }

        /**
         * If you do not want to use the default campaign template, use this filter and return false. 
         *
         * @uses    charitable_use_campaign_template
         */
        if ( ! apply_filters( 'charitable_use_campaign_template', true ) ) {
            return $content;
        }

        /** 
         * Remove ourselves as a filter to prevent eternal recursion if apply_filters('the_content') 
         * is called by one of the templates.
         */
        remove_filter( 'the_content', 'charitable_template_campaign_content' );

        ob_start();
        
        charitable_template( 'content-campaign.php', array( 'content' => $content, 'campaign' => charitable_get_current_campaign() ) );

        $content = ob_get_clean();

        add_filter( 'the_content', 'charitable_template_campaign_content' );

        return $content;
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_description' ) ) :
    /**
     * Display the campaign description before the summary and rest of content. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  void 
     * @since   1.0.0
     */
    function charitable_template_campaign_description( $campaign ) {
        charitable_template( 'campaign/description.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_finished_notice' ) ) :
    /**
     * Display the campaign finished notice.
     *
     * @param   Charitable_Campaign $campaign
     * @return  void 
     * @since   1.0.0
     */
    function charitable_template_campaign_finished_notice( $campaign ) {
        if ( ! $campaign->has_ended() ) {
            return;
        }
        
        charitable_template( 'campaign/finished-notice.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_percentage_raised' ) ) :
    /**
     * Display the percentage that the campaign has raised in summary block. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  boolean     True if the template was displayed. False otherwise.
     * @since   1.0.0
     */
    function charitable_template_campaign_percentage_raised( $campaign ) {
        if ( ! $campaign->has_goal() ) {
            return false;
        }

        charitable_template( 'campaign/summary-percentage-raised.php', array( 'campaign' => $campaign ) );

        return true;
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_donation_summary' ) ) :
    /**
     * Display campaign goal in summary block. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  true
     * @since   1.0.0
     */
    function charitable_template_campaign_donation_summary( $campaign ) {
        charitable_template( 'campaign/summary-donations.php', array( 'campaign' => $campaign ) );
        return true;
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_donor_count' ) ) :
    /**
     * Display number of campaign donors in summary block.
     *
     * @param   Charitable_Campaign $campaign
     * @return  true
     * @since   1.0.0
     */
    function charitable_template_campaign_donor_count( $campaign ) {
        charitable_template( 'campaign/summary-donors.php', array( 'campaign' => $campaign ) );
        return true;
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_time_left' ) ) :
    /**
     * Display the amount of time left in the campaign in the summary block. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  boolean     True if the template was displayed. False otherwise.
     * @since   1.0.0
     */
    function charitable_template_campaign_time_left( $campaign ) {
        if ( $campaign->is_endless() ) {
            return false;
        }

        charitable_template( 'campaign/summary-time-left.php', array( 'campaign' => $campaign ) );
        return true;
    }
endif;

if ( ! function_exists( 'charitable_template_donate_button' ) ) :
    /**
     * Display donate button or link in the campaign summary.
     *
     * @param   Charitable_Campaign $campaign
     * @return  boolean     True if the template was displayed. False otherwise.
     * @since   1.0.0
     */
    function charitable_template_donate_button( $campaign ) {
        if ( $campaign->has_ended() ) {
            return false;
        }

        $campaign->donate_button_template();

        return true;
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_summary' ) ) :
    /**
     * Display campaign summary before rest of campaign content. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  void 
     * @since   1.0.0
     */
    function charitable_template_campaign_summary( $campaign ) {
        charitable_template( 'campaign/summary.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_progress_bar' ) ) :
    /**
     * Output the campaign progress bar. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_progress_bar( $campaign ) {
        charitable_template( 'campaign/progress-bar.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_donate_button' ) ) :
    /**
     * Output the campaign donate button.
     *
     * @param   Charitable_Campaign $campaign 
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_donate_button( $campaign ) {
        charitable_template( 'campaign/donate-button.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_donate_link' ) ) :
    /**
     * Output the campaign donate link. 
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_donate_link( $campaign ) {
        charitable_template( 'campaign/donate-link.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_status_tag' ) ) :
    /**
     * Output the campaign status tag.
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_status_tag( $campaign ) {
        charitable_template( 'campaign/status-tag.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_donation_form_in_page' ) ) :
    /**
     * Add the donation form straight into the campaign page. 
     *
     * @param   Charitable_Campaign $campaign 
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_donation_form_in_page( Charitable_Campaign $campaign ) {
        if ( $campaign->has_ended() ) {
            return;
        }
        
        if ( 'same_page' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
            charitable_get_current_donation_form()->render();
        }
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_modal_donation_window' ) ) : 
    /**
     * Adds the modal donation window to a campaign page.
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_modal_donation_window() {
        if ( Charitable::CAMPAIGN_POST_TYPE != get_post_type() ) {
            return;
        }

        $campaign = charitable_get_current_campaign();

        if ( $campaign->has_ended() ) {
            return;
        }

        if ( 'modal' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
            charitable_template( 'campaign/donate-modal-window.php', array( 'campaign' => $campaign ) );            
        }
    }
endif;

/**********************************************/ 
/* CAMPAIGN LOOP
/**********************************************/

if ( ! function_exists( 'charitable_template_campaign_loop' ) ) :
    /**
     * Display the campaign loop.
     *
     * This is used instead of the_content filter. 
     *     
     * @param   WP_Query $campaigns
     * @param   int     $columns
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_loop( $campaigns = false, $columns = 1 ) {
        if ( ! $campaigns ) {
            global $wp_query;
            $campaigns = $wp_query;
        }
        

        charitable_template( 'campaign-loop.php', array( 'campaigns' => $campaigns, 'columns' => $columns ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_loop_thumbnail' ) ) :
    /**
     * Output the campaign thumbnail on campaigns displayed within the loop.
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_loop_thumbnail( $campaign ) {
        charitable_template( 'campaign-loop/thumbnail.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_loop_donation_stats' ) ) :
    /**
     * Output the campaign donation status on campaigns displayed within the loop.
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_loop_donation_stats( $campaign ) {
        charitable_template( 'campaign-loop/donation-stats.php', array( 'campaign' => $campaign ) );
    }
endif;

if ( ! function_exists( 'charitable_template_campaign_loop_donate_link' ) ) : 
    /**
     * Output the campaign donation status on campaigns displayed within the loop.
     *
     * @param   Charitable_Campaign $campaign
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_campaign_loop_donate_link( $campaign ) {
        charitable_template( 'campaign-loop/donation-link.php', array( 'campaign' => $campaign ) );
    }
endif;

/**********************************************/
/* DONATION FORM
/**********************************************/

if ( ! function_exists( 'charitable_template_donation_form_content' ) ) :
    /**
     * Display the donation form. This is used with the_content filter.
     *
     * @param   string  $content
     * @return  string
     * @since   1.0.0
     */
    function charitable_template_donation_form_content( $content ) {
        if ( charitable_is_page( 'campaign_donation_page' ) ) {
            ob_start();
            
            charitable_template( 'content-donation-form.php' );

            $content = ob_get_clean();
        }

        return $content;
    }
endif;

/**********************************************/
/* DONATION RECEIPT
/**********************************************/

if ( ! function_exists( 'charitable_template_donation_receipt_content' ) ) :
    /**
     * Display the donation form. This is used with the_content filter.
     *
     * @param   string  $content
     * @return  string
     * @since   1.0.0
     */
    function charitable_template_donation_receipt_content( $content ) {
        if ( ! charitable_is_page( 'donation_receipt_page' ) ) {
            return $content;
        }
            
        $donation = charitable_get_current_donation();
        
        if ( ! $donation ) {
            return $content;
        }

        ob_start();            
                
        charitable_template( 'content-donation-receipt.php', array( 'content' => $content, 'donation' => $donation ) );        

        $content = ob_get_clean();

        return $content;
    }
endif;

if ( ! function_exists( 'charitable_template_donation_receipt_summary' ) ) : 
    /**
     * Display the donation receipt summary. 
     *
     * @param   Charitable_Donation $donation
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_receipt_summary( Charitable_Donation $donation ) {
        charitable_template( 'donation-receipt/summary.php', array( 'donation' => $donation ) );
    }
endif;

if ( ! function_exists( 'charitable_template_donation_receipt_offline_payment_instructions' ) ) : 
    /**
     * Display the offline payment instructions, if applicable.
     *
     * @param   Charitable_Donation $donation
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_receipt_offline_payment_instructions( Charitable_Donation $donation ) {
        if ( 'offline' != $donation->get_gateway() ) {
            return;
        }

        charitable_template( 'donation-receipt/offline-payment-instructions.php', array( 'donation' => $donation ) );
    }
endif;

if ( ! function_exists( 'charitable_template_donation_receipt_details' ) ) : 
    /**
     * Display the donation details.
     *
     * @param   Charitable_Donation $donation
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_receipt_details( Charitable_Donation $donation ) {
        charitable_template( 'donation-receipt/details.php', array( 'donation' => $donation ) );
    }
endif;

/**********************************************/
/* DONATION FORM
/**********************************************/

if ( ! function_exists( 'charitable_template_donation_form_login' ) ) :
    /**
     * Display the login form before the user fields within a donation form.
     *
     * @param   Charitable_Form $form
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_form_login( Charitable_Form $form ) {
        $user = $form->get_user();

        if ( $user ) {
            return;
        }

        charitable_template( 'donation-form/donor-fields/login-form.php', array( 'user' => $user ) );
    }
endif;

if ( ! function_exists( 'charitable_template_donation_form_donor_details' ) ) :
    /**
     * Display the donor's saved details if the user is logged in.
     *
     * @param   Charitable_Form $form
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_form_donor_details( Charitable_Form $form ) {
        $user = $form->get_user();

        if ( ! $user ) {
            return;
        }

        charitable_template( 'donation-form/donor-fields/donor-details.php', array( 'user' => $user ) );
    }
endif;

if ( ! function_exists( 'charitable_template_donation_form_donor_fields_hidden_wrapper_start' ) ) :
    /**
     * If the user is logged in, adds a wrapper around the donor fields that hide them.
     *
     * @param   Charitable_Form $form
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_form_donor_fields_hidden_wrapper_start( Charitable_Form $form ) {
        if ( ! $form->get_user() ) {
            return;
        }

        charitable_template( 'donation-form/donor-fields/hidden-fields-wrapper-start.php' );
    }
endif;

if ( ! function_exists( 'charitable_template_donation_form_donor_fields_hidden_wrapper_end' ) ) :
    /**
     * Closes the hidden donor fields wrapper div if the user is logged in.
     *
     * @param   Charitable_Form $form
     * @return  void
     * @since   1.0.0
     */
    function charitable_template_donation_form_donor_fields_hidden_wrapper_end( Charitable_Form $form ) {
        if ( ! $form->get_user() ) {
            return;
        }

        charitable_template( 'donation-form/donor-fields/hidden-fields-wrapper-end.php' );
    }
endif;