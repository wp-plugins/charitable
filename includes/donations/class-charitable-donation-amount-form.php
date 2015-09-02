<?php
/**
 * Donation amount form model class.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Donation_Amount_Form
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation_Amount_Form' ) ) : 

/**
 * Charitable_Donation_Amount_Form
 *
 * @since       1.0.0
 */
class Charitable_Donation_Amount_Form extends Charitable_Donation_Form implements Charitable_Donation_Form_Interface {

    /** 
     * @var     Charitable_Campaign
     */
    protected $campaign;

    /**
     * @var     array
     */
    protected $form_fields;

    /**
     * @var     string
     */
    protected $nonce_action = 'charitable_donation_amount';

    /**
     * @var     string
     */
    protected $nonce_name = '_charitable_donation_amount_nonce';

    /**
     * Action to be executed upon form submission. 
     *
     * @var     string
     * @access  protected
     */
    protected $form_action = 'make_donation_streamlined';

    /**
     * Set up callbacks for actions and filters. 
     *
     * @return  void
     * @access  protected
     * @since   1.0.0
     */
    protected function attach_hooks_and_filters() {
        parent::attach_hooks_and_filters();

        // add_action( 'charitable_donation_amount_form_submit', array( $this, 'redirect_after_submission' ) );

        remove_filter( 'charitable_donation_form_gateway_fields', array( $this, 'add_credit_card_fields' ), 10, 2 );
        remove_action( 'charitable_donation_form_after_user_fields', array( $this, 'add_password_field' ) );        

        do_action( 'charitable_donation_amount_form_start', $this );
    }

    /**
     * Return the donation form fields. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_fields() {
        return $this->get_donation_fields();
    }

    /**
     * Return the donation values. 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_donation_values() {
        $submitted = $this->get_submitted_values();

        $values = array(
            'campaign_id'   => $submitted[ 'campaign_id' ],
            'amount'        => self::get_donation_amount( $submitted )
        );

        return apply_filters( 'charitable_donation_amount_form_submission_values', $values, $submitted, $this );
    }

    /**
     * Save the submitted donation.
     *
     * @return  int|false   If successful, this returns the donation ID. If unsuccessful, returns false.
     * @access  public
     * @since   1.0.0
     */
    public function save_donation() {
        $campaign_id = charitable_get_current_campaign_id();

        if ( ! $campaign_id ) {
            return 0;
        }

        if ( ! $this->validate_nonce() ) {
            return 0;
        }        

        /* Set the donation amount */
        $campaign_id = $this->get_campaign()->ID;
        $amount = parent::get_donation_amount();

        if ( 0 == $amount && ! apply_filters( 'charitable_permit_empty_donations', false ) ) {
            charitable_get_notices()->add_error( __( 'No donation amount was set.', 'charitable' ) );
            return false;
        }

        /* Create or update the donation object in the session, with the current campaign ID. */
        charitable_get_session()->add_donation( $campaign_id, $amount );
        
        do_action( 'charitable_donation_amount_form_submit', $campaign_id, $amount );

        return true;        
    }

    /**
     * Redirect to payment form after submission. 
     *
     * @param   int     $campaign_id
     * @param   int     $amount
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function redirect_after_submission( $campaign_id, $amount ) {
        if ( defined('DOING_AJAX') && DOING_AJAX ) {
            return;
        }
        
        $redirect_url = charitable_get_permalink( 'campaign_donation_page', array( 'campaign_id' => $campaign_id ) );
        $redirect_url = apply_filters( 'charitable_donation_amount_form_redirect', $redirect_url, $campaign_id, $amount );
        
        wp_redirect( esc_url_raw( $redirect_url ) );

        die();
    }
}

endif; // End class_exists check