<?php
/**
 * Class that is responsible for generating a CSV export of donations.
 *
 * @package     Charitable/Classes/Charitable_Export_Donations
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Export_Donations' ) ) : 

/* Include Charitable_Export base class. */
if ( ! class_exists( 'Charitable_Export' ) ) {
    require_once( 'abstract-class-charitable-export.php' );
}

/**
 * Charitable_Export_Donations
 *
 * @since       1.0.0
 */
class Charitable_Export_Donations extends Charitable_Export {

    /**
     * @var     string  The type of export.
     */
    const EXPORT_TYPE = 'donations';

    /**
     * Create class object.
     * 
     * @param   mixed[] $args
     * @access  public
     * @since   1.0.0
     */
    public function __construct( $args ) {
        add_filter( 'charitable_export_data_key_value', array( $this, 'set_custom_field_data' ), 10, 3 );

        parent::__construct( $args );
    }

    /**
     * Filter the date and time fields. 
     *
     * @param   mixed   $value
     * @param   string  $key
     * @param   array   $data
     * @return  mixed
     * @access  public
     * @since   1.0.0
     */
    public function set_custom_field_data( $value, $key, $data ) {
        switch( $key ) {
            case 'date' : 
                if ( isset( $data[ 'post_date' ] ) ) {
                    $value = mysql2date( 'l, F j, Y', $data[ 'post_date' ] );
                }
                break;

            case 'time' : 
                if ( isset( $data[ 'post_date' ] ) ) {
                    $value = mysql2date( 'H:i A', $data[ 'post_date' ] );
                }
                break;
        }

        return $value;
    }

    /**
     * Return the CSV column headers.
     *
     * The columns are set as a key=>label array, where the key is used to retrieve the data for that column.
     *
     * @return  string[]
     * @access  protected
     * @since   1.0.0
     */
    protected function get_csv_columns() {
        $columns = array( 
            'donation_id'   => __( 'Donation ID', 'charitable' ), 
            'campaign_id'   => __( 'Campaign ID', 'charitable' ), 
            'campaign_name' => __( 'Campaign Title', 'charitable' ), 
            'first_name'    => __( 'Donor First Name', 'charitable' ), 
            'last_name'     => __( 'Donor Last Name', 'charitable' ), 
            'email'         => __( 'Donor Email', 'charitable' ), 
            'amount'        => __( 'Donation Amount', 'charitable' ), 
            'date'          => __( 'Date of Donation', 'charitable' ),
            'time'          => __( 'Time of Donation', 'charitable' ), 
            'post_content'  => __( 'Donor Note', 'charitable' )
        );

        return apply_filters( 'charitable_export_donations_columns', $columns, $this->args );
    }

    /**
     * Get the data to be exported.
     *
     * @return  array
     * @access  protected
     * @since   1.0.0
     */
    protected function get_data() {
        $query_args = array();

        if ( isset( $this->args[ 'campaign_id' ] ) ) {
            $query_args[ 'campaign_id' ] = $this->args[ 'campaign_id' ];
        }

        return charitable_get_table( 'campaign_donations' )->get_donations_report( $query_args );
    }   
}

endif; // End class_exists check