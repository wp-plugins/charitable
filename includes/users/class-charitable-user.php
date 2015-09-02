<?php
/**
 * Contains the class that models users in Charitable.
 *
 * There are several different user roles in Charitable, and one user 
 * may be more than one. People who make donations get the "donor" role;
 * people who create campaigns (via Charitable Ambassadors) get 
 * the "campaign_creator" role; people who create fundraisers for campaigns 
 * get the "fundraiser" role.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_User
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_User' ) ) : 

/**
 * Charitable_User
 *
 * @since       1.0.0
 */
class Charitable_User extends WP_User {

    /**
     * @var     int
     * @access  protected
     */
    protected $donor_id;

    /**
     * Create class object.
     * 
     * @param   int|string|stdClass|WP_User $id     User's ID, a WP_User object, or a user object from the DB.
     * @param   string  $name                       Optional. User's username
     * @param   int     $blog_id                    Optional Blog ID, defaults to current blog.
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function __construct( $id = 0, $name = '', $blog_id = '' ) {
        parent::__construct( $id, $name, $blog_id );
    }

    /**
     * Create object using a donor ID. 
     *
     * @param   int     $donor_id
     * @return  Charitable_user
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function init_with_donor( $donor_id ) {
        $user_id = charitable_get_table( 'donors' )->get_user_id( $donor_id );
        $user = charitable_get_user( $user_id );
        $user->set_donor_id( $donor_id );
        return $user;
    }

    /**
     * Magic getter method. Looks for the specified key in the mapped keys before using WP_User's __get method. 
     *
     * @return  mixed
     * @access  public
     * @since   1.0.0
     */
    public function __get( $key ) {
        $mapped_keys = $this->get_mapped_keys();

        if ( array_key_exists( $key, $mapped_keys ) ) {
            $key = $mapped_keys[ $key ];
        }

        return parent::__get( $key );
    }

    /**
     * Display the donor name when printing the object.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function __toString() {
        return $this->get_name();
    }

    /**
     * Returns whether the user is logged in. 
     *
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function is_logged_in() {
        return 0 !== $this->ID;
    }

    /**
     * Set the donor ID of this user. 
     *
     * @param   int     $donor_id
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function set_donor_id( $donor_id ) {
        $this->donor_id = $donor_id;
    }

    /**
     * Return the donor ID of this user. 
     *
     * @return  int     $donor_id
     * @access  public
     * @since   1.0.0
     */
    public function get_donor_id() {
        if ( isset( $this->donor_id ) || ! is_null( $this->get_donor() ) ) {
            return $this->donor_id;
        }        

        return 0;
    }

    /**
     * Return the donor record. 
     *
     * @return  Object|null     Object if a donor record could be matched. 
     *                          null if user is logged out or no donor record found.
     * @access  public
     * @since   1.0.0
     */
    public function get_donor() {    
        if ( ! $this->is_logged_in() && ! isset( $this->donor_id ) ) {
            return null;
        } 

        if ( isset( $this->donor_id ) ) {
            $donor = wp_cache_get( $this->donor_id, 'donors' );

            if ( is_object( $donor ) ) {
                return $donor;
            }

            $donor = charitable_get_table( 'donors' )->get( $this->donor_id );
        }    
        else {

            $donor = charitable_get_table( 'donors' )->get_by( 'user_id', $this->ID );

            if ( ! is_object( $donor ) ) {
                return null;
            }

            $this->donor_id = $donor->donor_id;
        }

        wp_cache_add( $this->donor_id, $donor, 'donors' );

        return $donor;
    }

    /**
     * Returns whether the user has ever made a donation. 
     *
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function is_donor() {
        return ! is_null( $this->get_donor() );
    }

    /**
     * Returns the email address of the donor.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_email() {
        return apply_filters( 'charitable_user_email', $this->get_donor()->email, $this );
    }

    /**
     * Returns the display name of the user.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_name() {
        if ( $this->is_donor() ) {
            $name = rtrim( sprintf( '%s %s', $this->get_donor()->first_name, $this->get_donor()->last_name ) );
        }        
        else {
            $name = $this->display_name;
        }
        return apply_filters( 'charitable_user_name', $name, $this );
    }

    /**
     * Returns the user's location.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_location() {
        $city = $this->get( 'donor_city' );
        $state = $this->get( 'donor_state' );
        $country = $this->get( 'donor_country' );
        $location = "";

        if ( strlen( $city ) || strlen( $state ) ) {
            $region = strlen( $city ) ? $city : $state;

            if ( strlen( $country ) ) {
                $location = sprintf( "%s, %s", $region, $country );
            }
            else {
                $location = $region;
            }
        }
        elseif ( strlen( $country ) ) {
            $location = $country;
        }       

        return apply_filters( 'charitable_user_location', $location, $this );
    }

    /**
     * Return an array of fields used for the address. 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_address_fields() {
        return apply_filters( 'charitable_user_address_fields', array(
            'donor_address', 
            'donor_address_2', 
            'donor_city', 
            'donor_state', 
            'donor_postcode', 
            'donor_country'
        ) );
    }

    /**
     * Returns printable address of donor. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_address() {
        $address_fields = apply_filters( 'charitable_user_address_fields', array(
            'first_name'    => $this->get( 'first_name' ),
            'last_name'     => $this->get( 'last_name' ),
            'company'       => $this->get( 'donor_company' ),
            'address'       => $this->get( 'donor_address' ),
            'address_2'     => $this->get( 'donor_address_2' ),
            'city'          => $this->get( 'donor_city' ),
            'state'         => $this->get( 'donor_state' ),
            'postcode'      => $this->get( 'donor_postcode' ),
            'country'       => $this->get( 'donor_country' )
        ), $this );

        return charitable_get_location_helper()->get_formatted_address( $address_fields );
    }

    /**
     * Return all donations made by donor. 
     *
     * @param   boolean     $distinct_donations     If true, will only count unique donations.
     * @return  Object
     * @access  public
     * @since   1.0.0
     */
    public function get_donations( $distinct_donations = false ) {
        return charitable_get_table( 'campaign_donations' )->get_donations_by_donor( $this->get_donor_id(), $distinct_donations );
    }

    /**
     * Return the number of donations made by the donor. 
     *
     * @param   boolean     $distinct_donations     If true, will only count unique donations.
     * @return  int
     * @access  public
     * @since   1.0.0
     */
    public function count_donations( $distinct_donations = false ) {
        return charitable_get_table( 'campaign_donations' )->count_donations_by_donor( $this->get_donor_id(), $distinct_donations);
    }

    /**
     * Return the number of campaigns that the donor has supported.
     *
     * @return  int
     * @access  public
     * @since   1.0.0
     */
    public function count_campaigns_supported() {
        return charitable_get_table( 'campaign_donations' )->count_campaigns_supported_by_donor( $this->get_donor_id() );
    }

    /**
     * Return the total amount donated by the donor.
     *
     * @return  float
     * @access  public
     * @since   1.0.0
     */
    public function get_total_donated() {
        return (float) charitable_get_table( 'campaign_donations' )->get_total_donated_by_donor( $this->get_donor_id() );
    }

    /**
     * Returns the user's avatar as a fully formatted <img> tag.
     *
     * By default, this will return the gravatar, but it can 
     * be extended to add support for locally hosted avatars.
     *
     * @param   int $size
     * @return  string  
     * @access  public
     * @since   1.0.0
     */
    public function get_avatar( $size = 100 ) {     
        /** If you use this filter, return an attachment ID. */
        $avatar_attachment_id = apply_filters( 'charitable_user_avatar', false, $this );

        /* If we don't have an attachment ID, display the gravatar. */
        if ( ! $avatar_attachment_id ) {
            return get_avatar( $this->ID, $size );
        }

        $attachment_src = wp_get_attachment_image_src( $avatar_attachment_id, array( $size, $size ) );

        /* No image for the given attachment ID? Fall back to the gravatar. */
        if ( ! $attachment_src ) {
            return get_avatar( $this->ID, $size );          
        }

        return apply_filters( 'charitable_user_avatar_custom', sprintf( '<img src="%s" alt="%s" class="avatar photo" width="%s" height="%s" />', 
            $attachment_src[ 0 ] , 
            esc_attr( $this->display_name ), 
            $attachment_src[ 1 ],
            $attachment_src[ 2 ]
        ), $avatar_attachment_id, $size, $this );
    }

    /**
     * Return the src of the avatar.  
     *
     * @param   int         $size
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_avatar_src( $size = 100 ) {     
        /* If this returns something, we don't need to deal with the gravatar. */
        $avatar = apply_filters( 'charitable_user_avatar_src', false, $this, $size );

        if ( false === $avatar ) {

            /* The gravatars are returned as fully formatted img tags, so we need to pull out the src. */
            $gravatar = get_avatar( $this->ID, $size );

            preg_match( "@src='([^']+)'@" , $gravatar, $matches );

            $avatar = array_pop( $matches );
        }

        return $avatar;
    }

    /**
     * Return the campaigns created by the user. 
     *
     * @param   array       $args       Optional. Any arguments accepted by WP_Query.
     * @return  WP_Query
     * @access  public
     * @since   1.0.0
     */
    public function get_campaigns( $args = array() ) {
        $defaults = array(
            'author' => $this->ID
        );

        $args = wp_parse_args( $args, $defaults );

        return Charitable_Campaigns::query( $args );
    }

    /**
     * Checks whether the user has any current campaigns (i.e. non-expired). 
     *
     * @return  WP_Query
     * @access  public
     * @since   1.0.0
     */
    public function get_current_campaigns( $args = array() ) {
        $defaults = array(
            'author' => $this->ID, 
            'meta_query'    => array(
                'relation'      => 'OR',
                array(
                    'key'       => '_campaign_end_date',
                    'value'     => date( 'Y-m-d H:i:s' ),
                    'compare'   => '>=',
                    'type'      => 'datetime'
                ), 
                array( 
                    'key'       => '_campaign_end_date',
                    'value'     => '0'
                )
            )
        );

        $args = wp_parse_args( $args, $defaults );

        return Charitable_Campaigns::query( $args );
    }

    /**
     * Returns all current campaigns by the user. 
     *
     * @return  WP_Query
     * @access  public
     * @since   1.0.0
     */
    public function has_current_campaigns() {
        return $this->get_current_campaigns()->found_posts;
    }

    /**
     * Returns the user's donation and campaign creation activity. 
     *
     * @see     WP_Query    
     * @param   array       $args       Optional. Any arguments accepted by WP_Query.
     * @return  WP_Query 
     * @access  public
     * @since   1.0.0
     */
    public function get_activity( $args = array() ) {
        $defaults = array(
            'author'        => $this->ID,
            'post_status'   => array( 'charitable-completed', 'charitable-preapproved', 'publish' ),
            'post_type'     => array( 'donation', 'campaign' ), 
            'order'         => 'DESC', 
            'orderby'       => 'date'
        );

        $args = wp_parse_args( $args, $defaults );

        $args = apply_filters( 'charitable_user_activity_args', $args, $this );

        return new WP_Query( $args );
    }

    /**
     * Add a new donor. This may also create a user account for them.   
     *
     * @param   array   $submitted
     * @return  int     $donor_id
     * @access  public
     * @since   1.0.0
     */
    public function add_donor( $submitted = array() ) {
        $email = false;

        if ( isset( $submitted[ 'email' ] ) ) {
            $email = $submitted[ 'email' ];
        }

        if ( ! $email && $this->is_logged_in() ) {
            $email = $this->user_email;
        }

        /**  
         * Still no email? We're going to have to call an end 
         * to this party right now then. 
         */
        if ( ! $email ) {
            _doing_it_wrong( __METHOD__, __( 'Unable to add donor. Email not set for logged out user.', 'charitable' ), '1.0.0' );
            return 0;
        }

        $donor_values = apply_filters( 'charitable_donor_values', array(
            'user_id' => $this->ID,
            'email' => $email, 
            'first_name' => isset( $submitted[ 'first_name' ] ) ? $submitted[ 'first_name' ] : $this->first_name,
            'last_name' => isset( $submitted[ 'last_name' ] ) ? $submitted[ 'last_name' ] : $this->last_name
        ), $this, $submitted );

        $donor_id = charitable_get_table( 'donors' )->insert( $donor_values );

        do_action( 'charitable_after_insert_donor', $donor_id, $this );

        return $donor_id;
    }

    /**
     * Insert a new donor with submitted values. 
     *
     * @param   array       $submitted      The submitted values.
     * @param   array       $keys           The keys of fields that are to be updated.   
     * @return  int 
     * @access  public
     * @since   1.0.0
     */
    public function update_profile( $submitted = array(), $keys = array() ) {
        if ( empty( $submitted ) ) {
            $submitted = $_POST;
        }

        if ( empty( $keys ) ) {
            $keys = array_keys( $submitted );
        }

        $this->update_core_user( $submitted );

        $this->update_user_meta( $submitted, $keys );
    }

    /**
     * Save core fields of the user (i.e. the wp_users data) 
     *
     * @uses    wp_insert_user
     * @param   array   $submitted
     * @return  int     User ID
     * @access  public
     * @since   1.0.0
     */
    public function update_core_user( $submitted ) {
        $core_fields = array_intersect( array_keys( $submitted ), $this->get_core_keys() );

        if ( empty( $core_fields ) ) {
            return 0;
        }

        $values = array();

        /* If we're updating an active user, set the ID */
        if ( 0 !== $this->ID ) {

            $values[ 'ID' ] = $this->ID;

        }

        foreach ( $core_fields as $field ) {

            $values[ $field ] = $submitted[ $field ];

        }

        /* Insert the user */
        if ( 0 == $this->ID ) {         
    
            if ( ! isset( $values[ 'user_pass' ] ) ) {
                $values[ 'user_pass' ] = wp_generate_password();
            }       

            if ( ! isset( $values[ 'user_login' ] ) ) {
                $values[ 'user_login' ] = $values[ 'user_email' ];
            }       

            $user_id = wp_insert_user( $values );

            if ( is_wp_error( $user_id ) ) {

                charitable_get_notices()->add_errors_from_wp_error( $user_id );
                return 0;

            }

            $this->init( self::get_data_by( 'id', $user_id ) );       

            do_action( 'charitable_after_insert_user', $user_id, $values );

        }
        else {

            $values[ 'ID' ] = $this->ID;

            $user_id = wp_update_user( $values );

        }

        /* If there was an error when inserting or updating the user, lodge the error. */
        if ( is_wp_error( $user_id ) ) {

            charitable_get_notices()->add_errors_from_wp_error( $user_id );

        }

        do_action( 'charitable_after_save_user', $user_id, $values );

        return $user_id;
    }

    /**
     * Save the user's meta fields. 
     *   
     * @param   array       $submitted      The submitted values.
     * @param   array       $keys           The keys of fields that are to be updated. 
     * @return  int         Number of fields updated.
     * @access  public
     * @since   1.0.0
     */
    public function update_user_meta( $submitted, $keys ) {
        /* Exclude the core keys */     
        $mapped_keys    = $this->get_mapped_keys();
        $meta_fields    = array_diff( $keys, $this->get_core_keys() );
        $updated        = 0;

        foreach ( $meta_fields as $field ) {

            if ( isset( $submitted[ $field ] ) ) {

                $meta_key = array_key_exists( $field, $mapped_keys ) ? $mapped_keys[ $field ] : $field;

                $meta_value = sanitize_meta( $meta_key, $submitted[ $field ], 'user' );

                update_user_meta( $this->ID, $meta_key, $meta_value );

                $updated++;

            }

        }

        return $updated;
    }

    /**
     * Automatically sign on user after registration. 
     *
     * @param   int     $user_id
     * @param   array   $values
     * @return  WP_User|WP_Error|false WP_User on login, WP_Error on failure. False if feature is disabled.
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function signon( $user_id, $values ) {
        if ( ! apply_filters( 'charitable_auto_login_after_registration', true, $user_id ) ) {
            return false;
        }

        if ( is_user_logged_in() ) {
            return false;
        }

        $creds = array(
            'user_login'    => $values[ 'user_login' ], 
            'user_password' => $values[ 'user_pass' ], 
            'remember'      => true
        );
        
        return wp_signon( $creds, false );
    }

    /**
     * Return the array of mapped keys, where the key is mapped to a meta_key in the user meta table. 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_mapped_keys() {
        return apply_filters( 'charitable_donor_mapped_keys', array(            
            'email'         => 'user_email',
            'company'       => 'donor_company',
            'address'       => 'donor_address',
            'address_2'     => 'donor_address_2',
            'city'          => 'donor_city',
            'state'         => 'donor_state',
            'postcode'      => 'donor_postcode',
            'zip'           => 'donor_postcode',
            'country'       => 'donor_country',
            'phone'         => 'donor_phone',
        ) );
    }

    /**
     * Return the array of core keys. 
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_core_keys() {
        return array( 
            'ID',
            'user_pass',
            'user_login',
            'user_nicename',
            'user_url',
            'user_email',
            'display_name',
            'nickname',
            'first_name',
            'last_name',
            'rich_editing',
            'date_registered',
            'role',
            'jabber',
            'aim',
            'yim'
        );
    }
}

endif; // End class_exists check