<?php
/**
 * Charitable Install class.
 * 
 * The responsibility of this class is to manage the events that need to happen 
 * when the plugin is activated.
 *
 * @package		Charitable
 * @subpackage	Charitable/Charitable Install
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 		1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Install' ) ) : 

/**
 * Charitable_Install
 *
 * @since 		1.0.0
 */
class Charitable_Install {

	/**
	 * Install the plugin. 
	 *
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function __construct() {			
		$this->setup_roles();
		$this->create_tables();	

		add_action( 'init', 'flush_rewrite_rules', 99 ); // Runs after post types & taxonomies are registered
		add_action( 'plugins_loaded', array( $this, 'do_install_action' ), 100 );		
	}

	/**
	 * Fire off a 'charitable_install' action late in the plugins_loaded cycle. 
	 *
	 * @return  void
	 * @access  public
	 * @since   1.0.0
	 */
	public function do_install_action() {
		do_action( 'charitable_install' );
	}

	/**
	 * Create wp roles and assign capabilities
	 *
	 * @return 	void
	 * @static
	 * @access 	public
	 * @since 	1.0.0
	 */
	private function setup_roles() {
		require_once( 'users/class-charitable-roles.php' );
		$roles = new Charitable_Roles();
		$roles->add_roles();
		$roles->add_caps();
	}

	/**
	 * Create database tables. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function create_tables() {
		require_once( 'db/abstract-class-charitable-db.php' );

		require_once( 'db/class-charitable-campaign-donations-db.php' );
		$table_helper = new Charitable_Campaign_Donations_DB();
		$table_helper->create_table();

		require_once( 'db/class-charitable-donors-db.php' );
		$table_helper = new Charitable_Donors_DB();
		$table_helper->create_table();
	}
}

endif;