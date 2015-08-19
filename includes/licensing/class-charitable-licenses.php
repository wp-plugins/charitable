<?php
/**
 * Class to assist with the setup of extension licenses.
 *
 * @package     Charitable/Classes/Charitable_Licenses
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Licenses' ) ) : 

/**
 * Charitable_Licenses
 *
 * @since       1.0.0
 */
class Charitable_Licenses extends Charitable_Start_Object {

    /**
     * All the registered products requiring licensing. 
     *
     * @var     array
     * @access  private
     */
    private $products;

    /**
     * All the stored licenses.
     *
     * @var     array
     * @access  private
     */
    private $licenses;

    /**
     * Create class object.
     *
     * Note that the only way to instantiate an object is with the charitable_start method, 
     * which can only be called during the start phase. In other words, don't try 
     * to instantiate this object. 
     * 
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {
        $this->products = array();

        $this->attach_hooks_and_filters();
    }

    /**
     * Attach callbacks to hooks and filters.  
     *
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function attach_hooks_and_filters() {        
        add_action( 'admin_init', array( $this, 'update_products' ), 0 );
        add_action( 'charitable_deactivate_license', array( $this, 'deactivate_license' ) );
    }   

    /**
     * Check for updates for any licensed products. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function update_products() {
        foreach ( $this->get_licenses() as $product_key => $license_details ) {

            if ( empty( $license_details ) ) {
                continue;
            }

            $product = $this->get_product_license_details( $product_key );
            $license = trim( $license_details[ 'license' ] );

            new Charitable_Plugin_Updater( $product[ 'url' ], $product[ 'file' ], array(
                'version'   => $product[ 'version' ],
                'license'   => $license,
                'item_name' => $product[ 'name' ],
                'author'    => $product[ 'author' ]
            ) );
        }
    }

    /**
     * Register a product that requires licensing. 
     *
     * @param   string $item_name
     * @param   string $author
     * @param   string $version
     * @param   string $url
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function register_licensed_product( $item_name, $author, $version, $file, $url = 'http://wpcharitable.com' ) {
        $this->products[ $this->get_item_key( $item_name ) ] = array(
            'name'      => $item_name, 
            'author'    => $author, 
            'version'   => $version,
            'url'       => $url, 
            'file'      => $file
        );    
    }

    /**
     * Return the list of products requiring licensing. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_products() {
        return $this->products;
    }

    /**
     * Return a specific product's licensing details. 
     *
     * @return  string[]
     * @access  public
     * @since   1.0.0
     */
    public function get_product_license_details( $item ) {
        return isset( $this->products[ $item ] ) ? $this->products[ $item ] : false;
    }

    /**
     * Returns whether the given product has a valid license.
     *
     * @param   string $key
     * @return  boolean
     * @access  public
     * @since   1.0.0
     */
    public function has_valid_license( $item ) {
        $license = $this->get_license_details( $item );

        if ( ! $license || ! isset( $license[ 'valid' ] ) ) {
            return false;
        }

        return $license[ 'valid' ];
    }

    /**
     * Returns the license details for the given product.
     *
     * @param   string $key
     * @return  mixed[]
     * @access  public
     * @since   1.0.0
     */
    public function get_license( $item ) {
        $license = $this->get_license_details( $item );

        if ( ! $license ) {
            return false;
        }

        return $license[ 'license' ];
    }

    /**
     * Returns the license details for the given product.
     *
     * @param   string $key
     * @return  mixed[]
     * @access  public
     * @since   1.0.0
     */
    public function get_license_details( $item ) {
        $licenses = $this->get_licenses();

        if ( ! isset( $licenses[ $item ] ) ) {
            return false;
        }

        return $licenses[ $item ];
    }

    /**
     * Return the list of licenses. 
     *
     * Note: The licenses are not necessarily valid. If a user enters an invalid
     * license, the license will be stored but it will be flagged as invalid. 
     *
     * @return  array[]
     * @access  public
     * @since   1.0.0
     */
    public function get_licenses() {
        if ( ! isset( $this->licenses ) ) {
            $this->licenses = charitable_get_option( 'licenses', array() );
        }

        return $this->licenses;
    }

    /**
     * Verify a license.
     *
     * @param   string $key
     * @param   string $license
     * @return  mixed[]
     * @access  public
     * @since   1.0.0
     */
    public function verify_license( $key, $license ) {
        $license = trim( $license );

        if ( $license == $this->get_license( $key ) ) {
            return $this->get_license_details( $key );
        }

        $product_details = $this->get_product_license_details( $key );

        /* This product was not correctly registered. */
        if ( ! $product_details ) {
            return;
        }    

        /* Data to send in our API request */
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license'   => $license,
            'item_name' => urlencode( $product_details[ 'name' ] ),
            'url'       => home_url()
        );

        /* Call the custom API */
        $response = wp_remote_post( $product_details[ 'url' ], array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        /* Make sure the response came back okay */
        if ( is_wp_error( $response ) ) {
            return;
        }

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        return array(
            'license'           => $license,
            'expiration_date'   => $license_data->expires, 
            'valid'             => 'valid' == $license_data->license
        );
    }

    /**
     * Return the URL to deactivate a specific license. 
     *
     * @param   string $product_key
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_license_deactivation_url( $product_key ) {
        return esc_url( add_query_arg( array(
            'charitable_action' => 'deactivate_license',
            'product_key'       => $product_key, 
            '_nonce'            => wp_create_nonce( 'license' )
        ), admin_url( 'admin.php?page=charitable-settings&tab=licenses' ) ) );
    }

    /**
     * Deactivate a license. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function deactivate_license() {
        if ( ! wp_verify_nonce( $_REQUEST[ '_nonce' ], 'license' ) ) {
            wp_die( __( 'Cheatin\' eh?!', 'charitable' ) );
        }

        $product_key = isset( $_REQUEST[ 'product_key' ] ) ? $_REQUEST[ 'product_key' ] : false;

        /* Product key must be set */
        if ( false === $product_key ) {
            wp_die( __( 'Missing product key', 'charitable' ) );
        }       

        $product = $this->get_product_license_details( $product_key );        

        /* Make sure we have a valid product with a valid license. */
        if ( ! $product || ! $this->has_valid_license( $product_key ) ) {
            wp_die( __( 'This product is not valid or does not have a valid license key.', 'charitable' ) );
        }

        $license = $this->get_license( $product_key );

        /* Data to send to wpcharitable.com to deactivate the license. */
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'license'   => $license,
            'item_name' => urlencode( $product[ 'name' ] ),
            'url'       => home_url()
        );

        /* Call the custom API. */
        $response = wp_remote_post( $product[ 'url' ], array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        /* Make sure the response came back okay */
        if ( is_wp_error( $response ) ) {
            return false;
        }

        /* Decode the license data */
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        $settings = get_option( 'charitable_settings' );

        unset( $settings[ 'licenses' ][ $product_key ] );

        update_option( 'charitable_settings', $settings );
    }

    /**
     * Return a key for the item, based on the item name. 
     *
     * @param   string $item_name
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_item_key( $item_name ) {
        return strtolower( str_replace( ' ', '_', $item_name ) );
    }
}

endif; // End class_exists check