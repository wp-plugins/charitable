<?php
/**
 * Donor model. 
 *
 * @package     Charitable/Classes/Charitable_Donor
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Donor' ) ) : 

/**
 * Charitable_Donor
 *
 * @since       1.0.0
 */
class Charitable_Donor {

    /**
     * The donor ID. 
     *
     * @var     int
     * @access  private
     */
    private $donor_id;

    /**
     * The donation ID. 
     *
     * @var     int
     * @access  private
     */
    private $donation_id;    

    /**
     * User object. 
     *
     * @var     Charitable_User
     * @access  private
     */
    private $user;

    /**
     * Donation object. 
     *
     * @var     Charitable_Donation|null
     * @access  private
     */
    private $donation = null;

    /**
     * Create class object.
     * 
     * @param   int $donor_id
     * @param   int $donation_id
     * @access  public
     * @since   1.0.0
     */
    public function __construct( $donor_id, $donation_id = false ) {
        $this->donor_id = $donor_id;
        $this->donation_id = $donation_id;        
    }

    /**
     * Magic getter method. Looks for the specified key in as a property before using Charitable_User's __get method. 
     *
     * @return  mixed
     * @access  public
     * @since   1.0.0
     */
    public function __get( $key ) {
        if ( isset( $this->$key ) ) {
            return $this->$key;
        }

        return $this->get_user()->$key;
    }

    /**
     * Return the Charitable_User object for this donor.
     *
     * @return  Charitable_User
     * @access  public
     * @since   1.0.0
     */
    public function get_user() {
        if ( ! isset( $this->user ) ) {
            $this->user = $this->user = Charitable_User::init_with_donor( $this->donor_id );
        }

        return $this->user;
    }

    /**
     * Return the Charitable_Donation object associated with this object.  
     *
     * @return  Charitable_Donation|false
     * @access  public
     * @since   1.0.0
     */
    public function get_donation() {
        if ( ! isset( $this->donation ) ) {            
            $this->donation = $this->donation_id ? new Charitable_Donation( $this->donation_id ) : false;
        }

        return $this->donation;
    }

    /**
     * Return the donor meta stored for the particular donation. 
     *
     * @return  array|false
     * @access  public
     * @since   1.0.0
     */
    public function get_donor_meta() {
        if ( ! $this->get_donation() ) {
            return false;
        }

        return get_post_meta( $this->donation_id, 'donor', true );
    }

    /**
     * Return the donor's name stored for the particular donation. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_name() {
        if ( ! $this->get_donor_meta() ) {
            return $this->get_user()->get_name();
        }

        $meta = $this->get_donor_meta();
        $first_name = isset( $meta[ 'first_name' ] ) ? $meta[ 'first_name' ] : '';
        $last_name = isset( $meta[ 'last_name' ] ) ? $meta[ 'last_name' ] : '';
        $name = trim( sprintf( '%s %s', $first_name, $last_name ) );

        return apply_filters( 'charitable_donor_name', $name, $this );
    }

    /**
     * Return the donor avatar. 
     *
     * @param   int $size
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_avatar( $size = 100 ) {
        return $this->get_user()->get_avatar();
    }

    /**
     * Return the donor location. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_location() {
        if ( ! $this->get_donor_meta() ) {
            return $this->get_user()->get_location();
        }

        $meta = $this->get_donor_meta();
        $city = isset( $meta[ 'city' ] ) ? $meta[ 'city' ] : '';
        $state = isset( $meta[ 'state' ] ) ? $meta[ 'state' ] : '';
        $country = isset( $meta[ 'country' ] ) ? $meta[ 'country' ] : '';
        
        $region = strlen( $city ) ? $city : $state;

        if ( strlen( $country ) ) {

            if ( strlen( $region ) ) {
                $location = sprintf( '%s, %s', $region, $country ); 
            }
            else {
                $location = $country;
            }
        }
        else {
            $location = $region;
        }

        return apply_filters( 'charitable_donor_location', $location, $this );
    }

    /**
     * Return the donation amount. 
     * 
     * If a donation ID was passed to the object constructor, this will return
     * the total donated with this particular donation. Otherwise, this will
     * return the total amount ever donated by the donor.
     *
     * @return  float
     * @access  public
     * @since   1.0.0
     */
    public function get_amount() {
        if ( $this->get_donation() ) {
            return charitable_get_table( 'campaign_donations' )->get_donation_total_amount( $this->donation_id );
        }

        return $this->get_user()->get_total_donated();
    }
}

endif; // End class_exists check