<?php
/**
 * Charitable Gateway Settings UI.
 * 
 * @package     Charitable/Classes/Charitable_Gateway_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Gateway_Settings' ) ) : 

/**
 * Charitable_Gateway_Settings
 *
 * @final
 * @since      1.0.0
 */
final class Charitable_Gateway_Settings extends Charitable_Start_Object {

    /**
     * Create object instance. 
     *
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {
        add_filter( 'charitable_settings_tab_fields_gateways', array( $this, 'add_gateway_fields' ), 5 );
        add_filter( 'charitable_settings_tab_fields', array( $this, 'add_individual_gateway_fields' ), 5 );
        add_filter( 'charitable_dynamic_groups', array( $this, 'add_gateway_settings_dynamic_groups' ) );
    }

    /**
     * Returns all the payment gateway settings fields.  
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_gateway_fields() {
        return array(
            'section' => array(
                'title'             => '',
                'type'              => 'hidden',
                'priority'          => 10000,
                'value'             => 'gateways', 
                'save'              => false
            ),
            'section_emails' => array(
                'title'             => __( 'Available Payment Gateways', 'charitable' ),
                'type'              => 'heading',
                'priority'          => 5
            ), 
            'gateways' => array(
                'title'             => false,
                'callback'          => array( $this, 'render_gateways_table' ), 
                'priority'          => 10
            ),
            'test_mode' => array(
                'title'             => __( 'Turn on Test Mode', 'charitable' ),
                'type'              => 'checkbox',
                'priority'          => 15
            )
        );
    }

    /**
     * Add settings for each individual payment gateway. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_individual_gateway_fields( $fields ) {
        foreach ( charitable_get_helper( 'gateways' )->get_active_gateways() as $gateway ) {
            if ( ! class_exists( $gateway ) ) {
                continue;
            }

            $fields[ 'gateways_' . $gateway::ID ] = apply_filters( 'charitable_settings_fields_gateways_gateway', array(), new $gateway );
        }

        return $fields;
    }

    /**
     * Add gateway keys to the settings groups. 
     *
     * @param   string[] $groups
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function add_gateway_settings_dynamic_groups( $groups ) {
        foreach ( charitable_get_helper( 'gateways' )->get_active_gateways() as $gateway_key => $gateway ) {
            if ( ! class_exists( $gateway ) ) {
                continue;
            }
                
            $groups[ 'gateways_' . $gateway_key ] = apply_filters( 'charitable_settings_fields_gateways_gateway', array(), new $gateway );
        }

        return $groups;
    }

    /**
     * Display table with available payment gateways.  
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function render_gateways_table( $args ) {
        charitable_admin_view( 'settings/gateways', $args );
    }
}

endif; // End class_exists check