<?php
/** 
 * The class that is responsible for querying data about donations.
 *  
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Donations
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'Charitable_Donation_Query' ) ) :

/** 
 * Charitable_Donations
 *  
 * @since		1.0.0
 * @uses 		WP_Query
 */
class Charitable_Donations {

	/**
	 * Return WP_Query object with predefined defaults to query only donations. 
	 *
	 * @param 	array 		$args
	 * @return 	WP_Query
	 * @static
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function query( $args = array() ) {
		$defaults = array(
			'post_type'      => array( 'donation' ),
			'posts_per_page' => get_option( 'posts_per_page' )
		);

		$args = wp_parse_args( $args, $defaults );

		return new WP_Query( $args );
	}

	/**
	 * Return the number of all donations.
	 *
	 * @global 	WPDB 		$wpdb
	 * @return 	int
	 * @access  public
	 * @static
	 * @since 	1.0.0
	 */
	public static function count_all() {
		global $wpdb;

		$sql = "SELECT COUNT( * ) 
				FROM $wpdb->posts 
				WHERE post_type = 'donation'";

		return $wpdb->get_var( $sql );
	}

	/**
	 * Return count of donations grouped by status. 
	 *
	 * @global 	WPDB 		$wpdb
	 * @return 	array
	 * @access  public
	 * @static
	 * @since 	1.0.0
	 */
	public static function count_by_status() {
		global $wpdb;

		$sql = "SELECT post_status, COUNT( * ) AS num_donations
				FROM $wpdb->posts	
				WHERE post_type = 'donation'			
				GROUP BY post_status";

		return $wpdb->get_results( $sql, OBJECT_K );
	}
}

endif; // End class_exists check 