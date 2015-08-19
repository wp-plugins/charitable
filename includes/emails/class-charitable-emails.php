<?php
/**
 * Class that sets up the emails. 
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Emails
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License   
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Emails' ) ) : 

/**
 * Charitable_Emails
 *
 * @since       1.0.0
 */
class Charitable_Emails extends Charitable_Start_Object {

    /**
     * All available emails. 
     *
     * @var     string[]
     * @access  private
     */
    private $emails;

    /**
     * @var     Charitable_Email The email currently being rendered.
     * @access  private
     */
    private $current_email;

    /**
     * Set up the class. 
     * 
     * Note that the only way to instantiate an object is with the charitable_start method, 
     * which can only be called during the start phase. In other words, don't try 
     * to instantiate this object. 
     *
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {                
        $this->attach_hooks_and_filters();
    }

    /**
     * Attach callbacks to hooks and filters.  
     *
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function attach_hooks_and_filters() {
        add_action( 'plugins_loaded', array( $this, 'register_emails' ), 500 );
        add_action( 'charitable_enable_email', array( $this, 'handle_email_settings_request' ) );
        add_action( 'charitable_disable_email', array( $this, 'handle_email_settings_request' ) );
        add_action( 'charitable_before_send_email', array( $this, 'set_current_email' ) );
        add_action( 'charitable_before_preview_email', array( $this, 'set_current_email' ) );
        add_filter( 'charitable_settings_fields_emails_email', array( $this, 'register_email_settings' ), 10, 2 );

        /* Hooks to send emails on certain actions. */
        add_action( 'charitable_after_add_donation', array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );
        add_action( 'charitable_after_update_donation', array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );
        add_action( 'charitable_after_add_donation', array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );
        add_action( 'charitable_after_update_donation', array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );

        /* Register email shortcode */
        add_shortcode( 'charitable_email', array( $this, 'email_shortcode' ) );
        
        /* 3rd party hook for overriding anything we've done above. */
        do_action( 'charitable_emails_start', $this );     
    }

    /**
     * Register Charitable emails. 
     *
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function register_emails() {
        $this->emails = apply_filters( 'charitable_emails', array(
            'new_donation' => 'Charitable_Email_New_Donation',
            'donation_receipt' => 'Charitable_Email_Donation_Receipt'
        ) );

        return $this->emails;
    }

    /**
     * Receives a request to enable or disable an email and validates it before passing it off.
     * 
     * @param   array
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function handle_email_settings_request() {
        if ( ! wp_verify_nonce( $_REQUEST[ '_nonce' ], 'email' ) ) {
            wp_die( __( 'Cheatin\' eh?!', 'charitable' ) );
        }

        $email = isset( $_REQUEST[ 'email_id' ] ) ? $_REQUEST[ 'email_id' ] : false;

        /* Gateway must be set */
        if ( false === $email ) {
            wp_die( __( 'Missing email.', 'charitable' ) );
        }       

        /* Validate email. */
        if ( ! isset( $this->emails[ $email ] ) ) {
            wp_die( __( 'Invalid email.', 'charitable' ) );
        }

        /* All good, so disable or enable the email */
        if ( 'charitable_disable_email' == current_filter() ) {
            $this->disable_email( $email );
        }
        else {
            $this->enable_email( $email );
        }   
    }

    /**
     * Returns all available emails. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_available_emails() {
        return $this->emails;
    }

    /**
     * Returns the currently enabled emails. 
     *
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function get_enabled_emails() {
        return charitable_get_option( 'enabled_emails', array() );
    }

    /**
     * Return the email class name for a given email.    
     *
     * @param   string  $email
     * @return  string|false    
     * @access  public
     * @since   1.0.0
     */
    public function get_email( $email ) {
        return isset( $this->emails[ $email ] ) ? $this->emails[ $email ] : false;
    }

    /**
     * Returns whether the passed email is enabled. 
     *
     * @param   string  $email_id
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function is_enabled_email( $email_id ) {      
        return array_key_exists( $email_id, $this->get_enabled_emails() );
    }

    /**
     * Register email settings fields.
     *
     * @param   array   $settings
     * @param   Charitable_Email  $email    The email's helper object.
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function register_email_settings( $settings, Charitable_Email $email ) {
        return $email->email_settings( $settings );
    }

    /**
     * Set the email currently being rendered/sent.
     *
     * This is executed before an email is prepared for send/preview. Setting
     * the email is required in order to allow the shortcode function (below)
     * to gather the correct information to display.
     *
     * @param   Charitable_Email $email
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function set_current_email( Charitable_Email $email ) {
        $this->current_email = $email;
    }

    /**
     * Handles the parsing of the [charitable_email] shortcode. 
     *
     * @param   array   $atts
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function email_shortcode( $atts = array() ) {
        $args = shortcode_atts( array( 'show' => '' ), $atts, 'charitable_email' );

        if ( ! isset( $args[ 'show' ] ) ) {
            return '';
        }

        return $this->current_email->get_value( $args[ 'show' ], $args );
    }

    /**
     * Enable an email. 
     *
     * @return  void
     * @access  protected
     * @since   1.0.0
     */
    protected function enable_email( $email ) {
        $settings = get_option( 'charitable_settings' );

        $enabled_emails = isset( $settings[ 'enabled_emails' ] ) ? $settings[ 'enabled_emails' ] : array();
        $enabled_emails[ $email ] = $this->emails[ $email ];
        $settings[ 'enabled_emails' ] = $enabled_emails;

        update_option( 'charitable_settings', $settings );

        do_action( 'charitable_email_enable', $email );
    }

    /**
     * Disable an email. 
     *
     * @return  void
     * @access  protected
     * @since   1.0.0
     */
    protected function disable_email( $email ) {
        $settings = get_option( 'charitable_settings' );

        if ( ! isset( $settings[ 'enabled_emails' ][ $email ] ) ) {
            return;
        }
        
        unset( $settings[ 'enabled_emails' ][ $email ] );

        update_option( 'charitable_settings', $settings );      

        do_action( 'charitable_email_disable', $email );
    }     
}

endif; // End class_exists check