<?php 
/**
 * Charitable Public class. 
 *
 * @package 	Charitable/Classes/Charitable_Public
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Public' ) ) : 

/**
 * Charitable Public class. 
 *
 * @final
 * @since 	    1.0.0
 */
final class Charitable_Public extends Charitable_Start_Object {

	/**
	 * Set up the class. 
	 * 
	 * Note that the only way to instantiate an object is with the start method, 
	 * which can only be called during the start phase. In other words, don't try 
	 * to instantiate this object. 
	 *
	 * @access 	protected
	 * @since 	1.0.0
	 */
	protected function __construct() {		
		$this->attach_hooks_and_filters();

		do_action( 'charitable_public_start', $this );
	}

	/**
	 * Sets up hook and filter callback functions for public facing functionality.
	 * 
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function attach_hooks_and_filters() {
		add_action( 'charitable_start', array( 'Charitable_Session', 'charitable_start' ), 5 );
		add_action( 'charitable_start', array( 'Charitable_Templates', 'charitable_start' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts') );
		add_filter( 'post_class', array( $this, 'campaign_post_class' ) );
	}

	/**
	 * Loads public facing scripts and stylesheets. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function wp_enqueue_scripts() {						
		$vars = apply_filters( 'charitable_javascript_vars', array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );

		wp_register_script( 'charitable-script', charitable()->get_path( 'assets', false ) . 'js/charitable.js', array( 'jquery' ), charitable()->get_version() );
        wp_localize_script( 'charitable-script', 'CHARITABLE_VARS', $vars );
        wp_enqueue_script( 'charitable-script' );

		wp_register_style( 'charitable-styles', charitable()->get_path( 'assets', false ) . 'css/charitable.css', array(), charitable()->get_version() );
		wp_enqueue_style( 'charitable-styles' );

		/* Lean Modal is registered but NOT enqueued yet. */
		if ( 'modal' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
			wp_register_script( 'lean-modal', charitable()->get_path( 'assets', false ) . 'js/libraries/jquery.leanModal.js', array( 'jquery' ), charitable()->get_version() );
			wp_register_style( 'lean-modal-css', charitable()->get_path( 'assets', false ) . 'css/modal.css', array(), charitable()->get_version() );
		}
	}

    /**
     * Adds custom post classes when viewing campaign. 
     *
     * @return  string[] 
     * @access  public
     * @since   1.0.0
     */
    public function campaign_post_class( $classes ) {
        $campaign = charitable_get_current_campaign();

        if ( ! $campaign ) {
        	return $classes;
        }

        $classes[] = $campaign->has_goal() ? 'campaign-has-goal' : 'campaign-has-no-goal';
        $classes[] = $campaign->is_endless() ? 'campaign-is-endless' : 'campaign-has-end-date';
        return $classes;
    }

}

endif;