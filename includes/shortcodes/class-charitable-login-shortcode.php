<?php
/**
 * Login shortcode class.
 * 
 * @version     1.0.0
 * @package     Charitable/Shortcodes/Login
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Login_Shortcode' ) ) : 

/**
 * Charitable_Login_Shortcode class. 
 *
 * @since       1.0.0
 */
class Charitable_Login_Shortcode {

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
        global $wp;        

        $defaults = array(
            'logged_in_message' => __( 'You are already logged in!', 'charitable' )
        );

        $args = shortcode_atts( $defaults, $atts, 'charitable_login' );     

        ob_start();

        if ( is_user_logged_in() ) {

            charitable_template( 'shortcodes/logged-in.php', $args );
            
            return ob_get_clean();
        }

        $args[ 'login_form_args' ] = self::get_login_form_args();

        charitable_template( 'shortcodes/login.php', $args );

        return apply_filters( 'charitable_login_shortcode', ob_get_clean() );        
    }

    /**
     * Return donations to display with the shortcode. 
     *
     * @return  mixed[] $args
     * @access  protected
     * @static
     * @since   1.0.0
     */
    protected static function get_login_form_args() {
        return apply_filters( 'charitable_login_form_args', array(
            'redirect' => esc_url( charitable_get_login_redirect_url() )
        ) );
    }
}

endif;