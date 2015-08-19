<?php
/**
 * Donation model
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Donation
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation' ) ) : 

/**
 * Donation Model
 *
 * @since       1.0.0
 */

class Charitable_Donation {
    
    /**
     * The donation ID. 
     *
     * @var     int 
     * @access  private
     */
    private $donation_id;

    /**
     * The database record for this donation from the Posts table.
     * 
     * @var     Object 
     * @access  private 
     */
    private $donation_data;

    /**
     * The Campaign Donations table.
     *
     * @var     Charitable_Campaign_Donations_DB
     * @access  private
     */
    private $campaign_donations_db; 

    /**
     * The payment gateway used to process the donation.
     *
     * @var     Charitable_Gateway_Interface
     * @access  private
     */
    private $gateway;

    /**
     * The campaign donations made as part of this donation. 
     *
     * @var     Object
     * @access  private
     */
    private $campaign_donations;

    /**
     * The WP_User object of the person who donated. 
     * 
     * @var     WP_User 
     * @access  private
     */
    private $donor;

    /**
     * Instantiate a new donation object based off the ID.
     * 
     * @param   mixed       $donation       The donation ID or WP_Post object.
     * @access  public
     * @since   1.0.0
     */
    public function __construct( $donation ) {
        if ( is_a( $donation, 'WP_Post' ) ) {
            $this->donation_id          = $donation->ID;
            $this->donation_data        = $donation;    
        }
        else {
            $this->donation_id          = $donation;
            $this->donation_data        = get_post( $donation );        
        }       
    }

    /**
     * Magic getter.
     *
     * @param   string      $key
     * @return  mixed
     * @access  public
     * @since   1.0.0
     */
    public function __get( $key ) {
        if ( method_exists( $this, 'get_' . $key ) ) {
            $method = 'get_' . $key;
            return $this->$method;
        }

        return $this->donation_data->$key;
    }

    /**
     * Return the donation number. By default, this is the ID, but it can be filtered. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_number() {
        return apply_filters( 'charitable_donation_number', $this->donation_id );
    }

    /**
     * Get the donation data.
     *
     * @return  Charitable_Campaign_Donations_DB
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign_donations_db() {
        if ( ! isset( $this->campaign_donations_db ) ) {
            $this->campaign_donations_db = new Charitable_Campaign_Donations_DB();
        }

        return $this->campaign_donations_db;
    }

    /**
     * The amount donated on this donation.
     *
     * @return  float
     * @access  public
     * @since   1.0.0
     */
    public function get_total_donation_amount() {
        return $this->get_campaign_donations_db()->get_donation_total_amount( $this->donation_id );
    }

    /**
     * Return the campaigns donated to in this donation. 
     *
     * @return  object[]
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign_donations() {
        if ( ! isset( $this->campaign_donations ) ) {
            $this->campaign_donations = $this->get_campaign_donations_db()->get_donation_records( $this->donation_id );
        }

        return $this->campaign_donations;
    }

    /**
     * Returns an array of the campaigns that were donated to.
     *
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function get_campaigns() {
        return array_map( array( $this, 'get_campaign_name' ), $this->get_campaign_donations() );
    }

    /**
     * Returns the campaign name from a campaign donation record.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign_name( $campaign_donation ) {
        return $campaign_donation->campaign_name;
    }

    /**
     * Return a comma separated list of the campaigns that were donated to. 
     *
     * @param   boolean     $linked         Whether to return the campaigns with links to the campaign pages.
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_campaigns_donated_to( $linked = false ) {
        $campaigns = $linked ? $this->get_campaigns_links() : $this->get_campaigns();

        return implode( ', ', $campaigns );
    }

    /**
     * Return a comma separated list of the campaigns that were donated to, with links to the campaigns. 
     *
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function get_campaigns_links() {
        $links = array();

        foreach ( $this->get_campaign_donations() as $campaign ) {

            if ( ! isset( $links[ $campaign->campaign_id ] ) ) {

                $links[ $campaign->campaign_id ] = sprintf( '<a href="%s" title="%s">%s</a>', 
                    get_permalink( $campaign->campaign_id ), 
                    sprintf( '%s %s', _x( 'Go to', 'go to campaign', 'charitable' ), get_the_title( $campaign->campaign_id ) ), 
                    get_the_title( $campaign->campaign_id )
                );

            }
        }

        return $links;
    }

    /**
     * Return the date of the donation.
     *
     * @param   string $format
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_date( $format = '' ) {
        if ( empty( $format ) ) {
            $format = get_option( 'date_format' );
        }

        return date_i18n( $format, strtotime( $this->donation_data->post_date ) );
    }

    /**
     * The name of the gateway used to process the donation.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_gateway() {
        return get_post_meta( $this->donation_id, 'donation_gateway', true );
    }

    /**
     * Return the unique donation key. 
     *
     * @return  string The key identifier of the donation.
     * @access  public
     * @since   1.0.0
     */
    public function get_donation_key() {
        return get_post_meta( $this->donation_id, 'donation_key', true );
    }

    /**
     * The public label of the gateway used to process the donation. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_gateway_label() {
        $gateway = $this->get_gateway_object();

        if ( ! $gateway ) {
            return '';
        } 

        return $gateway->get_label();
    }

    /**
     * Returns the gateway's object helper. 
     *
     * @return  Charitable_Gateway
     * @access  public
     * @since   1.0.0
     */
    public function get_gateway_object() {
        $class = charitable_get_helper( 'gateways' )->get_gateway( $this->get_gateway() );

        if ( ! $class ) {
            return false;
        } 

        return new $class;
    }

    /**
     * The status of this donation.
     *
     * @param   boolean     $label  Whether to return the label. If not, returns the key.
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_status( $label = false ) {
        $status = $this->donation_data->post_status;

        if ( ! $label ) {
            return $status;
        }

        $statuses = self::get_valid_donation_statuses();
        return $statuses[ $status ];
    } 

    /**
     * Returns the donation ID. 
     * 
     * @return  int
     * @access  public
     * @since   1.0.0
     */
    public function get_donation_id() {
        return $this->donation_id;
    }

    /**
     * Returns the customer note attached to the donation.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_notes() {
        return $this->donation_data->post_content;
    }

    /**
     * Returns the donor ID of the donor. 
     *
     * @return  int
     * @access  public
     * @since   1.0.0
     */
    public function get_donor_id() {
        return current( $this->get_campaign_donations() )->donor_id;
    }

    /**
     * Returns the donor who made this donation.
     *
     * @return  Charitable_User
     * @access  public
     * @since   1.0.0
     */
    public function get_donor() {
        if ( ! isset( $this->donor ) ) {
            $this->donor = Charitable_User::init_with_donor( $this->get_donor_id() );
        }

        return $this->donor;
    }
    
    /**
     * Return array of valid donations statuses. 
     *
     * @return  array
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function get_valid_donation_statuses() {
        return apply_filters( 'charitable_donation_statuses', array( 
            'charitable-completed'  => __( 'Paid', 'charitable' ),
            'charitable-pending'    => __( 'Pending', 'charitable' ),           
            'charitable-failed'     => __( 'Failed', 'charitable' ),
            'charitable-cancelled'  => __( 'Cancelled', 'charitable' ),
            'charitable-refunded'   => __( 'Refunded', 'charitable' )
        ) );
    }   

    /**
     * Returns whether the donation status is valid. 
     *
     * @return  boolean
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function is_valid_donation_status( $status ) {
        return array_key_exists( $status, self::get_valid_donation_statuses() );
    }

    /**
     * Returns the donation statuses that signify a donation was complete. 
     *
     * By default, this is just 'charitable-completed'. However, 'charitable-preapproval' 
     * is also counted. 
     *
     * @return  string[]
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function get_approval_statuses() {
        return apply_filters( 'charitable_approval_donation_statuses', array( 'charitable-completed' ) );
    }

    /**
     * Returns whether the passed status is an confirmed status. 
     *
     * @return  boolean
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function is_approved_status( $status ) {
        return in_array( $status, self::get_approval_statuses() );
    }

    /**
     * Add a message to the donation log. 
     *
     * @param   string      $message
     * @return  void
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function update_donation_log( $donation_id, $message ) {
        $log = self::get_donation_log( $donation_id );

        $log[] = array( 
            'time'      => time(), 
            'message'   => $message
        );

        update_post_meta( $donation_id, '_donation_log', $log );
    }

    /**
     * Get a donation's log.  
     *
     * @return  array
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function get_donation_log( $donation_id ) {
        $log = get_post_meta( $donation_id, '_donation_log', true );;

        return is_array( $log ) ? $log : array();
    }

    /**
     * Sanitize meta values before they are persisted to the database. 
     *
     * @param   mixed   $value
     * @param   string  $key
     * @return  mixed
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function sanitize_meta( $value, $key ) {
        if ( 'donation_gateway' == $key ) {         
            if ( empty( $value ) || ! $value ) {
                $value = 'manual';
            }           
        }

        return apply_filters( 'charitable_sanitize_donation_meta-' . $key, $value );
    }

    /**
     * Update the status of the donation. 
     *  
     * @uses    wp_update_post()
     * @param   string      $new_status
     * @return  int|WP_Error                    The value 0 or WP_Error on failure. The donation ID on success.
     * @access  public
     * @since   1.0.0
     */
    public function update_status( $new_status ) {
        if ( false === self::is_valid_donation_status( $new_status ) ) {
            $new_status = array_search( $new_status, self::get_valid_donation_statuses() );

            if ( false === $new_status ) {
                _doing_it_wrong( __METHOD__, sprintf( '%s is not a valid donation status.', $new_status ), '1.0.0' );
                return 0;
            }
        }

        $valid_statuses = self::get_valid_donation_statuses();

        $old_status = $this->get_status();      

        if ( $old_status == $new_status ) {
            return 0;
        }       

        /* This actually updates the post status */
        $this->donation_data->post_status = $new_status;
        $donation_id = wp_update_post( $this->donation_data );

        self::update_donation_log( $donation_id, sprintf( __( 'Donation status updated from %s to %s.', 'charitable' ), $valid_statuses[$old_status], $valid_statuses[$new_status] ) );

        do_action( 'charitable_after_update_donation', $donation_id, $new_status );

        return $donation_id;
    }

    /**
     * Process a refund. 
     *
     * @param   float $refund_amount
     * @param   string $message
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function process_refund( $refund_amount, $message = "" ) {       
        $campaign_donations = $this->get_campaign_donations();

        $refund_log = get_post_meta( $this->ID, 'donation_refund', true );

        $total_refund = isset( $refund_log[ 'total_refund' ] ) ? $refund_log[ 'total_refund' ] : 0;
        $refunds_per_campaign = isset( $refund_log[ 'campaign_refunds' ] ) ? $refund_log[ 'campaign_refunds' ] : array();
        
        foreach ( $this->get_campaign_donations() as $campaign_donation ) {

            if ( $refund_amount == 0 ) {
                break;
            }

            if ( ! isset( $refunds_per_campaign[ $campaign_donation->campaign_id ] ) ) {
                $refunds_per_campaign[ $campaign_donation->campaign_id ] = array();
            }

            /** 
             * Calculate the amount to be refunded out of this particular campaign's amount. 
             *
             * This takes into account any amounts that have already been refunded, to find the 
             * amount that remains credited towards to the campaign.
             */
            $campaign_remaining_amount = $campaign_donation->amount - array_sum( $refunds_per_campaign[ $campaign_donation->campaign_id ] );

            if ( $campaign_remaining_amount > $refund_amount ) {
                $campaign_refund_amount = $refund_amount;   
            }
            else {
                $campaign_refund_amount = $campaign_remaining_amount;
            }           

            $refunds_per_campaign[ $campaign_donation->campaign_id ][] = $campaign_refund_amount;

            /* Reduce the remaining amount to refund. */
            $refund_amount -= $campaign_refund_amount;

            /* Increase the total refund amount. */
            $total_refund += $campaign_refund_amount;
        }

        $refund_log = array(
            'time' => time(), 
            'message' => $message, 
            'campaign_refunds' => $refunds_per_campaign, 
            'total_refund' => $total_refund
        );

        update_post_meta( $this->ID, 'donation_refund', $refund_log );

        $this->update_status( 'charitable-refunded' );
    }

    /**
     * Flush the donations cache for every campaign receiving a donation. 
     *
     * @param   int $donation_id
     * @return  void
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function flush_campaigns_donation_cache( $donation_id ) {
        $campaign_donations = charitable_get_table( 'campaign_donations' )->get_donation_records( $donation_id );

        foreach ( $campaign_donations as $campaign_donation ) {
            Charitable_Campaign::flush_donations_cache( $campaign_donation->campaign_id );
        }
    }
}

endif; // End class_exists check