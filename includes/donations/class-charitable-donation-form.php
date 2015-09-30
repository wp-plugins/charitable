<?php
/**
 * Donation form model class.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Donation_Form
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation_Form' ) ) : 

/**
 * Charitable_Donation_Form
 *
 * @since       1.0.0
 */
class Charitable_Donation_Form extends Charitable_Form implements Charitable_Donation_Form_Interface {

    /** 
     * @var     Charitable_Campaign
     */
    protected $campaign;

    /** 
     * @var     Charitable_User
     */
    protected $user;    

    /**
     * @var     array
     */
    protected $form_fields;

    /**
     * @var     string
     */
    protected $nonce_action = 'charitable_donation';

    /**
     * @var     string
     */
    protected $nonce_name = '_charitable_donation_nonce';

    /**
     * Action to be executed upon form submission. 
     *
     * @var     string
     * @access  protected
     */
    protected $form_action = 'make_donation';

    /**
     * Create a donation form object.
     *
     * @param   Charitable_Campaign $campaign
     * @access  public
     * @since   1.0.0
     */
    public function __construct( Charitable_Campaign $campaign ) {
        $this->campaign = $campaign;
        $this->id = uniqid();   

        $this->attach_hooks_and_filters();  
    }

    /**
     * Set up callbacks for actions and filters. 
     *
     * @return  void
     * @access  protected
     * @since   1.0.0
     */
    protected function attach_hooks_and_filters() {
        parent::attach_hooks_and_filters();
        
        add_filter( 'charitable_form_field_template', array( $this, 'use_custom_templates' ), 10, 2 );
        add_filter( 'charitable_donation_form_gateway_fields', array( $this, 'add_credit_card_fields' ), 10, 2 );        
        add_action( 'charitable_donation_form_after_user_fields', array( $this, 'add_password_field' ) );        

        $this->setup_payment_fields();
    }

    /**
     * Returns the campaign associated with this donation form object. 
     *
     * @return  Charitable_Campaign
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign() {
        return $this->campaign;
    }

    /**
     * Return the current user. 
     *
     * @return  Charitable_User|false   Object if the user is logged in. False otherwise.
     * @access  public
     * @since   1.0.0
     */
    public function get_user() {
        if ( ! isset( $this->user ) ) {
            $user = wp_get_current_user();
            $this->user = $user->ID ? new Charitable_User( $user ) : false;
        }

        return $this->user;
    }

    /**
     * Returns the set value for a particular user field. 
     *
     * @param   string  $key
     * @return  mixed
     * @access  public
     * @since   1.0.0
     */
    public function get_user_value( $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            return $_POST[ $key ];
        }

        if ( ! $this->get_user() ) {
            return '';
        }

        return $this->get_user()->get( $key );
    }

    /**
     * Returns the fields related to the person making the donation. 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_user_fields() {
        $user_fields = apply_filters( 'charitable_donation_form_user_fields', array(
            'first_name' => array( 
                'label'     => __( 'First name', 'charitable' ), 
                'type'      => 'text', 
                'priority'  => 4, 
                'value'     => $this->get_user_value( 'first_name' ), 
                'required'  => true,                
                'requires_registration' => false,
                'data_type' => 'user'
            ),
            'last_name' => array( 
                'label'     => __( 'Last name', 'charitable' ),                 
                'type'      => 'text', 
                'priority'  => 6, 
                'value'     => $this->get_user_value( 'last_name' ), 
                'required'  => true,                
                'requires_registration' => false,
                'data_type' => 'user'
            ),
            'email' => array(
                'label'     => __( 'Email', 'charitable' ), 
                'type'      => 'email',
                'required'  => true, 
                'priority'  => 8,
                'value'     => $this->get_user_value( 'user_email' ), 
                'requires_registration' => false,
                'data_type' => 'user'
            ),
            'address' => array( 
                'label'     => __( 'Address', 'charitable' ),               
                'type'      => 'text', 
                'priority'  => 10, 
                'value'     => $this->get_user_value( 'donor_address' ), 
                'required'  => false, 
                'requires_registration' => true,
                'data_type' => 'user'
            ),
            'address_2' => array( 
                'label'     => __( 'Address 2', 'charitable' ), 
                'type'      => 'text', 
                'priority'  => 12, 
                'value'     => $this->get_user_value( 'donor_address_2' ), 
                'required'  => false,
                'requires_registration' => true,
                'data_type' => 'user'
            ),
            'city' => array( 
                'label'     => __( 'City', 'charitable' ),          
                'type'      => 'text', 
                'priority'  => 14, 
                'value'     => $this->get_user_value( 'donor_city' ), 
                'required'  => false,
                'requires_registration' => true, 
                'data_type' => 'user'
            ),
            'state' => array( 
                'label'     => __( 'State', 'charitable' ),                 
                'type'      => 'text', 
                'priority'  => 16, 
                'value'     => $this->get_user_value( 'donor_state' ), 
                'required'  => false,
                'requires_registration' => true,
                'data_type' => 'user'
            ),
            'postcode' => array( 
                'label'     => __( 'Postcode / ZIP code', 'charitable' ),               
                'type'      => 'text', 
                'priority'  => 18, 
                'value'     => $this->get_user_value( 'donor_postcode' ), 
                'required'  => false,
                'requires_registration' => true,
                'data_type' => 'user'
            ),
            'country' => array( 
                'label'     => __( 'Country', 'charitable' ),               
                'type'      => 'select', 
                'options'   => charitable_get_location_helper()->get_countries(), 
                'priority'  => 20, 
                'value'     => $this->get_user_value( 'donor_country' ), 
                'required'  => false,
                'requires_registration' => true,
                'data_type' => 'user'
            ),
            'phone' => array( 
                'label'     => __( 'Phone', 'charitable' ),                 
                'type'      => 'text', 
                'priority'  => 22, 
                'value'     => $this->get_user_value( 'donor_phone' ), 
                'required'  => false,
                'requires_registration' => true,
                'data_type' => 'user'
            )
        ), $this );
        
        uasort( $user_fields, 'charitable_priority_sort' );

        return $user_fields;
    }

    /**
     * Return fields used for account creation. 
     *
     * By default, this just returns the password field. You can include a username
     * field with ... 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_user_account_fields() {
        $account_fields = array(
            'user_pass' => array(
                'label'     => __( 'Password', 'charitable' ), 
                'type'      => 'password', 
                'priority'  => 4, 
                'required'  => true,
                'requires_registration' => true,
                'data_type' => 'user'
            )
        );

        if ( apply_filters( 'charitable_donor_usernames', false ) ) {
            $account_fields['user_login'] = array(
                'label'     => __( 'Username', 'charitable' ), 
                'type'      => 'text', 
                'priority'  => 2,
                'required'  => true,
                'requires_registration' => true,
                'data_type' => 'user'
            );
        }

        return $account_fields;
    }

    /**
     * Returns the donation fields. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_donation_fields() {
        $donation_fields = apply_filters( 'charitable_donation_form_donation_fields', array(
            'donation_amount' => array( 
                'type'      => 'donation-amount', 
                'priority'  => 4,
                'required'  => false
            )
        ), $this );

        uasort( $donation_fields, 'charitable_priority_sort' );

        return $donation_fields;
    }

    /**
     * Return the donation form fields. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_fields() {
        $fields = apply_filters( 'charitable_donation_form_fields', array(
            'donation_fields' => array(
                'legend'        => __( 'Your Donation', 'charitable' ), 
                'type'          => 'fieldset', 
                'fields'        => $this->get_donation_fields(),                
                'priority'      => 20
            ), 
            'user_fields' => array(
                'legend'        => __( 'Your Details', 'charitable' ), 
                'type'          => 'donor-fields',
                'fields'        => $this->get_user_fields(),
                'class'         => 'charitable-fieldset',
                'priority'      => 40
            )
        ), $this );             

        uasort( $fields, 'charitable_priority_sort' );

        return $fields;
    }

    /**
     * Add payment fields to the donation form if necessary. 
     *
     * @param   array[] $fields
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_payment_fields( $fields ) {
        $gateways_helper = charitable_get_helper( 'gateways' );
        $active_gateways = $gateways_helper->get_active_gateways();
        $default_gateway = $gateways_helper->get_default_gateway();        
        
        $gateways = array();
        $has_gateway_fields = false;

        foreach ( $gateways_helper->get_active_gateways() as $gateway_id => $gateway_class ) {
            $gateway = new $gateway_class;
            $gateway_fields = apply_filters( 'charitable_donation_form_gateway_fields', array(), $gateway );
            $gateways[ $gateway_id ] = array(
                'label'     => $gateway->get_label(),
                'fields'    => $gateway_fields
            );

            $has_gateway_fields = $has_gateway_fields || ! empty( $gateway_fields );

        }

        /* Add the payment section if there are gateway fields to be filled out. */
        if ( $has_gateway_fields || count( $gateways ) > 1 ) {

            $fields[ 'payment_fields' ] = array(
                'type'      => 'gateway-fields',
                'legend'    => __( 'Payment', 'charitable' ), 
                'default'   => $default_gateway, 
                'gateways'  => $gateways, 
                'priority'  => 60
            );

        }

        return $fields;
    }

    /**
     * Use custom template for some form fields.
     *
     * @param   string|false $custom_template
     * @param   array   $field
     * @return  string|false|Charitable_Template
     * @access  public
     * @since   1.0.0
     */
    public function use_custom_templates( $custom_template, $field ) {
        $donation_form_templates = array( 'donation-amount', 'donor-fields', 'gateway-fields', 'cc-expiration' );

        if ( in_array( $field[ 'type' ], $donation_form_templates ) ) {

            $template_name = 'donation-form/' . $field[ 'type' ] . '.php';
            $custom_template = new Charitable_Template( $template_name, false );

        }

        return $custom_template;
    }

    /**
     * Add credit card fields to the donation form if this gateway requires it. 
     *
     * @param   array[] $fields
     * @param   Charitable_Gateway $gateway
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_credit_card_fields( $fields, Charitable_Gateway $gateway ) {
        if ( $gateway->requires_credit_card_form() ) {
            $fields = array_merge( $fields, $gateway->get_credit_card_fields() );
        }

        return $fields;
    }

    /**
     * Render the donation form. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function render() {
        charitable_template( 'donation-form/form-donation.php', array( 
            'campaign' => $this->get_campaign(), 
            'form' => $this 
        ) );
    }

    /**
     * Adds hidden fields to the start of the donation form.    
     *
     * @param   Charitable_Donation_Form $form
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function add_hidden_fields( $form ) {    
        if ( false === parent::add_hidden_fields( $form ) ) {
            return false;
        }   

        $hidden_fields = apply_filters( 'charitable_donation_form_hidden_fields', array(
            'campaign_id' => $this->campaign->ID
        ) );

        foreach ( $hidden_fields as $name => $value  ) {
            printf( '<input type="hidden" name="%s" value="%s" />', $name, $value );
        }
    }

    /**
     * Set the gateway as a hidden field when there is only one gateway. 
     *
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function add_hidden_gateway_field( $fields ) {        
        $gateways = charitable_get_helper( 'gateways' )->get_active_gateways();

        if ( count( $gateways ) !== 1 ) {
            return $fields;
        }

        $gateway_keys = array_keys( $gateways );

        $fields[ 'gateway' ] = $gateway_keys[ 0 ];

        return $fields;
    }

    /**
     * Add a password field to the end of the form.  
     *
     * @param   Charitable_Donation_Form $form
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function add_password_field( $form ) {
        if ( ! $form->is_current_form( $this->id ) ) {
            return;
        }

        /**
         * Make sure we are not logged in.
         */
        if ( 0 !== wp_get_current_user()->ID ) {
            return;
        }

        charitable_template( 'donation-form/user-login-fields.php' );
    }

    /**
     * Validate the form submission. 
     *
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function validate_submission() {
        if ( ! $this->validate_nonce() ) {
            return false;
        }

        $has_required_fields = $this->check_required_fields( $this->get_merged_fields() );

        if ( ! $has_required_fields ) {
            return false;
        }

        if ( self::get_donation_amount() <= 0 && ! apply_filters( 'charitable_permit_0_donation', false ) ) {
            charitable_get_notices()->add_error( sprintf( __( 'You must donate more than %s.', 'charitable' ), charitable_format_money( '0' ) ) );
            return false;
        }

        return apply_filters( 'charitable_validate_donation_form_submission', true, $this );
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
            'user_id'   => get_current_user_id(),
            'gateway'   => $submitted[ 'gateway' ],
            'campaigns' => array(
                array(
                    'campaign_id'   => $submitted[ 'campaign_id' ],
                    'amount'        => self::get_donation_amount()
                )
            )
        );

        foreach ( $this->get_merged_fields() as $key => $field ) {

            if ( isset( $field[ 'data_type' ] ) || 'gateways' == $key ) {

                if ( 'gateways' == $key ) {
                    
                    foreach ( $field as $gateway_id => $gateway_fields ) {

                        foreach ( $gateway_fields as $key => $field ) {

                            if ( ! isset( $field[ 'type' ] ) ) {
                                continue;
                            }

                            $field_type = $field[ 'type' ];
                            $default = $field_type == 'checkbox' ? false : '';
                            $values[ 'gateways' ][ $gateway_id ][ $key ] = isset( $submitted[ $key ] ) ? $submitted[ $key ] : $default;
                        }
                    }
                }
                elseif ( isset( $field[ 'type' ] ) ) {

                    $data_type = $field[ 'data_type' ];
                    $field_type = $field[ 'type' ];
                    $default = $field_type == 'checkbox' ? false : '';
                    $values[ $data_type ][ $key ] = isset( $submitted[ $key ] ) ? $submitted[ $key ] : $default;

                }
                 
            }

        }

        return apply_filters( 'charitable_donation_form_submission_values', $values, $submitted, $this );
    }

    /**
     * Returns all fields as a merged array. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_merged_fields() {    
        $fields = array();

        foreach ( $this->get_fields() as $section_id => $section ) {

            if ( 'payment_fields' == $section_id ) {
                $section_fields = array();
                foreach ( $section[ 'gateways' ] as $gateway_id => $gateway_section ) {
                    if ( isset( $gateway_section[ 'fields' ] ) ) {
                        $section_fields[ 'gateways' ][ $gateway_id ] = $gateway_section[ 'fields' ];
                    }
                }
            }
            else {
                $section_fields = isset( $section[ 'fields' ] ) ? $section[ 'fields' ] : $section;
            }

            $fields = array_merge( $fields, $section_fields );            
        }

        return $fields;
    }
    
    /**
     * Return the donation amount.  
     *
     * @return  float
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function get_donation_amount() {
        $suggested  = isset( $_POST[ 'donation_amount' ] ) ? $_POST[ 'donation_amount' ] : 0;
        $custom     = isset( $_POST[ 'custom_donation_amount' ] ) ? $_POST[ 'custom_donation_amount' ] : 0;

        if ( 0 === $suggested || 'custom' === $suggested ) {

            $amount = $custom;

        } 
        else {

            $amount = $suggested;

        }

        return $amount;
    }

    /**
     * Set up payment fields based on the gateways that are installed and which one is default. 
     *
     * @return  void
     * @access  protected
     * @since   1.0.0
     */
    protected function setup_payment_fields() {
        $active_gateways = charitable_get_helper( 'gateways' )->get_active_gateways();
        $has_gateways = apply_filters( 'charitable_has_active_gateways', ! empty( $active_gateways ) );

        /* If no gateways have been selected, display a notice and return the fields */
        if ( ! $has_gateways ) {  

            charitable_get_notices()->add_error( $this->get_no_active_gateways_notice() );
            return;

        }

        if ( count( $active_gateways ) == 1 ) {

            add_filter( 'charitable_donation_form_hidden_fields', array( $this, 'add_hidden_gateway_field' ) );
            
        }
        
        add_action( 'charitable_donation_form_fields', array( $this, 'add_payment_fields' ) );
    }   

    /**
     * A formatted notice to advise that there are no gateways active.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_no_active_gateways_notice() {
        $message = __( 'There are no active payment gateways.', 'charitable' );

        if ( current_user_can( 'manage_charitable_settings' ) ) {
            $message = sprintf( '%s <a href="%s">%s</a>.', 
                $message, 
                admin_url( 'admin.php?page=charitable-settings&tab=gateways' ), 
                __( 'Enable one now', 'charitable' ) 
            ); 
        }

        return apply_filters( 'charitable_no_active_gateways_notice', $message, current_user_can( 'manage_charitable_settings' ) );
    }


    /**
     * Return the donor value fields. 
     *
     * @return  string[]
     * @access  protected
     * @since   1.0.0
     */
    protected function get_donor_value_fields( $submitted ) {
        $donor_fields = array();

        if ( isset( $submitted[ 'first_name' ] ) ) {
            $donor_fields[ 'first_name' ] = $submitted[ 'first_name' ];
        }

        if ( isset( $submitted[ 'last_name' ] ) ) {
            $donor_fields[ 'last_name' ] = $submitted[ 'last_name' ];
        }

        if ( isset( $submitted[ 'user_email' ] ) ) {
            $donor_fields[ 'email' ] = $submitted[ 'user_email' ];
        }

        return $donor_fields;
    }

    /**
     * Checks whether the form submission contains profile fields.  
     *
     * @return  boolean
     * @access  protected
     * @since   1.0.0
     */
    protected function has_profile_fields( $submitted, $user_fields ) {
        foreach ( $user_fields as $key => $field ) {
            if ( $field[ 'requires_registration' ] && isset( $submitted[ $key ] ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if required fields are missing. 
     *
     * @param   array   $required_fields
     * @return  boolean
     * @access  protected
     * @since   1.0.0
     */
    protected function is_missing_required_fields( $required_fields ) {
        if ( is_user_logged_in() ) {
            return false;
        }

        if ( is_null( $this->get_submitted_value( 'gateway' ) ) ) {
            
            charitable_get_notices()->add_error( sprintf( '<p>%s</p>', 
                __( 'Your donation could not be processed. No payment gateway was selected.', 'charitable' )
            ) );

            return false;
        }

        return ! $this->check_required_fields( $required_fields );
    }
}

endif; // End class_exists check