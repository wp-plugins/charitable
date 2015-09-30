<?php
/**
 * Plugin Name:         Charitable
 * Plugin URI:          https://wpcharitable.com
 * Description:         Fundraise with WordPress.
 * Version:             1.1.5
 * Author:              WP Charitable
 * Author URI:          https://wpcharitable.com
 * Requires at least:   4.1
 * Tested up to:        4.3
 *
 * Text Domain:         charitable
 * Domain Path:         /i18n/languages/
 *
 * @package             Charitable
 * @author              Eric Daams
 * @copyright           Copyright (c) 2015, Studio 164a
 * @license             http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable' ) ) :

/**
 * Main Charitable class
 *
 * @class       Charitable
 * @version     1.1.5
 */
class Charitable {

    /**
     * @var     string
     */
    const VERSION = '1.1.5';

    /**
     * @var     string      A date in the format: YYYYMMDD
     */
    const DB_VERSION = '20150615';  

    /**
     * @var     string      The Campaign post type.
     */
    const CAMPAIGN_POST_TYPE = 'campaign';

    /**
     * @var     string      The Donation post type.
     */
    const DONATION_POST_TYPE = 'donation';

    /**
     * @var     Charitable
     * @access  private
     */
    private static $instance = null;

    /**
     * @var     string
     * @access  private
     */
    private $textdomain = 'charitable';    

    /**
     * @var     string      Directory path for the plugin.
     * @access  private
     */
    private $directory_path;

    /**
     * @var     string      Directory url for the plugin.
     * @access  private
     */
    private $directory_url;

    /**
     * @var     string      Directory path for the includes folder of the plugin.
     * @access  private
     */
    private $includes_path;    

    /**
     * @var     string      Directory path for the admin folder of the plugin. 
     * @access  private
     */
    private $admin_path;

    /**
     * @var     string      Directory path for the assets folder. 
     * @access  private
     */
    private $assets_path;

    /**
     * @var     string      Directory path for the templates folder in themes.
     * @access  private
     */
    private $theme_template_path;    

    /**
     * @var     string      Directory path for the templates folder the plugin.
     * @access  private
     */
    private $plugin_template_path;        

    /**
     * @var     array       Store of registered objects.  
     * @access  private
     */
    private $registry;

    /**
     * Create class instance. 
     * 
     * @since   1.0.0
     */
    public function __construct() {
        $this->directory_path   = plugin_dir_path( __FILE__ );
        $this->directory_url    = plugin_dir_url( __FILE__ );
        $this->includes_path    = $this->directory_path . 'includes/';

        $this->load_dependencies();

        register_activation_hook( __FILE__, array( $this, 'activate') );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate') );

        add_action( 'plugins_loaded', array( $this, 'start' ), 1 );
    }

    /**
     * Returns the original instance of this class. 
     * 
     * @return  Charitable
     * @since   1.0.0
     */
    public static function get_instance() {
        return self::$instance;
    }

    /**
     * Run the startup sequence. 
     *
     * This is only ever executed once.  
     * 
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function start() {
        // If we've already started (i.e. run this function once before), do not pass go. 
        if ( $this->started() ) {
            return;
        }

        // Set static instance
        self::$instance = $this;        

        $this->maybe_upgrade();

        $this->attach_hooks_and_filters();

        $this->maybe_start_admin();      

        $this->maybe_start_public();        

        $this->maybe_start_ajax();

        Charitable_Addons::load( $this );
    }

    /**
     * Include necessary files.
     * 
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function load_dependencies() {
        $includes_path = $this->get_path( 'includes' );

        /* Abstracts */
        require_once( $includes_path . 'abstracts/class-charitable-form.php' );
        require_once( $includes_path . 'abstracts/class-charitable-query.php' );
        require_once( $includes_path . 'abstracts/class-charitable-start-object.php' );
        
        /* Functions & Core Classes */
        require_once( $includes_path . 'charitable-core-functions.php' );                
        require_once( $includes_path . 'charitable-utility-functions.php' );
        require_once( $includes_path . 'class-charitable-locations.php' );
        require_once( $includes_path . 'class-charitable-notices.php' );
        require_once( $includes_path . 'class-charitable-post-types.php' );
        require_once( $includes_path . 'class-charitable-request.php' );
        require_once( $includes_path . 'class-charitable-cron.php' );
        require_once( $includes_path . 'class-charitable-i18n.php' );
        
        /* Addons */
        require_once( $includes_path . 'addons/class-charitable-addons.php' );

        /* Campaigns */
        require_once( $includes_path . 'campaigns/charitable-campaign-functions.php' );
        require_once( $includes_path . 'campaigns/class-charitable-campaign.php' );
        require_once( $includes_path . 'campaigns/class-charitable-campaigns.php' );

        /* Currency */
        require_once( $includes_path . 'currency/charitable-currency-functions.php' );
        require_once( $includes_path . 'currency/class-charitable-currency.php' );

        /* Donations */                
        require_once( $includes_path . 'donations/interface-charitable-donation-form.php' );
        require_once( $includes_path . 'donations/class-charitable-donation-processor.php' );
        require_once( $includes_path . 'donations/class-charitable-donation.php' );
        require_once( $includes_path . 'donations/class-charitable-donations.php' );                
        require_once( $includes_path . 'donations/class-charitable-donation-form.php' );    
        require_once( $includes_path . 'donations/class-charitable-donation-amount-form.php' );
        require_once( $includes_path . 'donations/charitable-donation-hooks.php' );
        require_once( $includes_path . 'donations/charitable-donation-functions.php' );

        /* Users */
        require_once( $includes_path . 'users/charitable-user-functions.php' );
        require_once( $includes_path . 'users/charitable-user-hooks.php' );
        require_once( $includes_path . 'users/class-charitable-user.php' );
        require_once( $includes_path . 'users/class-charitable-roles.php' );
        require_once( $includes_path . 'users/class-charitable-donor.php' );     
        require_once( $includes_path . 'users/class-charitable-donor-query.php' );
        require_once( $includes_path . 'users/class-charitable-registration-form.php' );
        require_once( $includes_path . 'users/class-charitable-profile-form.php' );

        /* Gateways */
        require_once( $includes_path . 'gateways/class-charitable-gateways.php' );
        include_once( $includes_path . 'gateways/abstract-class-charitable-gateway.php' );
        include_once( $includes_path . 'gateways/class-charitable-gateway-offline.php' );
        include_once( $includes_path . 'gateways/class-charitable-gateway-paypal.php' );        

        /* Emails */
        include_once( $includes_path . 'emails/charitable-email-hooks.php' );
        require_once( $includes_path . 'emails/class-charitable-emails.php' ); 
        include_once( $includes_path . 'emails/abstract-class-charitable-email.php' );
        include_once( $includes_path . 'emails/class-charitable-email-new-donation.php' );
        include_once( $includes_path . 'emails/class-charitable-email-donation-receipt.php' );
        include_once( $includes_path . 'emails/class-charitable-email-campaign-end.php' );
            
        /* Database */
        require_once( $includes_path . 'db/abstract-class-charitable-db.php' );
        require_once( $includes_path . 'db/class-charitable-campaign-donations-db.php' );
        require_once( $includes_path . 'db/class-charitable-donors-db.php' );

        /* Licensing */
        require_once( $includes_path . 'licensing/class-charitable-licenses.php' );
        require_once( $includes_path . 'licensing/class-charitable-plugin-updater.php' );

        /* Public */
        require_once( $includes_path . 'public/charitable-page-functions.php' );
        require_once( $includes_path . 'public/charitable-template-functions.php' );
        require_once( $includes_path . 'public/charitable-template-hooks.php' );
        require_once( $includes_path . 'public/class-charitable-session.php' );        
        require_once( $includes_path . 'public/class-charitable-template.php' );      
        require_once( $includes_path . 'public/class-charitable-template-part.php' );
        require_once( $includes_path . 'public/class-charitable-templates.php' );
        require_once( $includes_path . 'public/class-charitable-ghost-page.php' );
        require_once( $includes_path . 'public/class-charitable-user-dashboard.php' );

        /* Shortcodes */
        require_once( $includes_path . 'shortcodes/class-charitable-shortcodes.php' );
        require_once( $includes_path . 'shortcodes/class-charitable-campaigns-shortcode.php' );
        require_once( $includes_path . 'shortcodes/class-charitable-my-donations-shortcode.php' );
        require_once( $includes_path . 'shortcodes/class-charitable-login-shortcode.php' );
        require_once( $includes_path . 'shortcodes/class-charitable-registration-shortcode.php' );
        require_once( $includes_path . 'shortcodes/class-charitable-profile-shortcode.php' );

        /* Widgets */
        require_once( $includes_path . 'widgets/class-charitable-widgets.php' );
        require_once( $includes_path . 'widgets/class-charitable-campaign-terms-widget.php' );
        require_once( $includes_path . 'widgets/class-charitable-campaigns-widget.php' );
        require_once( $includes_path . 'widgets/class-charitable-donors-widget.php' );
        require_once( $includes_path . 'widgets/class-charitable-donate-widget.php' );
        require_once( $includes_path . 'widgets/class-charitable-donation-stats-widget.php' );

        /* Deprecated */
        require_once( $includes_path . 'deprecated/charitable-deprecated-functions.php' );
    }

    /**
     * Set up hook and filter callback functions.
     * 
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function attach_hooks_and_filters() {
        add_action('plugins_loaded', array( $this, 'charitable_install' ), 100 );
        add_action('plugins_loaded', array( $this, 'charitable_start' ), 100 );
        add_action('charitable_start', array( 'Charitable_Licenses', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_Post_Types', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_Widgets', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_Gateways', 'charitable_start' ), 3 ); 
        add_action('charitable_start', array( 'Charitable_Emails', 'charitable_start' ), 3 ); 
        add_action('charitable_start', array( 'Charitable_Request', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_Shortcodes', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_User_Dashboard', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_Cron', 'charitable_start' ), 3 );
        add_action('charitable_start', array( 'Charitable_i18n', 'charitable_start' ), 3 );

        /**
         * We do this on priority 20 so that any functionality that is loaded on init (such 
         * as addons) has a chance to run before the event.
         */
        add_action('init', array( $this, 'do_charitable_actions' ), 20 );        

        add_filter('charitable_sanitize_campaign_meta', array( 'Charitable_Campaign', 'sanitize_meta' ), 10, 3 );
        add_filter('charitable_sanitize_donation_meta', array( 'Charitable_Donation', 'sanitize_meta' ), 10, 2 );
        add_filter('charitable_after_insert_user', array( 'Charitable_User', 'signon' ), 10, 2 );
    }

    /**
     * Checks whether we're in the admin area and if so, loads the admin-only functionality.
     *
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function maybe_start_admin() {
        if ( ! is_admin() ) {
            return;
        }

        require_once( $this->get_path( 'admin' ) . 'class-charitable-admin.php' );

        add_action('charitable_start', array( 'Charitable_Admin', 'charitable_start' ), 3 );
    }

    /**
     * Checks whether we're on the public-facing side and if so, loads the public-facing functionality.
     *
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function maybe_start_public() {
        if ( is_admin() ) {
            return;
        }

        require_once( $this->get_path( 'public' ) . 'class-charitable-public.php' );

        add_action('charitable_start', array( 'Charitable_Public', 'charitable_start' ), 3 );
    }

    /**
     * Checks whether we're executing an AJAX hook and if so, loads some AJAX functionality. 
     *
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function maybe_start_ajax() {
        if ( false === ( defined('DOING_AJAX') && DOING_AJAX ) ) {
            return;
        }

        add_action('charitable_start', array( 'Charitable_Session', 'charitable_start' ), 1 );
    }

    /**
     * This method is fired after all plugins are loaded and simply fires the charitable_start hook.
     *
     * Extensions can use the charitable_start event to load their own functionality.
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function charitable_start() {        
        do_action( 'charitable_start', $this );     
    }

    /**
     * Fires off an action right after Charitable is installed, allowing other 
     * plugins/themes to do something at this point. 
     *
     * @return  void
     * @access  public
     * @since   1.0.1
     */
    public function charitable_install() {
        $install = get_transient( 'charitable_install' );        

        if ( ! $install ) {
            return;
        }

        do_action( 'charitable_install' );

        delete_transient( 'charitable_install' );
    }

    /**
     * Returns whether we are currently in the start phase of the plugin. 
     *
     * @return  bool
     * @access  public
     * @since   1.0.0
     */
    public function is_start() {
        return current_filter() == 'charitable_start';
    }

    /**
     * Returns whether the plugin has already started.
     * 
     * @return  bool
     * @access  public
     * @since   1.0.0
     */
    public function started() {
        return did_action( 'charitable_start' ) || current_filter() == 'charitable_start';
    }

    /**
     * Returns whether the plugin is being activated. 
     *
     * @return  bool
     * @access  public
     * @since   1.0.0
     */
    public function is_activation() {
        return current_filter() == 'activate_charitable/charitable.php';
    }

    /**
     * Returns whether the plugin is being deactivated.
     *
     * @return  bool
     * @access  public
     * @since   1.0.0
     */
    public function is_deactivation() {
        return current_filter() == 'deactivate_charitable/charitable.php';
    }

    /**
     * Stores an object in the plugin's registry.
     *
     * @param   mixed $object
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function register_object($object) {
        if ( ! is_object( $object ) ) {
            return;
        }

        $class = get_class( $object );

        $this->registry[$class] = $object;
    }

    /**
     * Returns a registered object.
     * 
     * @param   string      $class          The type of class you want to retrieve.
     * @return  mixed       The object if its registered. Otherwise false.
     * @access  public
     * @since   1.0.0
     */
    public function get_registered_object($class) {
        return isset( $this->registry[$class] ) ? $this->registry[$class] : false;
    }

    /**
     * Returns plugin paths. 
     *
     * @param   string      $type           If empty, returns the path to the plugin.
     * @param   bool        $absolute_path  If true, returns the file system path. If false, returns it as a URL.
     * @return  string
     * @since   1.0.0
     */
    public function get_path($type = '', $absolute_path = true ) {      
        $base = $absolute_path ? $this->directory_path : $this->directory_url;

        switch( $type ) {
            case 'includes' : 
                $path = $base . 'includes/';
                break;

            case 'admin' :
                $path = $base . 'includes/admin/';
                break;

            case 'public' : 
                $path = $base . 'includes/public/';
                break;

            case 'assets' : 
                $path = $base . 'assets/';
                break;

            case 'templates' : 
                $path = $base . 'templates/';
                break;

            case 'directory' : 
                $path = $base;
                break;

            default :
                $path = __FILE__;
        }

        return $path;
    }

    /**
     * Returns the plugin's version number. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_version() {
        return self::VERSION;
    }

    /**
     * Returns the public class. 
     *
     * @return  Charitable_Public
     * @access  public
     * @since   1.0.0
     */
    public function get_public() {
        return $this->get_registered_object( 'Charitable_Public' );
    }

    /**
     * Returns the admin class. 
     *
     * @return  Charitable_Admin
     * @access  public
     * @since   1.0.0
     */
    public function get_admin() {
        return $this->get_registered_object( 'Charitable_Admin' );
    }

    /**
     * Returns the location helper. 
     *
     * @return  Charitable_Locations
     * @access  public
     * @since   1.0.0
     */
    public function get_location_helper() {
        $location_helper = $this->get_registered_object('Charitable_Locations');

        if ( false === $location_helper ) {
            $location_helper = new Charitable_Locations();
            $this->register_object( $location_helper );
        }

        return $location_helper;
    }

    /**
     * Return the current request object. 
     *
     * @return  Charitable_Request
     * @access  public
     * @since   1.0.0
     */
    public function get_request() {
        $request = $this->get_registered_object('Charitable_Request');

        if ( $request === false ) {
            $request = new Charitable_Request();
            $this->register_object( $request );
        }

        return $request;
    }

    /**
     * Return an instance of the currency helper. 
     *
     * @return  Charitable_Currency
     * @access  public
     * @since   1.0.0
     */
    public function get_currency_helper() {
        $currency_helper = $this->get_registered_object('Charitable_Currency');

        if ( false === $currency_helper ) {
            $currency_helper = new Charitable_Currency();
            $this->register_object( $currency_helper );
        }

        return $currency_helper;
    }

    /**
     * Returns the model for one of Charitable's database tables. 
     *
     * @param   string          $table
     * @return  Charitable_DB
     * @access  public
     * @since   1.0.0
     */
    public function get_db_table( $table ) {
        $tables = $this->get_tables();

        if ( ! isset( $tables[ $table ] ) ) {
            _doing_it_wrong( __METHOD__, sprintf( 'Invalid table %s passed', $table ), '1.0.0' );
            return null;
        }

        $class_name = $tables[ $table ];

        $db_table = $this->get_registered_object( $class_name );

        if ( false === $db_table ) {
            $db_table = new $class_name;
            $this->register_object( $db_table );
        }

        return $db_table;
    }

    /**
     * Return the filtered list of registered tables. 
     *
     * @return  string[]
     * @access  private
     * @since   1.0.0
     */
    private function get_tables() {
        $default_tables = array(
            'campaign_donations' => 'Charitable_Campaign_Donations_DB', 
            'donors'             => 'Charitable_Donors_DB'   
        );
        
        return apply_filters( 'charitable_db_tables', $default_tables );
    }

    /**
     * Runs on plugin activation. 
     *
     * @see register_activation_hook
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function activate() {
        require_once( $this->get_path( 'includes' ) . 'class-charitable-install.php' );
        new Charitable_Install();
    }

    /**
     * Runs on plugin deactivation. 
     *
     * @see     register_deactivation_hook
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function deactivate() {
        require_once( $this->get_path( 'includes' ) . 'class-charitable-uninstall.php' );
        new Charitable_Uninstall();
    }

    /**
     * Perform upgrade routine if necessary. 
     *
     * @return  void
     * @access  private
     * @since   1.0.0
     */
    private function maybe_upgrade() {
        $db_version = get_option( 'charitable_version' );

        if ( $db_version !== self::VERSION ) {      

            require_once( $this->get_path( 'includes' ) . 'class-charitable-upgrade.php' );

            Charitable_Upgrade::upgrade_from( $db_version, self::VERSION );
        }
    }

    /**
     * If a charitable_action event is triggered, delegate the event using do_action.     
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function do_charitable_actions() {
        if ( isset( $_REQUEST['charitable_action'] ) ) {

            $action = $_REQUEST[ 'charitable_action' ];
            
            do_action( 'charitable_' . $action, 20 );
        }
    }

    /**
     * Throw error on object clone. 
     *
     * This class is specifically designed to be instantiated once. You can retrieve the instance using charitable()
     *
     * @since   1.0.0
     * @access  public
     * @return  void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'charitable' ), '1.0.0' );
    }

    /**
     * Disable unserializing of the class. 
     *
     * @since   1.0.0
     * @access  public
     * @return  void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'charitable' ), '1.0.0' );
    }   
}

$charitable = new Charitable();

endif; // End if class_exists check