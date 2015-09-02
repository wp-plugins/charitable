<?php
/**
 * Charitable widgets class. 
 *
 * Registers custom widgets for Charitable.
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Widgets
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Widgets' ) ) : 

/**
 * Charitable_Widgets
 *
 * @final
 * @since 		1.0.0
 */
final class Charitable_Widgets extends Charitable_Start_Object {

	/**
	 * Set up the class. 
	 * 
	 * Note that the only way to instantiate an object is with the on_start method, 
	 * which can only be called during the start phase. In other words, don't try 
	 * to instantiate this object. 
	 *
	 * @access 	protected
	 * @since 	1.0.0
	 */
	protected function __construct() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register widgets.
	 *
	 * @see 	widgets_init hook
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function register_widgets() {
		register_widget( 'Charitable_Campaign_Terms_Widget' );
		register_widget( 'Charitable_Campaigns_Widget' );
		register_widget( 'Charitable_Donors_Widget' );
		register_widget( 'Charitable_Donate_Widget' );
		register_widget( 'Charitable_Donation_Stats_Widget' );
	}
}

endif; // End class_exists check.