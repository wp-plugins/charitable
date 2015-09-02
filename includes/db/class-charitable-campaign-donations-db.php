<?php 
/**
 * Charitable Campaign Donations DB class. 
 *
 * @package     Charitable/Classes/Charitable_Campaign_Donations_DB
 * @version    	1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License   
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Campaign_Donations_DB' ) ) : 

/**
 * Charitable_Campaign_Donations_DB
 *
 * @since 		1.0.0 
 */
class Charitable_Campaign_Donations_DB extends Charitable_DB {	

	/**
	 * The version of our database table
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public $version = '1.0.0';

	/**
	 * The name of the primary column
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public $primary_key = 'campaign_donation_id';

	/**
	 * Set up the database table name. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function __construct() {
		global $wpdb;

		$this->table_name = $wpdb->prefix . 'charitable_campaign_donations';
	}

	/**
	 * Create the table.
	 *
	 * @global 	$wpdb
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function create_table() {
		global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
				`campaign_donation_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`donation_id` bigint(20) NOT NULL,
				`donor_id` bigint(20) NOT NULL,
				`campaign_id` bigint(20) NOT NULL,
				`campaign_name` text NOT NULL,
				`amount` float NOT NULL,
				PRIMARY KEY (campaign_donation_id),
				KEY donation (donation_id),
				KEY campaign (campaign_id)
				) $charset_collate;";

		$this->_create_table( $sql );
	}
	
	/**
	 * Whitelist of columns.
	 *
	 * @return  array 
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_columns() {
		return array(
			'campaign_donation_id'	=> '%d', 
			'donation_id'			=> '%d',
			'donor_id' 				=> '%d',
			'campaign_id'			=> '%d',
			'campaign_name'			=> '%s',
			'amount'				=> '%f'
		);
	}

	/**
	 * Default column values.
	 *
	 * @return 	array
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_column_defaults() {
		return array(
			'campaign_donation_id'	=> '', 
			'donation_id'			=> '',
			'donor_id'				=> '',
			'campaign_id'			=> '',
			'campaign_name'			=> '',
			'amount'				=> '',			
		);
	}

	/** 
	 * Add a new campaign donation.
	 * 
	 * @param 	array 		$data
	 * @return 	int 		The ID of the inserted campaign donation
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function insert( $data, $type = 'campaign_donation' ) {	
		if ( ! isset( $data[ 'campaign_name' ] ) ) {
			$data[ 'campaign_name' ] = get_the_title( $data[ 'campaign_id' ] );
		}

		Charitable_Campaign::flush_donations_cache( $data[ 'campaign_id' ] );

		return parent::insert( $data, $type );
	}

	/**
	 * Update campaign donation record. 
	 *
	 * @param 	int 		$row_id
	 * @param 	array 		$data
	 * @param 	string 		$where 			Column used in where argument.
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function update( $row_id, $data = array(), $where = '' ) {
		if ( empty( $where ) ) {
			Charitable_Campaign::flush_donations_cache( $row_id );
		}

		return parent::update( $row_id, $data, $where );
	}

	/**
	 * Delete a row identified by the primary key.
	 *
	 * @param 	int 		$row_id
	 * @access  public
	 * @since   1.0.0
	 * @return  bool
	 */
	public function delete( $row_id = 0 ) {	
		Charitable_Campaign::flush_donations_cache( $row_id );

		return parent::delete( $row_id );
	}

	/**
	 * Get the total amount donated, ever.
	 *
	 * @global 	$wpdb
	 * @return 	float
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_total() {
		global $wpdb;

		$sql = "SELECT SUM(amount) 
				FROM $this->table_name";

		return $wpdb->get_var( $sql );
	}

	/**
	 * Get an object of all campaign donations associated with a single donation. 
	 *
	 * @global 	wpdb	$wpdb
	 * @return 	Object
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_donation_records( $donation_id ) {
		global $wpdb;

		$sql = "SELECT * 
				FROM $this->table_name 
				WHERE donation_id = %d;";

		return $wpdb->get_results( $wpdb->prepare( $sql, intval( $donation_id ) ), OBJECT_K );				
	}

	/**
	 * Get the total amount donated in a single donation. 
	 *
	 * @global 	$wpdb
	 * @param 	int 	$donation_id
	 * @return 	float
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_donation_total_amount( $donation_id ) {
		global $wpdb;

		$sql = "SELECT SUM(amount) 
				FROM $this->table_name 
				WHERE donation_id = %d;";

		return $wpdb->get_var( $wpdb->prepare( $sql, intval( $donation_id ) ) );
	}

	/**
	 * Get an object of all donations on a campaign.
	 *
	 * @global 	wpdb 	$wpdb
	 * @param 	int 	$campaign_id
	 * @return 	object
	 * @since 	1.0.0
	 */
	public function get_donations_on_campaign( $campaign_id ){
		global $wpdb;
		return $wpdb->get_results( 
			$wpdb->prepare( 
				"SELECT * 
				FROM $this->table_name 
				WHERE campaign_id = %d;", 
				$campaign_id 
			), OBJECT_K);
	}

	/**
	 * Get total amount donated to a campaign.
	 *
	 * @global 	wpdb 	$wpdb
	 * @param 	int 	$campaign_id
	 * @param 	boolean $include_all
	 * @return 	int 					
	 * @since 	1.0.0
	 */
	public function get_campaign_donated_amount( $campaign_id, $include_all = false ) {
		global $wpdb;

		$statuses = $include_all ? array() : Charitable_Donation::get_approval_statuses();

		list( $status_clause, $parameters ) = $this->get_donation_status_clause( $statuses );

		array_unshift( $parameters, $campaign_id );

		$sql = "SELECT SUM(amount) cd
				FROM $this->table_name cd
				INNER JOIN $wpdb->posts p
				ON p.ID = cd.donation_id
				WHERE cd.campaign_id = %d
				$status_clause;";

		return $wpdb->get_var( $wpdb->prepare( $sql, $parameters ) );
	}	

	/**
	 * The users who have donated to the given campaign.
	 *
	 * @global 	wpdb	$wpdb
	 * @param 	int 	$campaign_id
	 * @return 	object
	 * @since 	1.0.0
	 */
	public function get_campaign_donors( $campaign_id ) {
		global $wpdb;
		return $wpdb->get_results( 
			$wpdb->prepare( 
				"SELECT DISTINCT p.post_author as donor_id
				FROM $this->table_name c
				INNER JOIN {$wpdb->prefix}posts p
				ON c.donation_id = p.ID
				WHERE c.campaign_id = %d;", 
				$campaign_id
			), OBJECT_K );
	} 	 

	 /**
	  * Return the number of users who have donated to the given campaign. 
	  *
	  * @global wpdb	$wpdb
	  * @param 	int 	$campaign_id
	  * @param 	boolean $include_all 	If false, only 
	  * @return int
	  * @since 	1.0.0
	  */
	public function count_campaign_donors( $campaign_id, $include_all = false ) {
		global $wpdb;

		$statuses = $include_all ? array() : Charitable_Donation::get_approval_statuses();

		list( $status_clause, $parameters ) = $this->get_donation_status_clause( $statuses );

		array_unshift( $parameters, $campaign_id );

		$sql = "SELECT COUNT( DISTINCT cd.donor_id ) 
				FROM $this->table_name cd
				INNER JOIN $wpdb->posts p ON p.ID = cd.donation_id
				WHERE cd.campaign_id = %d
				$status_clause;";

		return $wpdb->get_var( $wpdb->prepare( $sql, $parameters ) );
	}

	/**
	 * Return all donations made by a donor. 
	 *
	 * @global	wpdb	$wpdb
	 * @param 	int 	$donor_id
	 * @param 	boolean $distinct_donations
	 * @return 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_donations_by_donor( $donor_id, $distinct_donations = false ) {
		global $wpdb;

		if ( $distinct_donations ) {
			$select_fields = "DISTINCT( cd.donation_id ), cd.campaign_id, cd.campaign_name, cd.amount";
		}
		else {
			$select_fields = "cd.campaign_donation_id, cd.donation_id, cd.campaign_id, cd.campaign_name, cd.amount";
		}

		$sql = "SELECT $select_fields
				FROM $this->table_name cd
				WHERE cd.donor_id = %d;";

		return $wpdb->get_results( $wpdb->prepare( $sql, $donor_id ), OBJECT_K );
	}

	/**
	 * Return total amount donated by a donor. 
	 *
	 * @global	wpdb	$wpdb
	 * @param 	int 	$donor_id
	 * @return 	int
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_total_donated_by_donor( $donor_id ) {
		global $wpdb;

		$sql = "SELECT SUM(cd.amount)
				FROM $this->table_name cd
				WHERE cd.donor_id = %d;";

		return $wpdb->get_var( $wpdb->prepare( $sql, $donor_id ) );
	}

	/**
	 * Count the number of donations made by the donor.  
	 *
	 * @global	wpdb	$wpdb
	 * @param 	int 	$donor_id
	 * @param 	boolean $distinct_donations 	If true, will only count unique donations.
	 * @return 	int
	 * @access  public
	 * @since 	1.0.0
	 */
	public function count_donations_by_donor( $donor_id, $distinct_donations = false ) {
		global $wpdb;
		
		$count = $distinct_donations ? "DISTINCT donation_id" : "donation_id";

		$sql = "SELECT COUNT( $count )
				FROM $this->table_name
				WHERE donor_id = %d;";

		return $wpdb->get_var( $wpdb->prepare( $sql, $donor_id ) );
	}

	/**
	 * Count the number of campaigns that the donor has supported. 
	 *
	 * @global 	wpdb 	$wpdb
	 * @return  int
	 * @access  public
	 * @since   1.0.0
	 */
	public function count_campaigns_supported_by_donor( $donor_id ) {
		global $wpdb;
		
		$sql = "SELECT COUNT( DISTINCT campaign_id )
				FROM $this->table_name
				WHERE donor_id = %d;";

		return $wpdb->get_var( $wpdb->prepare( $sql, $donor_id ) );
	}

	/**
	 * Return a set of donations, filtered by the provided arguments. 
	 *
	 * @param 	array 	$args
	 * @return  array
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_donations_report( $args ) {
		global $wpdb;

		$parameters = array();
		$sql_where = "";
		$sql_where_clauses = array();		

		if ( isset( $args[ 'campaign_id' ] ) ) {
			$sql_where_clauses[] = "cd.campaign_id = %d";
			$parameters[] = intval( $args[ 'campaign_id' ] );
		}

		if ( ! empty( $sql_where_clauses ) ) {
			$sql_where = "WHERE " . implode( " OR ", $sql_where_clauses );
		}

		/* This is our base SQL query */
		$sql = "SELECT cd.donation_id, cd.campaign_id, cd.campaign_name, cd.amount, d.email, d.first_name, d.last_name, p.post_date, p.post_content, p.post_status
				FROM $this->table_name cd
				INNER JOIN {$wpdb->prefix}charitable_donors d
				ON d.donor_id = cd.donor_id
				INNER JOIN $wpdb->posts p
				ON p.ID = cd.donation_id
				$sql_where";		

		return $wpdb->get_results( $wpdb->prepare( $sql, $parameters ) );
	}

	/**
	 * Returns the donation status clause. 
	 *
	 * @param 	boolean $include_all 	If true, will return a blank string. 
	 * @return  string
	 * @access  private
	 * @since   1.0.0
	 */
	private function get_donation_status_clause( $statuses = array() ) {
		if ( empty( $statuses ) ) {
			return array( "", array() );
		}

		$statuses = array_filter( $statuses, array( 'Charitable_Donation', 'is_valid_donation_status' ) );
		$placeholders = array_fill( 0, count( $statuses ), '%s' );
		$in = implode( ', ', $placeholders );

		$sql = "AND p.post_status IN ( $in )";

		$clause = array( $sql, $statuses );

		return $clause;
	}
}	

endif;