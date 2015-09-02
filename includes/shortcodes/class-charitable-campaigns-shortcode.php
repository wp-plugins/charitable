<?php
/**
 * Campaigns shortcode class.
 * 
 * @version     1.0.0
 * @package     Charitable/Shortcodes/Campaigns
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Campaigns_Shortcode' ) ) : 

/**
 * Charitable_Campaigns_Shortcode class. 
 *
 * @since       1.0.0
 */
class Charitable_Campaigns_Shortcode {

    /**
     * The callback method for the campaigns shortcode.
     *
     * This receives the user-defined attributes and passes the logic off to the class. 
     *
     * @param   array       $atts       User-defined shortcode attributes.
     * @return  string
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function display( $atts ) {
        $default = array(
            'orderby' => 'post_date',
            'number' => get_option( 'posts_per_page' ), 
            'category' => '',
            'creator' => '', 
            'exclude' => '',
            'include_inactive' => false,
            'columns' => 2
        );

        $args = shortcode_atts( $default, $atts, 'campaigns' );
        $args[ 'campaigns' ] = self::get_campaigns( $args );

        /* Allows extensions/themes to plug in their own template objects here. */
        $template = apply_filters( 'charitable_campaigns_shortcode_template', false, $args );

        /* Fall back to default Charitable_Template if no template returned or if template was not object of 'Charitable_Template' class. */
        if ( ! is_object( $template ) || ! is_a( $template, 'Charitable_Template' ) ) {
            $template = new Charitable_Template( 'campaign-loop.php', false );
        }

        if ( ! $template->template_file_exists() ) {
            return false;
        }

        $view_args = apply_filters( 'charitable_campaigns_shortcode_view_args', array(
            'campaigns' => $args[ 'campaigns' ],
            'columns'   => $args[ 'columns' ]
        ), $args );

        $template->set_view_args( $view_args );

        ob_start();        

        $template->render();

        return apply_filters( 'charitable_campaigns_shortcode', ob_get_clean(), $args );
    }

    /**
     * Return campaigns to display in the campaigns shortcode. 
     *
     * @param   array   $args
     * @return  WP_Query
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function get_campaigns( $args ) {
        $query_args = array(
            'posts_per_page' => $args[ 'number' ]
        );

        /* Pagination */
        if ( ! empty( $args[ 'paged' ] ) ) {
            $query_args[ 'paged' ] = $args[ 'paged' ];
        }

        /* Set category constraint */
        if ( ! empty( $args[ 'category' ] ) ) {
            $query_args[ 'tax_query' ] = array(
                array(
                    'taxonomy'  => 'campaign_category',
                    'field'     => 'slug',
                    'terms'     => $args[ 'category' ]
                )
            );
        }

        /* Set author constraint */
        if ( ! empty( $args[ 'creator' ] ) ) {
            $query_args[ 'author' ] = $args[ 'creator' ];
        }

        /* Only include active campaigns if flag is set */
        if ( ! $args[ 'include_inactive' ] ) {
            $query_args[ 'meta_query' ] = array(
                'relation' => 'OR',
                array(
                    'key'       => '_campaign_end_date',
                    'value'     => date( 'Y-m-d H:i:s' ),
                    'compare'   => '>=',
                    'type'      => 'datetime'
                ), 
                array(
                    'key'       => '_campaign_end_date',
                    'value'     => 0,
                    'compare'   => '='
                )
            );
        }

        if ( ! empty( $args[ 'exclude' ] ) ) {
            $query_args[ 'post__not_in' ] = explode( ',', $args[ 'exclude' ] );
        }

        /* Return campaigns, ordered by date of creation. */
        if ( 'post_date' == $args[ 'orderby' ] ) {
            $query_args[ 'orderby' ] = 'date';
            $query_args[ 'order' ] = 'DESC';
            return Charitable_Campaigns::query( $query_args );
        }

        /* Return campaigns, ordered by how much money has been raised. */
        if ( 'popular' == $args[ 'orderby' ] ) {
            return Charitable_Campaigns::ordered_by_amount( $query_args );
        }

        /* Return campaigns, ordered by how soon they are ending. */
        return Charitable_Campaigns::ordered_by_ending_soon( $query_args );
    }

}

endif;