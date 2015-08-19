<?php
/**
 * A helper class to query donation data.
 *
 * @package     Charitable/Classes/Charitable_Donations_Query
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Donations_Query' ) ) : 

/**
 * Charitable_Donations_Query
 *
 * @since       1.0.0
 */
class Charitable_Donations_Query {

    /**
     * Create class object.
     * 
     * @access  public
     * @since   1.0.0
     */
    public function __construct( $args = array() ) {
        $defaults = array(
            'output'          => 'donations', // Use 'posts' to get standard post objects
            'post_type'       => array( 'donation' ),
            'start_date'      => false,
            'end_date'        => false,
            'number'          => 20,
            'page'            => null,
            'orderby'         => 'ID',
            'order'           => 'DESC',
            'donor'           => null,
            'meta_key'        => null,
            'year'            => null,
            'month'           => null,
            'day'             => null,
            's'               => null,
            'children'        => false,
            'fields'          => null,
            'campaign'        => null
        );    

        // 'post_type'      => array( 'donation' ),
        // 'posts_per_page' => get_option( 'posts_per_page' )

        $this->args = wp_parse_args( $args, $defaults );

        $this->init();
    }
}

endif; // End class_exists check