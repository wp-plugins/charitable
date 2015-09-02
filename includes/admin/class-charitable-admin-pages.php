<?php
/**
 * This class is responsible for adding the Charitable admin pages.
 *
 * @package     Charitable/Classes/Charitable_Admin_Pages
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Admin_Pages' ) ) : 

/**
 * Charitable_Admin_Pages
 *
 * @since       1.0.0
 */
final class Charitable_Admin_Pages extends Charitable_Start_Object {

    /**
     * The page to use when registering sections and fields.
     *
     * @var     string 
     * @access  private
     */
    private $admin_menu_parent_page;

    /**
     * The capability required to view the admin menu. 
     *
     * @var     string
     * @access  private
     */
    private $admin_menu_capability;

    /**
     * Create class object.
     * 
     * @access  protected
     * @since   1.0.0
     */
    protected function __construct() {
        $this->admin_menu_capability = apply_filters( 'charitable_admin_menu_capability', 'manage_options' );
        $this->admin_menu_parent_page = 'charitable';

        add_action( 'admin_menu', array( $this, 'add_menu' ), 5 );
    }

    /**
     * Add Settings menu item under the Campaign menu tab.
     * 
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function add_menu() {
        add_menu_page( 'Charitable', 'Charitable', $this->admin_menu_capability, $this->admin_menu_parent_page, array( $this, 'render_charitable_settings_page' ) );        

        foreach ( $this->get_submenu_pages() as $page ) {
            if ( ! isset( $page[ 'page_title' ] ) 
                || ! isset( $page[ 'menu_title' ] ) 
                || ! isset( $page[ 'menu_slug' ] ) ) {
                continue;
            }

            $page_title = $page[ 'page_title' ];
            $menu_title = $page[ 'menu_title' ];
            $capability = isset( $page[ 'capability' ] ) ? $page[ 'capability' ] : $this->admin_menu_capability;
            $menu_slug = $page[ 'menu_slug' ];
            $function = isset( $page[ 'function' ] ) ? $page[ 'function' ] : '';

            add_submenu_page( 
                $this->admin_menu_parent_page, 
                $page_title, 
                $menu_title,
                $capability,
                $menu_slug, 
                $function
            );
        }

        remove_submenu_page( $this->admin_menu_parent_page, $this->admin_menu_parent_page );
    }

    /**
     * Returns an array with all the submenu pages. 
     *
     * @return  array
     * @access  private
     * @since   1.0.0
     */
    private function get_submenu_pages() {
        $campaign_post_type = get_post_type_object( 'campaign' );
        $donation_post_type = get_post_type_object( 'donation' );

        return apply_filters( 'charitable_submenu_pages', array(
            array( 
                'page_title'    => $campaign_post_type->labels->menu_name,
                'menu_title'    => $campaign_post_type->labels->menu_name,
                'menu_slug'     => 'edit.php?post_type=campaign'
            ), 
            array( 
                'page_title'    => $campaign_post_type->labels->add_new,
                'menu_title'    => $campaign_post_type->labels->add_new,
                'menu_slug'     => 'post-new.php?post_type=campaign'
            ), 
            array( 
                'page_title'    => $donation_post_type->labels->menu_name,
                'menu_title'    => $donation_post_type->labels->menu_name,
                'menu_slug'     => 'charitable-donations-table',
                'function'      => array( $this, 'render_donations_page' )
            ), 
            array( 
                'page_title'    => __( 'Campaign Categories', 'charitable' ),
                'menu_title'    => __( 'Categories', 'charitable' ),
                'menu_slug'     => 'edit-tags.php?taxonomy=campaign_category&post_type=campaign'
            ), 
            array( 
                'page_title'    => __( 'Campaign Tags', 'charitable' ),
                'menu_title'    => __( 'Tags', 'charitable' ),
                'menu_slug'     => 'edit-tags.php?taxonomy=campaign_tag&post_type=campaign'
            ), 
            array( 
                'page_title'    => __( 'Settings', 'charitable' ),
                'menu_title'    => __( 'Settings', 'charitable' ),
                'menu_slug'     => 'charitable-settings', 
                'function'      => array( $this, 'render_settings_page' )
            ), 
        ) );
    }

    /**
     * Display the Charitable settings page. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function render_settings_page() {
        charitable_admin_view( 'settings/settings' );
    }

    /**
     * Display the Charitable donations page. 
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function render_donations_page() {
        charitable_admin_view( 'donations-page' );
    }
}

endif; // End class_exists check