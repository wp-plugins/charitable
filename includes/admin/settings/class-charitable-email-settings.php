<?php
/**
 * Charitable Email Settings UI.
 * 
 * @package     Charitable/Classes/Charitable_Email_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Email_Settings' ) ) : 

/**
 * Charitable_Email_Settings
 *
 * @final
 * @since      1.0.0
 */
final class Charitable_Email_Settings extends Charitable_Start_Object {

    /**
     * Create object instance. 
     *
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {
        add_filter( 'charitable_settings_tab_fields_emails', array( $this, 'add_email_fields' ), 5 );
        add_filter( 'charitable_settings_tab_fields', array( $this, 'add_individual_email_fields' ), 5 );
        add_filter( 'charitable_dynamic_groups', array( $this, 'add_email_settings_dynamic_groups' ) );
    }

    /**
     * Returns all the payment email settings fields.  
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_email_fields() {
        return array(
            'section' => array(
                'title'     => '',
                'type'      => 'hidden',
                'priority'  => 10000,
                'value'     => 'emails', 
                'save'      => false
            ),
            'section_emails' => array(
                'title'     => __( 'Available Emails', 'charitable' ),
                'type'      => 'heading',
                'priority'  => 5
            ), 
            'emails' => array(
                'title'     => false,
                'callback'  => array( $this, 'render_emails_table' ), 
                'priority'  => 7
            ), 
            'section_email_general' => array(
                'title'     => __( 'General Email Settings', 'charitable' ), 
                'type'      => 'heading', 
                'priority'  => 10
            ),
            'email_from_name' => array(
                'title'     => __( '"From" Name', 'charitable' ),
                'type'      => 'text',
                'help'      => __( 'The name of the email sender.', 'charitable' ), 
                'priority'  => 12, 
                'default'   => get_option( 'blogname' )
            ),
             'email_from_email' => array(
                'title'     => __( '"From" Email', 'charitable' ),
                'type'      => 'email',
                'help'      => __( 'The email address of the email sender. This will be the address recipients email if they hit "Reply".', 'charitable' ), 
                'priority'  => 14, 
                'default'   => get_option( 'admin_email' )
            ),
        );
    }

    /**
     * Add settings for each individual payment email. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_individual_email_fields( $fields ) {
        foreach ( charitable_get_helper( 'emails' )->get_available_emails() as $email ) {
            $fields[ 'emails_' . $email::ID ] = apply_filters( 'charitable_settings_fields_emails_email', array(), new $email );
        }

        return $fields;
    }

    /**
     * Add email keys to the settings groups. 
     *
     * @param   string[] $groups
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function add_email_settings_dynamic_groups( $groups ) {
        foreach ( charitable_get_helper( 'emails' )->get_available_emails() as $email_key => $email_class ) {
            if ( ! class_exists( $email_class ) ) {
                continue;
            }
                
            $groups[ 'emails_' . $email_key ] = apply_filters( 'charitable_settings_fields_emails_email', array(), new $email_class );
        }

        return $groups;
    }

    /**
     * Display table with emails.  
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function render_emails_table( $args ) {
        charitable_admin_view( 'settings/emails', $args );
    }

    /**
     * Checks whether we're looking at an individual email's settings page. 
     *
     * @return  boolean
     * @access  private
     * @since   1.0.0
     */
    private function is_individual_email_settings_page() {
        return isset( $_GET[ 'edit_email' ] );
    }

    /**
     * Returns the helper class of the email we're editing.
     *
     * @return  Charitable_Email|false
     * @access  private
     * @since   1.0.0
     */
    private function get_current_email_class() {
        $email = charitable_get_helper( 'emails' )->get_email( $_GET[ 'edit_email' ] );

        return $email ? new $email : false;
    }
}

endif; // End class_exists check