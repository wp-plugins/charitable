<?php
/**
 * Email model
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Email
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Email' ) ) : 

/**
 * Charitable_Email
 *
 * @abstract
 * @since       1.0.0
 */
abstract class Charitable_Email {    

    /**
     * @var     string  The email's unique identifier.
     */
    const ID = '';

    /**
     * @var     string  Descriptive name of the email.
     * @access  protected
     * @since   1.0.0
     */
    protected $name;

    /**
     * @var     string[] Array of supported object types (campaigns, donations, donors, etc).
     * @access  protected
     * @since   1.0.0
     */
    protected $object_types = array();

    /**
     * @var     boolean Whether the email allows you to define the email recipients.
     * @access  protected
     * @since   1.0.0
     */
    protected $has_recipient_field = false;

    /**
     * @var     Charitable_Donation
     */
    protected $donation;

    /**
     * @var     Charitable_Campaign
     */
    protected $campaign;

    /**
     * @var     string
     * @access  protected
     */
    protected $recipients;

    /**
     * Create a class instance. 
     *
     * @param   mixed[]  $objects
     * @access  public
     * @since   1.0.0
     */
    public function __construct( $objects = array() ) {
        $this->donation = isset( $objects[ 'donation' ] ) ? $objects[ 'donation' ] : null;
        $this->campaign = isset( $objects[ 'campaign' ] ) ? $objects[ 'campaign' ] : null;

        if ( $this->has_recipient_field ) {
            add_filter( 'charitable_settings_fields_emails_email_' . $this::ID, array( $this, 'add_recipients_field' ) );
        }

        if ( in_array( 'donation', $this->object_types ) ) {
            add_filter( 'charitable_email_content_fields', array( $this, 'add_donation_content_fields' ), 10, 2 );
            add_filter( 'charitable_email_preview_content_fields', array( $this, 'add_preview_donation_content_fields' ), 10, 2 );
        }

        if ( in_array( 'campaign', $this->object_types ) ) {
            add_filter( 'charitable_email_content_fields', array( $this, 'add_campaign_content_fields' ), 10, 2 );
            add_filter( 'charitable_email_preview_content_fields', array( $this, 'add_preview_campaign_content_fields' ), 10, 2 );   
        }
    }

    /**
     * Checks whether this email is enabled. 
     *
     * @return  boolean
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function is_enabled() {
        return charitable_get_helper( 'emails' )->is_enabled_email( self::ID );
    }

    /**
     * Return the email name.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get from name for email. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_from_name() {
        return wp_specialchars_decode( charitable_get_option( 'email_from_name', get_option('blogname') ) );
    }

    /**
     * Get from address for email. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_from_address() {
        return charitable_get_option( 'email_from_address', get_option('admin_email') );
    }

    /**
     * Return the email recipients. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_recipient() {
        return $this->get_option( 'recipient', $this->get_default_recipient() );
    }

    /**
     * Return the email subject line. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_subject() {
        return $this->get_option( 'subject', $this->get_default_subject() );
    }

    /**
     * Get the email content type
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_content_type() {
        return apply_filters( 'charitable_email_content_type', 'text/html', $this );
    }

    /**
     * Get the email headers.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_headers() {
        if ( ! isset( $this->headers ) ) {
            $this->headers  = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
            $this->headers .= "Reply-To: {$this->get_from_address()}\r\n";
            $this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
        }

        return apply_filters( 'charitable_email_headers', $this->headers, $this );
    }

    /**
     * Return the value of a specific field to be displayed in the email. 
     *
     * This is used by Charitable_Emails::email_shortcode() to obtain the value of the
     * particular field that was referenced in the shortcode. The second argument is
     * an optional array of arguments.
     *
     * @param   string  $field
     * @param   array   $args   Optional. May contain additional arguments. 
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_value( $field, $args = array() ) {
        $fields = $this->get_fields();

        if ( ! isset( $fields[ $field ] ) ) {
            return '';
        }

        if ( isset( $args[ 'preview' ] ) && $args[ 'preview' ] ) {
            return $this->get_preview_field_content( $field );
        }

        add_filter( 'charitable_email_content_field_value_' . $field, $fields[ $field ][ 'callback' ], 10, 3 );

        return apply_filters( 'charitable_email_content_field_value_' . $field, '', $field, $args );
    }

    /**
     * Returns all fields that can be displayed using the [charitable_email] shortcode.
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function get_fields() {
        return apply_filters( 'charitable_email_content_fields', array(
            'site_name'     => array( 
                'description'   => __( 'Your website title', 'charitable' ), 
                'callback'      => array( $this, 'get_site_name' )
            ), 
            'site_url'      => array(
                'description'   => __( 'Your website URL', 'charitable' ), 
                'callback'      => home_url()
            )            
        ), $this );
    }

    /**
     * Return the site/blog name. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_site_name() {
        return get_option( 'blogname' );
    }

    /**
     * Register email settings. 
     *
     * @param   array   $settings
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function email_settings( $settings ) {
        $email_settings = apply_filters( 'charitable_settings_fields_emails_email_' . $this::ID, array(
            'section_email' => array(
                'type'      => 'heading',
                'title'     => $this->get_name(),
                'priority'  => 2
            ),            
            'subject' => array(
                'type'      => 'text',
                'title'     => __( 'Email Subject Line', 'charitable' ), 
                'help'      => __( 'The email subject line when it is delivered to recipients.', 'charitable' ),
                'priority'  => 6, 
                'class'     => 'wide', 
                'default'   => $this->get_default_subject()
            ), 
            'headline' => array(
                'type'      => 'text',
                'title'     => __( 'Email Headline', 'charitable' ), 
                'help'      => __( 'The headline displayed at the top of the email.', 'charitable' ),
                'priority'  => 10, 
                'class'     => 'wide', 
                'default'   => $this->get_default_headline()
            ), 
            'body' => array(
                'type'      => 'editor',
                'title'     => __( 'Email Body', 'charitable' ), 
                'help'      => sprintf( '%s <div class="charitable-shortcode-options">%s</div>', 
                    __( 'The content of the email that will be delivered to recipients. HTML is accepted.', 'charitable' ),
                    $this->get_shortcode_options()
                ), 
                'priority'  => 14, 
                'default'   => $this->get_default_body()
            ), 
            'preview' => array(
                'type'      => 'content',
                'title'     => __( 'Preview', 'charitable' ),
                'content'   => sprintf( '<a href="%s" title="%s" target="_blank" class="button">%s</a>', 
                    esc_url( 
                        add_query_arg( array( 
                            'charitable_action' => 'preview_email',
                            'email_id' => $this::ID
                        ), site_url() ) 
                    ), 
                    __( 'Preview email in your browser', 'charitable' ),
                    __( 'Preview email', 'charitable' )
                ),
                'priority'  => 18
            )
        ) );

        return wp_parse_args( $settings, $email_settings );
    } 

    /**
     * Add recipient field
     *
     * @param   array   $settings
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function add_recipients_field( $settings ) {
        $settings[ 'recipient' ] = array(
            'type'      => 'text',
            'title'     => __( 'Recipients', 'charitable' ), 
            'help'      => __( 'A comma-separated list of email address that will receive this email.', 'charitable' ),
            'priority'  => 4, 
            'class'     => 'wide', 
            'default'   => $this->get_default_recipient()
        );

        return $settings;        
    }

    /**
     * Add donation content fields.   
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function add_donation_content_fields( $fields ) {   
        $fields[ 'donor' ] = array(
            'description'   => __( 'The full name of the donor', 'charitable' ),
            'callback'      => array( $this, 'get_donor_full_name' )
        );
         
        $fields[ 'donor_first_name' ] = array(
            'description'   => __( 'The first name of the donor', 'charitable' ), 
            'callback'      => array( $this, 'get_donor_first_name' )
        );

        $fields[ 'donor_email' ] = array(
            'description'   => __( 'The email address of the donor', 'charitable' ),
            'callback'      => array( $this, 'get_donor_email' )
        );

        $fields[ 'donation_id' ] = array(
            'description'   => __( 'The donation ID', 'charitable' ), 
            'callback'      => array( $this, 'get_donation_id' )
        );

        $fields[ 'donation_summary' ] = array(
            'description'   => __( 'A summary of the donation', 'charitable' ), 
            'callback'      => array( $this, 'get_donation_summary' )
        );

        return $fields;
    }

    /**
     * Return the first name of the donor. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_donor_first_name() {        
        return $this->return_value_if_has_valid_donation( $this->donation->get_donor()->first_name );
    }

    /**
     * Return the full name of the donor. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_donor_full_name() {
        return $this->return_value_if_has_valid_donation( $this->donation->get_donor()->get_name() );
    }

    /**
     * Return the full name of the donor. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_donor_email() {
        return $this->return_value_if_has_valid_donation( $this->donation->get_donor()->get_email() );
    }

    /**
     * Returns the donation ID. 
     *
     * @return  int
     * @access  public
     * @since   1.0.0
     */
    public function get_donation_id() {
        return $this->return_value_if_has_valid_donation( $this->donation->get_donation_id() );
    }

    /**
     * Returns a summary of the donation, including all the campaigns that were donated to.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_donation_summary() {
        if ( ! $this->has_valid_donation() ) {
            return '';            
        }

        $output = "";

        foreach ( $this->donation->get_campaign_donations() as $campaign_donation ) {
            $line_item = sprintf( '%s: %s%s', $campaign_donation->campaign_name, charitable_get_currency_helper()->get_monetary_amount( $campaign_donation->amount ), PHP_EOL );
            $output .= apply_filters( 'charitable_donation_summary_line_item_email', $line_item, $campaign_donation );
        }

        return $output;
    }

    /**
     * Add donation content fields' fake data for previews.
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function add_preview_donation_content_fields( $fields ) {
        $fields[ 'donor_first_name' ]   = 'John';
        $fields[ 'donor_full_name' ]    = 'John Deere';
        return $fields;
    }

    /**
     * Add campaign content fields.   
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function add_campaign_content_fields( $fields ) {
        $fields[ 'campaign_title' ] = array(
            'description'   => __( 'The title of the campaign', 'charitable' ), 
            'callback'      => array( $this, 'get_campaign_title' )
        );

        $fields[ 'campaign_creator' ] = array(
            'description'   => __( 'The name of the campaign creator', 'charitable' ), 
            'callback'      => array( $this, 'get_campaign_creator' )
        );

        $fields[ 'campaign_creator_email' ] = array(
            'description'   => __( 'The email address of the campaign creator', 'charitable' ), 
            'callback'      => array( $this, 'get_campaign_creator_email' )
        );

        return $fields;
    }    

    /**
     * Return the campaign creator's name.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign_title() {
        if ( ! $this->has_valid_campaign() ) {
            return '';            
        }

        return $this->campaign->post_title;
    }

    /**
     * Return the campaign creator's name.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign_creator() {
        if ( ! $this->has_valid_campaign() ) {
            return '';            
        }

        return get_the_author_meta( 'display_name', $this->campaign->get_campaign_creator() );
    }

    /**
     * Return the campaign creator's email address.  
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_campaign_creator_email() {
        if ( ! $this->has_valid_campaign() ) {
            return '';            
        }

        return get_the_author_meta( 'user_email', $this->campaign->get_campaign_creator() );
    }

    /**
     * Add campaign content fields' fake data for previews.
     *
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function add_preview_campaign_content_fields( $fields ) {        
        return $fields;
    }

    /**
     * Sends the email.
     *
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function send() {            
        do_action( 'charitable_before_send_email', $this );        

        wp_mail( 
            $this->get_recipient(),
            do_shortcode( $this->get_subject() ),
            $this->build_email(),
            $this->get_headers()
        );

        do_action( 'charitable_after_send_email', $this );
    }

    /**
     * Preview the email. This will display a sample email within the browser. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function preview() {
        add_filter( 'shortcode_atts_charitable_email', array( $this, 'set_preview_mode' ) );

        do_action( 'charitable_before_preview_email', $this );        

        return $this->build_email();
    }

    /**
     * Set preview mode in the shortcode attributes. 
     *
     * @param   array   $atts
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function set_preview_mode( $atts ) {
        $atts[ 'preview' ] = true;
        return $atts;
    }

    /**
     * Returns the body content of the email, formatted as HTML. 
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_body() {
        $body = $this->get_option( 'body', $this->get_default_body() );
        $body = do_shortcode( $body );
        $body = wpautop( $body );
        return apply_filters( 'charitable_email_body', $body, $this );
    }

    /**
     * Returns the email headline.
     *
     * @return  string
     * @access  public
     * @since   1.0.0
     */
    public function get_headline() {
        $headline = $this->get_option( 'headline', $this->get_default_headline() );
        $headline = do_shortcode( $headline );
        return apply_filters( 'charitable_email_headline', $headline, $this );
    }    

    /**
     * Build the email.  
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function build_email() {
        ob_start();

        charitable_template( 'emails/header.php', array( 'email' => $this ) );

        charitable_template( 'emails/body.php', array( 'email' => $this ) );

        charitable_template( 'emails/footer.php', array( 'email' => $this ) );

        $message = ob_get_clean();

        return apply_filters( 'charitable_email_message', $message, $this );
    }

    /**
     * Return the value of an option specific to this email. 
     *
     * @param   string  $key
     * @return  mixed
     * @access  protected
     * @since   1.0.0
     */
    protected function get_option( $key, $default ) {
        return charitable_get_option( array( $this::ID, $key ), $default );
    }

    /**
     * Return the default recipient for the email.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_default_recipient() {
        return "";
    }

    /**
     * Return the default subject line for the email.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_default_subject() {
        return "";   
    }

    /**
     * Return the default headline for the email.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_default_headline() {
        return "";   
    }

    /**
     * Return the default body for the email.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_default_body() {
        return "";
    }  

    /**
     * Returns the value of a particular field (generally 
     * called through the [charitable_email] shortcode). 
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_field_content( $field ) {
        $fields = $this->get_fields();

        if ( ! isset( $fields[ $field ] ) ) {
            return '';
        }

        return call_user_func( $fields[ $field ] );
    }    

    /**
     * Return the value of a field for the preview.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_preview_field_content( $field ) {
        $values = apply_filters( 'charitable_email_preview_content_fields', array(
            'site_name'     => get_option( 'blogname' ), 
            'site_url'      => home_url()
        ), $this );

        if ( ! isset( $values[ $field ] ) ) {
            return $field;
        }

        return $values[ $field ];
    }

    /**
     * Return HTML formatted list of shortcode options that can be used within the body, headline and subject line. 
     *
     * @return  string
     * @access  protected
     * @since   version
     */
    protected function get_shortcode_options() {
        ob_start();
?>
        <p><?php _e( 'The following options are available with the <code>[charitable_email]</code> shortcode:', 'charitable' ) ?></p>
        <ul>
        <?php foreach ( $this->get_fields() as $key => $field ) : ?>
            <li><strong><?php echo $field[ 'description' ] ?></strong>: [charitable_email show=<?php echo $key ?>]</li>
        <?php endforeach ?> 
        </ul>

<?php
        $html = ob_get_clean();

        return apply_filters( 'charitable_email_shortcode_options_text', $html, $this );
    }

    /**
     * Returns the given value if the current email object has a valid donation. 
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function return_value_if_has_valid_donation( $return, $fallback = "" ) {
        if ( ! $this->has_valid_donation() ) {
            return $fallback;
        }

        return $return;
    }

    /**
     * Checks whether the email has a valid donation object set. 
     *
     * @return  boolean
     * @access  protected
     * @since   1.0.0
     */
    protected function has_valid_donation() {
        if ( is_null( $this->donation ) || ! is_a( $this->donation, 'Charitable_Donation' ) ) {
            _doing_it_wrong( __METHOD__, __( 'You cannot send this email without a donation!', 'charitable' ), '1.0.0' );
            return false;
        }

        return true;
    }

    /**
     * Checks whether the email has a valid donation object set. 
     *
     * @return  boolean
     * @access  protected
     * @since   1.0.0
     */
    protected function has_valid_campaign() {
        if ( is_null( $this->campaign ) || ! is_a( $this, 'Charitable_Campaign' ) ) {
            _doing_it_wrong( __METHOD__, __( 'You cannot this email without a campaign!', 'charitable' ), '1.0.0' );
            return false;
        }

        return true;
    }

    /**
     * Returns the current email's ID.  
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_email_id() {
        $class = get_called_class();
        return $class::ID;
    }    
}

endif; // End class_exists check