<?php
/**
 * Charitable Advanced Settings UI.
 * 
 * @package     Charitable/Classes/Charitable_Advanced_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Advanced_Settings' ) ) : 

/**
 * Charitable_Advanced_Settings
 *
 * @final
 * @since      1.0.0
 */
final class Charitable_Advanced_Settings extends Charitable_Start_Object {

    /**
     * Create object instance. 
     *
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {
        add_filter( 'charitable_settings_tab_fields_advanced', array( $this, 'add_advanced_fields' ), 5 );
    }

    /**
     * Add the advanced tab settings fields. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function add_advanced_fields() {
        return array(
            'section'               => array(
                'title'             => '',
                'type'              => 'hidden',
                'priority'          => 10000,
                'value'             => 'advanced'
            ),            
            'section_dangerous'     => array(
                'title'             => __( 'Dangerous Settings', 'charitable' ), 
                'type'              => 'heading', 
                'priority'          => 100
            ),
            'delete_data_on_uninstall'  => array(
                'label_for'         => __( 'Reset Data', 'charitable' ), 
                'type'              => 'checkbox', 
                'help'              => __( 'DELETE ALL DATA when uninstalling the plugin.', 'charitable' ), 
                'priority'          => 105
            )
        );
    }
}

endif; // End class_exists check