<?php
/**
 * Paypal Payment Gateway class
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Gateway_Paypal
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Gateway_Paypal' ) ) : 

/**
 * Paypal Payment Gateway 
 *
 * @since		1.0.0
 */
class Charitable_Gateway_Paypal extends Charitable_Gateway {
	
    /**
     * @var     string
     */
    CONST ID = 'paypal';

    /**
     * Instantiate the gateway class, defining its key values.
     *
     * @access  public
     * @since   1.0.0
     */
    public function __construct() {
        $this->name = apply_filters( 'charitable_gateway_paypal_name', __( 'PayPal', 'charitable' ) );

        $this->defaults = array(
            'label' => __( 'PayPal', 'charitable' )
        );
    }

    /**
     * Register gateway settings. 
     *
     * @param   array   $settings
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function gateway_settings( $settings ) {
        $settings[ 'paypal_email' ] = array(
            'type'      => 'email', 
            'title'     => __( 'PayPal Email Address', 'charitable' ), 
            'priority'  => 6, 
            'help'      => __( 'Enter the email address for the PayPal account that should receive donations.', 'charitable' )
        );

        $settings[ 'transaction_mode' ] = array(
            'type'      => 'radio',
            'title'     => __( 'PayPal Transaction Type', 'charitable' ), 
            'priority'  => 8,
            'options'   => array(
                'donations' => __( 'Donations', 'charitable' ),
                'standard'  => __( 'Standard Transaction', 'charitable' )
            ), 
            'default'   => 'donations',
            'help'      => sprintf( '%s<br /><a href="%s" target="_blank">%s</a>', 
                __( 'PayPal offers discounted fees to registered non-profit organizations. You must create a PayPal Business account to apply.', 'charitable' ), 
                'https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=merchant%2Fdonations',
                __( 'Find out more.', 'charitable' ) 
            )
        );

        return $settings;
    }

    /**
     * Validate the submitted credit card details.  
     *
     * @param   boolean $valid
     * @param   string $gateway
     * @param   mixed[] $values
     * @return  boolean
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function validate_donation( $valid, $gateway, $values ) {
        if ( 'paypal' != $gateway ) {
            return $valid;
        }

        $settings = charitable_get_option( 'gateways_paypal', array() );
        $email = trim( $settings[ 'paypal_email' ] );

        /* Make sure that the keys are set. */
        if ( empty( $email ) ) {

            charitable_get_notices()->add_error( __( 'Missing PayPal email address. Unable to proceed with payment.', 'charitable' ) );
            return false;

        }

        return $valid;
    }

	/**
     * Process the donation with the gateway, seamlessly over the Stripe API.
     *   
     * @param   int $donation_id
     * @param   Charitable_Donation_Processor $processor
     * @return  void
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function process_donation( $donation_id, $processor ) {
        $gateway = new Charitable_Gateway_Paypal();

        $user_data = $processor->get_donation_data_value( 'user' );
        $donation = new Charitable_Donation( $donation_id );
        $transaction_mode = $gateway->get_value( 'transaction_mode' );

        $paypal_args = apply_filters( 'charitable_paypal_redirect_args', array(
            'business'      => $gateway->get_value( 'paypal_email' ),
            'email'         => $user_data[ 'email' ],
            'first_name'    => $user_data[ 'first_name' ], 
            'last_name'     => $user_data[ 'last_name' ],
            'address1'      => $user_data[ 'address' ],
            'address2'      => $user_data[ 'address_2' ],
            'city'          => $user_data[ 'city' ],
            'country'       => $user_data[ 'country' ],
            'zip'           => $user_data[ 'postcode' ],
            'invoice'       => $processor->get_donation_data_value( 'donation_key' ),
            'amount'        => $donation->get_total_donation_amount(),
            'item_name'     => html_entity_decode( $donation->get_campaigns_donated_to(), ENT_COMPAT, 'UTF-8' ),
            'no_shipping'   => '1',
            'shipping'      => '0',
            'no_note'       => '1',
            'currency_code' => charitable_get_currency(),
            'charset'       => get_bloginfo( 'charset' ),
            'custom'        => $donation_id,
            'rm'            => '2',
            'return'        => charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $donation_id ) ),
            'cancel_return' => home_url(),
        //     'cancel_return' => edd_get_failed_transaction_uri( '?payment-id=' . $payment ),
            'notify_url'    => $processor->get_ipn_url( self::ID ),
            'cbt'           => get_bloginfo( 'name' ),
            'bn'            => 'Charitable_SP', 
            'cmd'           => $transaction_mode == 'donations' ? '_donations' : '_xclick'
        ), $donation_id, $processor );

        /**
         * Set up the PayPal redirect URL
         */
        $paypal_redirect = trailingslashit( $gateway->get_redirect_url() ) . '?';
        $paypal_redirect .= http_build_query( $paypal_args );
        $paypal_redirect = str_replace( '&amp;', '&', $paypal_redirect );

        /**
         * Redirect to PayPal
         */
        wp_redirect( $paypal_redirect );
        exit;
	}

    /**
     * Handle a call to our IPN listener.
     *
     * @return  string
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function process_ipn() {
        /* We only accept POST requests */
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] != 'POST' ) {
            return false;
        }

        $gateway = new Charitable_Gateway_Paypal();

        $data = $gateway->get_encoded_ipn_data();

        if ( empty( $data ) ) {
            return false;
        }

        if ( ! $gateway->paypal_ipn_verification( $data ) ) {
            return false;
        }

        $defaults = array(
            'txn_type' => '', 
            'payment_status' => ''
        );

        $data = wp_parse_args( $data, $defaults );

        $donation_id = isset( $data[ 'custom' ] ) ? absint( $data[ 'custom' ] ) : 0;

        if ( ! $donation_id ) {
            return false;
        }

        /**
         * By default, all transactions are handled by the web_accept handler. 
         * To handle other transaction types in a different way, use the 
         * 'charitable_paypal_{transaction_type}' hook.
         *
         * @see Charitable_Gateway_Paypal::process_web_accept()
         */
        $txn_type = strlen( $data[ 'txn_type' ] ) ? $data[ 'txn_type' ] : 'web_accept';
        
        if ( has_action( 'charitable_paypal_' . $txn_type ) ) {

            do_action( 'charitable_paypal_' . $txn_type, $data, $donation_id );

        }
        else {
            
            do_action( 'charitable_paypal_web_accept', $data, $donation_id );

        }

        exit;
    }

    /**
     * Receives verified IPN data from PayPal and processes the donation. 
     *
     * @return  void
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function process_web_accept( $data, $donation_id ) {
        if ( ! isset( $data[ 'invoice' ] ) ) {
            return;
        }

        $gateway        = new Charitable_Gateway_Paypal();
        $donation       = new Charitable_Donation( $donation_id );

        if ( 'paypal' != $donation->get_gateway() ) {
            return;
        }

        $donation_key   = $data[ 'invoice' ]; 
        $amount         = $data[ 'mc_gross' ];
        $payment_status = strtolower( $data['payment_status'] );
        $currency_code  = strtoupper( $data['mc_currency'] );
        $business_email = isset( $data[ 'business' ] ) && is_email( $data[ 'business' ] ) ? trim( $data[ 'business' ] ) : trim( $data[ 'receiver_email' ] );        

        /* Verify that the business email matches the PayPal email in the settings */
        if ( strcasecmp( $business_email, trim( $gateway->get_value( 'paypal_email' ) ) ) != 0 ) {
            
            $message = sprintf( '%s %s', __( 'Invalid Business email in the IPN response. IPN data:', 'charitable' ), json_encode( $data ) );
            Charitable_Donation::update_donation_log( $donation_id, $message );
            $donation->update_status( 'charitable-failed' );
            return;

        }

        /* Verify that the currency matches. */
        if ( $currency_code != charitable_get_currency() ) {

            $message = sprintf( '%s %s', __( 'The currency in the IPN response does not match the site currency. IPN data:', 'charitable' ), json_encode( $data ) );
            Charitable_Donation::update_donation_log( $donation_id, $message );
            $donation->update_status( 'charitable-failed' );
            return;

        } 

        /* Process a refunded donation. */
        if ( in_array( $payment_status, array( 'refunded', 'reversed' ) ) ) {

            /* It's a partial refund. */
            if ( $amount < $donation->get_total_donation_amount() ) {
                $message = sprintf( '%s: #%s', 
                    __( 'Partial PayPal refund processed', 'charitable' ), 
                    isset( $data[ 'parent_txn_id' ] ) ? $data[ 'parent_txn_id' ] : ''
                );
            }
            else {
                $message = sprintf( '%s #%s %s: %s', 
                    __( 'PayPal Payment', 'charitable' ), 
                    isset( $data[ 'parent_txn_id' ] ) ? $data[ 'parent_txn_id' ] : '',  
                    __( 'refunded with reason', 'charitable' ),
                    isset( $data[ 'reason_code' ] ) ? $data[ 'reason_code' ] : ''
                );
            }

            $donation->process_refund( $amount, $message );
            return;

        }

        /* Mark a payment as failed. */
        if ( in_array( $payment_status, array( 'declined', 'failed', 'denied', 'expired', 'voided' ) ) ) {

            $message = sprintf( '%s: %s', __( 'The donation has failed with the following status', 'charitable' ), $payment_status );
            Charitable_Donation::update_donation_log( $donation_id, $message );
            $donation->update_status( 'charitable-failed' );
            return;

        }

        /* If we have already processed this donation, stop here. */
        if ( 'charitable-completed' == get_post_status( $donation_id ) ) {
            return; 
        }

        /* Verify that the donation key matches the one stored for the donation. */
        if ( $donation_key != $donation->get_donation_key() ) {
                    
            $message = sprintf( '%s %s', __( 'Donation key in the IPN response does not match the donation. IPN data:', 'charitable' ), json_encode( $data ) );
            Charitable_Donation::update_donation_log( $donation_id, $message );
            $donation->update_status( 'charitable-failed' );
            return;

        }

        /* Verify that the amount in the IPN matches the amount we expected. */
        if ( $amount < $donation->get_total_donation_amount() ) {

            $message = sprintf( '%s %s', __( 'The amount in the IPN response does not match the expected donation amount. IPN data:', 'charitable' ), json_encode( $data ) );
            Charitable_Donation::update_donation_log( $donation_id, $message );
            $donation->update_status( 'charitable-failed' );
            return;

        }

        /* Process a completed donation. */
        if ( 'completed' == $payment_status ) {

            $message = sprintf( '%s: %s', __( 'PayPal Transaction ID', 'charitable' ), $data[ 'txn_id' ] );
            Charitable_Donation::update_donation_log( $donation_id, $message );
            $donation->update_status( 'charitable-completed' );
            return;

        }

        /* If the donation is set to pending but has a pending_reason provided, save that to the log. */
        if ( 'pending' == $payment_status ) {

            if ( isset( $data['pending_reason'] ) ) {

                $message = $gateway->get_pending_reason_note( strtolower( $data[ 'pending_reason' ] ) );
                Charitable_Donation::update_donation_log( $donation_id, $message );
            
            }

            $donation->update_status( 'charitable-pending' );

        }
    }

    /**
     * Return the posted IPN data. 
     *
     * @return  mixed[]
     * @access  public
     * @since   1.0.0
     */
    public function get_encoded_ipn_data() {
        $post_data = "";        

        /* Fallback just in case post_max_size is lower than needed. */
        if ( ini_get( 'allow_url_fopen' ) ) {
            $post_data = file_get_contents( 'php://input' );
        }
        else {
            ini_set( 'post_max_size', '12M' );
        }

        if ( strlen( $post_data ) ) {
            $arg_separator = ini_get( 'arg_separator.output' );
            $data_string = 'cmd=_notify-validate' . $arg_separator . $post_data;

            /* Convert collected post data to an array */
            parse_str( $data_string, $data );

            return $data;
        }

        /* Return an empty array if there are no POST variables. */
        if ( empty( $_POST ) ) {
            return array();
        }

        $data = array(
            'cmd' => '_notify-validate'
        );

        foreach ( $_POST as $key => $value ) {
            $data[ $key ] = urlencode( $value );
        }

        return $data;
    }

    /**
     * Validates an IPN request with PayPal.    
     *
     * @param   mixed[] $data
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function paypal_ipn_verification( $data ) {
        $remote_post_vars = array(
            'method'           => 'POST',
            'timeout'          => 45,
            'redirection'      => 5,
            'httpversion'      => '1.1',
            'blocking'         => true,
            'headers'          => array(
                'host'         => 'www.paypal.com',
                'connection'   => 'close',
                'content-type' => 'application/x-www-form-urlencoded',
                'post'         => '/cgi-bin/webscr HTTP/1.1',

            ),
            'sslverify'        => false,
            'body'             => $data
        );

        /* Get response */
        $api_response = wp_remote_post( $this->get_redirect_url(), $remote_post_vars );

        $is_valid = ! is_wp_error( $api_response ) && 'VERIFIED' == $api_response['body'];

        return apply_filters( 'charitable_paypal_ipn_verification', $is_valid, $api_response );
    }

    /**
     * Return a note to log for a pending payment.  
     *
     * @param   string $reason_code
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_pending_reason_note( $reason_code ) {
        switch ( $reason_code ) {
            case 'echeck' :
                $note = __( 'Payment made via eCheck and will clear automatically in 5-8 days', 'charitable' );
                break;

            case 'address' :
                $note = __( 'Payment requires a confirmed customer address and must be accepted manually through PayPal', 'charitable' );
                break;

            case 'intl' :
                $note = __( 'Payment must be accepted manually through PayPal due to international account regulations', 'charitable' );
                break;

            case 'multi-currency' :
                $note = __( 'Payment received in non-shop currency and must be accepted manually through PayPal', 'charitable' );
                break;

            case 'paymentreview' :
            case 'regulatory_review' :
                $note = __( 'Payment is being reviewed by PayPal staff as high-risk or in possible violation of government regulations', 'charitable' );
                break;

            case 'unilateral' :
                $note = __( 'Payment was sent to non-confirmed or non-registered email address.', 'charitable' );
                break;

            case 'upgrade' :
                $note = __( 'PayPal account must be upgraded before this payment can be accepted', 'charitable' );
                break;

            case 'verify' :
                $note = __( 'PayPal account is not verified. Verify account in order to accept this payment', 'charitable' );
                break;

            case 'other' :
                $note = __( 'Payment is pending for unknown reasons. Contact PayPal support for assistance', 'charitable' );
                break;
        }

        return apply_filters( 'charitable_paypal_gateway_pending_reason_note', $note, $reason_code );
    }

    /**
     * Return the base of the PayPal
     *
     * @param   bool $ssl_check
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_redirect_url( $ssl_check = false ) {
        $protocol = is_ssl() || ! $ssl_check ? 'https://' : 'http://';
        
        if ( charitable_get_option( 'test_mode' ) ) {

            $paypal_uri = $protocol . 'www.sandbox.paypal.com/cgi-bin/webscr';

        } 
        else {
        
            $paypal_uri = $protocol . 'www.paypal.com/cgi-bin/webscr';

        }

        return apply_filters( 'charitable_paypal_uri', $paypal_uri );
    }

    /**
     * Returns the current gateway's ID.  
     *
     * @return  string
     * @access  public
     * @static
     * @since   1.0.3
     */
    public static function get_gateway_id() {
        return self::ID;
    }
}

endif; // End class_exists check