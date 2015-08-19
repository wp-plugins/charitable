<?php
/**
 * Contains the class that is used to register and retrieve notices like errors, warnings, success messages, etc.
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Notices
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Notices' ) ) : 

/**
 * Charitable_Notices
 *
 * @since 		1.0.0
 */
class Charitable_Notices {

	/**
	 * The single instance of this class.  
	 *
	 * @var 	Charitable_Notices|null
	 * @access  private
	 * @static
	 */
	private static $instance = null;

	/**
	 * The array of notices.  
	 *
	 * @var 	array
	 * @access  protected
	 */
	protected $notices;

	/**
	 * Returns and/or create the single instance of this class.  
	 *
	 * @return 	Charitable_Notices
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Charitable_Notices();
		}

		return self::$instance;
	}

	/**
	 * Create class object. A private constructor, so this is used in a singleton context. 
	 * 
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct() {
		/* Retrieve the notices from the session */
		$this->notices = charitable_get_session()->get( 'notices' );

		/* Reset the session back to empty */
		charitable_get_session()->set( 'notices', array(
			'error'		=> array(), 
			'warning'	=> array(), 
			'success'	=> array(), 
			'info'		=> array()
		) );
	}

	/**
	 * Adds a notice message. 
	 *
	 * @param 	string 		$message
	 * @param 	string 		$type
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function add_notice( $message, $type ) {
		$this->notices[ $type ][] = $message; 
	}

	/**
	 * Add multiple notices at once.  
	 *
	 * @param 	array		$messages
	 * @param 	string 		$type
	 * @return  void
	 * @access  public
	 * @since   1.0.0
	 */
	public function add_notices( $messages, $type ) {
		if ( ! is_array( $messages ) ) {
			$messages = array( $messages );
		}

		$this->notices[ $type ] = array_merge( $this->notices[ $type ], $messages );
	}

	/**
	 * Adds an error message. 
	 *
	 * @param 	string 		$message
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function add_error( $message ) {
		$this->add_notice( $message, 'error' );
	}

	/**
	 * Adds a warning message. 
	 *
	 * @param 	string 		$message
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function add_warning( $message ) {
		$this->add_notice( $message, 'warning' );
	}

	/**
	 * Adds a success message. 
	 *
	 * @param 	string 		$message
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function add_success( $message ) {
		$this->add_notice( $message, 'success' );
	}

	/**
	 * Adds an info message. 
	 *
	 * @param 	string 		$message
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function add_info( $message ) {
		$this->add_notice( $message, 'info' );
	}

	/**
	 * Receives a WP_Error object and adds the error messages to our array. 
	 *
	 * @param 	WP_Error 	$error
	 * @return  void
	 * @access  public
	 * @since   1.0.0
	 */
	public function add_errors_from_wp_error( WP_Error $error ) {
		$this->add_notices( $error->get_error_messages(), 'error' );
	}

	/**
	 * Return all errors as an array. 
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_errors() {
		return $this->notices['error'];
	}

	/**
	 * Return all warnings as an array. 
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_warnings() {
		return $this->notices['warning'];
	}

	/**
	 * Return all successs as an array. 
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_success_notices() {
		return $this->notices['success'];
	}

	/**
	 * Return all infos as an array. 
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_info_notices() {
		return $this->notices['info'];
	}

	/**
	 * Return all notices as an array. 
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_notices() {
		return $this->notices;
	}
}	

endif; // End class_exists check