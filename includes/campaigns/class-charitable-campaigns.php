<?php
/** 
 * The class responsible for querying data about campaigns.
 *  
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Campaigns
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'Charitable_Campaigns' ) ) :

/** 
 * Charitable_Campaigns. 
 *  
 * @since		1.0.0
 */
class Charitable_Campaigns {

	/**
	 * Return WP_Query object with predefined defaults to query only campaigns. 
	 *
	 * @param 	array 		$args
	 * @return 	WP_Query
	 * @static
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function query( $args = array() ) {
		$defaults = array(
			'post_type'      => array( 'campaign' ),
			'posts_per_page' => get_option( 'posts_per_page' )
		);

		$args = wp_parse_args( $args, $defaults );

		return new WP_Query( $args );
	}
	
	/**
	 * Returns a WP_Query that will return active campaigns, ordered by the date they're ending.
	 *
	 * @param 	array $args 	Additional arguments to pass to WP_Query 
	 * @return	WP_Query
	 * @static
	 * @since 	1.0.0
	 */
	public static function ordered_by_ending_soon( $args = array() ) {
		$defaults = array(
			'meta_query' 	=> array(
				array(
					'key' 		=> '_campaign_end_date',
					'value' 	=> date( 'Y-m-d H:i:s' ),
					'compare' 	=> '>=',
					'type' 		=> 'datetime'
				)
			),
			'meta_key' 		=> '_campaign_end_date',
			'orderby' 		=> 'meta_value',
			'order' 		=> 'ASC'
		);

		$args = wp_parse_args( $args, $defaults );
		
		return Charitable_Campaigns::query( $args );	
	}

	/**
	 * Returns a WP_Query that will return campaigns, ordered by the amount they raised.
	 *
	 * @global 	$wpdb
	 * @param 	array $args 	Additional arguments to pass to WP_Query 
	 * @return 	WP_Query
	 * @static
	 * @since 	1.0.0
	 * @todo
	 */
	public static function ordered_by_amount( $args = array() ) {
		global $wpdb;

		/* Set up filters to order by amount */		
		add_filter( 'posts_join_paged', array( 'Charitable_Campaigns', 'join_campaign_donations_table' ) );
		add_filter( 'posts_groupby', array( 'Charitable_Campaigns', 'groupby_campaign_id' ) );
		add_filter( 'posts_orderby', array( 'Charitable_Campaigns', 'orderby_campaign_donation_amount' ) );

		$query = Charitable_Campaigns::query( $args );

		/* Clean up filters */
		remove_filter( 'posts_join_paged', array( 'Charitable_Campaigns', 'join_campaign_donations_table' ) );
		remove_filter( 'posts_groupby', array( 'Charitable_Campaigns', 'groupby_campaign_id' ) );
		remove_filter( 'posts_orderby', array( 'Charitable_Campaigns', 'orderby_campaign_donation_amount' ) );

		return $query;
	}

	/**
	 * A method used to join the campaign donations table on the campaigns query. 
	 *
	 * @param 	string 	$join_statement
	 * @return  string
	 * @access  public
	 * @static
	 * @since   1.0.0
	 */
	public static function join_campaign_donations_table( $join_statement ) {
		global $wpdb;
		$join_statement .= " LEFT JOIN {$wpdb->prefix}charitable_campaign_donations cd ON cd.campaign_id = $wpdb->posts.ID ";
		return $join_statement;
	}

	/**
	 * A method used to change the group by parameter of the campaigns query. 
	 *
	 * @return  string
	 * @access  public
	 * @static
	 * @since   1.0.0
	 */
	public static function groupby_campaign_id() {
		global $wpdb;
		return "$wpdb->posts.ID";
	}

	/**
	 * A method used to change the ordering of the campaigns query, to order by the amount donated.
	 *
	 * @return  string
	 * @access  public
	 * @static
	 * @since   1.0.0
	 */
	public static function orderby_campaign_donation_amount() {
		return "COALESCE(SUM(cd.amount), 0) DESC";
	}
}

endif; // End class_exists check 