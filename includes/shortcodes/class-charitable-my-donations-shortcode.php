<?php
/**
 * My Donations shortcode class.
 * 
 * @version     1.0.0
 * @package     Charitable/Shortcodes/My Donations
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_My_Donations_Shortcode' ) ) : 

/**
 * Charitable_My_Donations_Shortcode class. 
 *
 * @since       1.0.0
 */
class Charitable_My_Donations_Shortcode {

    /**
     * The callback method for the campaigns shortcode.
     *
     * This receives the user-defined attributes and passes the logic off to the class. 
     *
     * @param   array   $atts   User-defined shortcode attributes.
     * @return  string
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function display( $atts ) {
        $args = shortcode_atts( $default, $atts, 'donations' );

        $view_args = array(
            'donations' => self::get_donations( $args )
        );

        ob_start();        

        charitable_template( 'shortcodes/my-donations.php', $view_args );

        return apply_filters( 'charitable_my_donations_shortcode', ob_get_clean(), $args );
    }

    /**
     * Return donations to display with the shortcode. 
     *
     * @param   array   $args
     * @return  WP_Query
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function get_donations( $args ) {
        
    }
}

endif;