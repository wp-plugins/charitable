<?php
/**
 * Gateway abstract model 
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Gateway
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Gateway' ) ) : 

/**
 * Charitable_Gateway
 *
 * @abstract
 * @since		1.0.0
 */
abstract class Charitable_Gateway {	
	
    /**
     * @var     string  The gateway's unique identifier.
     */
    const ID = '';

    /**
     * @var     string  Name of the payment gateway.
     * @access  protected
     * @since   1.0.0
     */
    protected $name;

    /**
     * @var     array   The default values for all settings added by the gateway.
     * @access  protected
     * @since   1.0.0
     */
    protected $defaults;

    /**
     * @var     boolean  Flags whether the gateway requires credit card fields added to the donation form.
     * @access  protected
     * @since   1.0.0
     */
    protected $credit_card_form = false;

    /**
     * Return the gateway name.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_name() {
        return $this->name;
    }
    
    /**
     * Returns the default gateway label to be displayed to donors. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_default_label() {
        return isset( $this->defaults[ 'label' ] ) ? $this->defaults[ 'label' ] : $this->get_name();
    }

    /**
     * Provide default gateway settings fields.
     *
     * @param   array   $settings
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function default_gateway_settings( $settings ) {
        return array(
            'section_gateway' => array(
                'type'      => 'heading',
                'title'     => $this->get_name(),
                'priority'  => 2
            ),
            'label' => array(
                'type'      => 'text',
                'title'     => __( 'Gateway Label', 'charitable' ), 
                'help'      => __( 'The label that will be shown to donors on the donation form.', 'charitable' ), 
                'priority'  => 4,
                'default'   => $this->get_default_label()
            )
        );
    }

    /**
     * Return the settings for this gateway. 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_settings() {
        return charitable_get_option( 'gateways_' . $this->get_gateway_id(), array() );
    }

    /**
     * Retrieve the gateway label. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_label() {
        return charitable_get_option( 'label', $this->get_default_label(), $this->get_settings() );
    }

    /**
     * Return the value for a particular gateway setting. 
     *
     * @param   string  $setting
     * @return  mixed
     * @access  public
     * @since   1.0.0
     */
    public function get_value( $setting ) {
        $default = isset( $this->defaults[ $setting ] ) ? $this->defaults[ $setting ] : '';
        return charitable_get_option( $setting, $default, $this->get_settings() );
    }

    /**
     * Returns whether a credit card form is required for this gateway. 
     *
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function requires_credit_card_form() {
        return $this->credit_card_form;
    }

    /**
     * Returns an array of credit card fields.
     *
     * If the gateway requires different fields, this can simply be redefined
     * in the child class.  
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_credit_card_fields() {
        return apply_filters( 'charitable_credit_card_fields', array(
            'cc_name' => array(
                'label'     => __( 'Name on Card', 'charitable' ),
                'type'      => 'text',
                'required'  => true,
                'priority'  => 2,
                'data_type' => 'gateway'
            ),
            'cc_number' => array(
                'label'     => __( 'Card Number', 'charitable' ),
                'type'      => 'text',
                'required'  => true,
                'priority'  => 4,
                'pattern'   => '[0-9]{13,16}',
                'data_type' => 'gateway'
            ),
            'cc_cvc' => array(
                'label'     => __( 'CVV Number', 'charitable' ),
                'type'      => 'text',
                'required'  => true,
                'priority'  => 6,
                'data_type' => 'gateway'
            ),
            'cc_expiration' => array(
                'label'     => __( 'Expiration', 'charitable' ),
                'type'      => 'cc-expiration',
                'required'  => true,
                'priority'  => 8,
                'data_type' => 'gateway'
            )
        ), $this );
    }

    /**
     * Returns the current gateway's ID.  
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_gateway_id() {
        $class = get_called_class();
        return $class::ID;
    }

    /**
     * Register gateway settings. 
     *
     * @param   array   $settings
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    abstract public function gateway_settings( $settings );
}

endif; // End class_exists check