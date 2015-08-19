<?php
/**
 * The class that defines how campaigns are managed on the admin side.
 * 
 * @package     Charitable/Admin/Charitable_Campaign_Post_Type
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'Charitable_Campaign_Post_Type' ) ) : 

/**
 * Charitable_Campaign_Post_Type class.
 *
 * @final
 * @since       1.0.0
 */
final class Charitable_Campaign_Post_Type extends Charitable_Start_Object {

    /**
     * @var     Charitable_Meta_Box_Helper $meta_box_helper
     * @access  private
     */
    private $meta_box_helper;

    /**
     * Create object instance. 
     *
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {    
        $this->meta_box_helper = new Charitable_Meta_Box_Helper( 'charitable-campaign' );

        add_action( 'add_meta_boxes',                               array( $this, 'add_meta_boxes' ), 10);
        add_action( 'add_meta_boxes_campaign',                      array( $this, 'wrap_editor' ) );
        add_action( 'edit_form_after_title',                        array( $this, 'campaign_form_top' ) );
        add_action( 'save_post',                                    array( $this, 'save_post' ), 10, 2);
        add_action( 'charitable_campaign_donation_options_metabox', array( $this, 'campaign_donation_options_metabox' ));
        add_filter( 'enter_title_here',                             array( $this, 'campaign_enter_title' ), 10, 2 );
        add_filter( 'get_user_option_meta-box-order_campaign',      '__return_false' );
    }

    /**
     * Add meta boxes.
     * 
     * @see     add_meta_boxes hook
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function add_meta_boxes() {
        $meta_boxes = array(
            array( 
                'id'            => 'campaign-description', 
                'title'         => __( 'Campaign Description', 'charitable' ), 
                'context'       => 'campaign-top', 
                'priority'      => 'high', 
                'view'          => 'metaboxes/campaign-description'
            ),
            array( 
                'id'            => 'campaign-goal', 
                'title'         => __( 'Fundraising Goal ($)', 'charitable' ), 
                'context'       => 'campaign-top', 
                'priority'      => 'high', 
                'view'          => 'metaboxes/campaign-goal', 
                'description'   => __( 'Leave empty for campaigns without a fundraising goal.', 'charitable' )
            ),  
            array( 
                'id'            => 'campaign-end-date', 
                'title'         => __( 'End Date', 'charitable' ), 
                'context'       => 'campaign-top', 
                'priority'      => 'high', 
                'view'          => 'metaboxes/campaign-end-date', 
                'description'   => __( 'Leave empty for ongoing campaigns.', 'charitable' )
            ),
            array(
                'id'            => 'campaign-donation-options', 
                'title'         => __( 'Donation Options', 'charitable' ), 
                'context'       => 'campaign-advanced', 
                'priority'      => 'high', 
                'view'          => 'metaboxes/campaign-donation-options'
            ), 
            array(
                'id'            => 'campaign-extended-description', 
                'title'         => __( 'Extended Description', 'charitable' ), 
                'context'       => 'campaign-advanced', 
                'priority'      => 'high', 
                'view'          => 'metaboxes/campaign-extended-description'
            )
        );

        $meta_boxes = apply_filters( 'charitable_campaign_meta_boxes', $meta_boxes );

        foreach ( $meta_boxes as $meta_box ) {
            add_meta_box( 
                $meta_box['id'], 
                $meta_box['title'], 
                array( $this->meta_box_helper, 'metabox_display' ), 
                Charitable::CAMPAIGN_POST_TYPE, 
                $meta_box['context'], 
                $meta_box['priority'], 
                $meta_box
            );
        }
    }

    /**
     * Display fields at the very top of the page. 
     *
     * @param   WP_Post     $post
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function campaign_form_top( $post ) {
        if ( Charitable::CAMPAIGN_POST_TYPE == $post->post_type ) {
            do_meta_boxes( Charitable::CAMPAIGN_POST_TYPE, 'campaign-top', $post );
        }       
    }

    /**
     * Wrap elements around the main editor.
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function wrap_editor() {
        add_filter( 'edit_form_after_title', array( $this, 'advanced_campaign_settings' ), 20 );
    }

    /**
     * Wrap editor (and other advanced settings). 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function editor_wrap_before() {
        charitable_admin_view( 'metaboxes/campaign-advanced-wrap-before', array( 'meta_boxes' => $this->get_advanced_meta_boxes() ) );
    }

    /**
     * End wrapper around editor and other advanced settings. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function editor_wrap_after() {
        charitable_admin_view( 'metaboxes/campaign-advanced-wrap-after' );
    }

    /**
     * Display advanced campaign fields. 
     *
     * @param   WP_Post         $post
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function advanced_campaign_settings( $post ) {
        charitable_admin_view( 'metaboxes/campaign-advanced-settings', array( 'meta_boxes' => $this->get_advanced_meta_boxes() ) );
    }

    /**
     * Return flat array of meta boxes, ordered by priority.  
     *
     * @global  array       $wp_meta_boxes
     * @return  array
     * @access  private
     * @since   1.0.0
     */
    private function get_advanced_meta_boxes() {
        global $wp_meta_boxes;

        $meta_boxes = array();

        if ( ! isset( $wp_meta_boxes['campaign']['campaign-advanced'] ) ) {
            return $meta_boxes;
        }

        foreach ( array( 'high', 'sorted', 'core', 'default', 'low' ) as $priority ) {
            if ( isset( $wp_meta_boxes['campaign']['campaign-advanced'][$priority] ) ) {
                foreach ( (array) $wp_meta_boxes['campaign']['campaign-advanced'][$priority] as $box ) {
                    $meta_boxes[] = $box;
                }
            }
        }



        return $meta_boxes;
    }

    /**
     * Adds fields to the campaign donation options metabox. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function campaign_donation_options_metabox() {
        /** Get the array of fields to be displayed within the 
            campaign donations metabox. **/
        $fields = apply_filters( 'charitable_campaign_donation_options_fields', 
            array(
                'donations'     => array(
                    'priority'  => 4, 
                    'view'      => 'metaboxes/campaign-donation-options/suggested-amounts', 
                    'label'     => __( 'Suggested Donation Amounts', 'charitable' )
                ), 
                'permit_custom' => array(
                    'priority'  => 6, 
                    'view'      => 'metaboxes/campaign-donation-options/permit-custom', 
                    'label'     => __( 'Allow Custom Donations', 'charitable' ) 
                )
            ) 
        );

        $this->meta_box_helper->display_fields( $fields );
    }

    /**
     * Save meta for the campaign. 
     * 
     * @param   int         $post_id    Post ID.
     * @param   WP_Post     $post       Post object.
     * @return  void
     * @access  public 
     * @since   1.0.0
     */
    public function save_post( $post_id, WP_Post $post ) {
        if ( $this->meta_box_helper->user_can_save( $post ) ) {
                    
            $meta_keys = apply_filters( 'charitable_campaign_meta_keys', array(
                '_campaign_end_date', 
                '_campaign_goal', 
                '_campaign_suggested_donations',
                '_campaign_allow_custom_donations',
                '_campaign_description'
            ) );            

            $submitted = $_POST;

            foreach ( $meta_keys as $key ) {

                $value = isset( $submitted[ $key ] ) ? $submitted[ $key ] : false;

                $value = apply_filters( 'charitable_sanitize_campaign_meta', $value, $key, $submitted );

                update_post_meta( $post_id, $key, $value );

            }

            /* Hook for plugins to do something else with the posted data */
            do_action( 'charitable_campaign_save', $post );
        }
    }   

    /**
     * Sets the placeholder text of the campaign title field. 
     *
     * @param   string      $placeholder
     * @param   WP_Post     $post
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function campaign_enter_title( $placeholder, WP_Post $post ) {       
        if ( $post->post_type == 'campaign' ) {
            $placeholder = __( 'Enter campaign title', 'charitable' );
        }

        return $placeholder;
    }
}

endif; // End class_exists check