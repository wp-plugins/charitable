<?php 
/**
 * Charitable Page Functions. 
 * 
 * @package 	Charitable/Functions/Page
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Displays a template. 
 *
 * @param 	string|array 	$template_name 		A single template name or an ordered array of template
 * @param 	arary 			$args 				Optional array of arguments to pass to the view.
 * @return 	Charitable_Template
 * @since 	1.0.0
 */
function charitable_template( $template_name, array $args = array() ) {
	if ( empty( $args ) ) {
		$template = new Charitable_Template( $template_name ); 
	}
	else {
		$template = new Charitable_Template( $template_name, false ); 
		$template->set_view_args( $args );
		$template->render();
	}

	return $template;
}

/**
 * Return the template path if the template exists. Otherwise, return default.
 *
 * @param 	string 	$template
 * @return  string 				The template path if the template exists. Otherwise, return default.
 * @since   1.0.0
 */
function charitable_get_template_path( $template, $default = "" ) {
    $t = new Charitable_Template( $template, false );
    $path = $t->locate_template();

    if ( ! file_exists( $path ) ) {
    	$path = $default;
    }

    return $path;
}

/**
 * Return the URL for a given page. 
 *
 * Example usage: 
 * 
 * - charitable_get_permalink( 'campaign_donation_page' );
 * - charitable_get_permalink( 'login_page' );
 * - charitable_get_permalink( 'registration_page' );
 * - charitable_get_permalink( 'profile_page' );
 * - charitable_get_permalink( 'donation_receipt_page' );
 *
 * @param 	string 	$page
 * @param   array 	$args 		Optional array of arguments.        
 * @return  string|false        String if page is found. False if none found.
 * @since   1.0.0
 */
function charitable_get_permalink( $page, $args = array() ) {
    return apply_filters( 'charitable_permalink_' . $page, false, $args );
}

/**
 * Checks whether we are currently looking at the given page. 
 *
 * Example usage: 
 * 
 * - charitable_is_page( 'campaign_donation_page' );
 * - charitable_is_page( 'login_page' );
 * - charitable_is_page( 'registration_page' );
 * - charitable_is_page( 'profile_page' );
 * - charitable_is_page( 'donation_receipt_page' );
 *
 * @param   string 	$page 
 * @param 	array 	$args 		Optional array of arguments.
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_page( $page, $args = array() ) {
    return apply_filters( 'charitable_is_page_' . $page, false, $args );
}

/**
 * Returns the URL for the campaign donation page. 
 *
 * This is used when you call charitable_get_permalink( 'campaign_donation_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @global 	WP_Rewrite 	$wp_rewrite
 * @param 	string 		$url
 * @param 	array 		$args
 * @return 	string
 * @since 	1.0.0
 */
function charitable_get_campaign_donation_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$campaign_id = isset( $args[ 'campaign_id' ] ) ? $args[ 'campaign_id' ] : get_the_ID();

	if ( $wp_rewrite->using_permalinks() && ! isset( $_GET[ 'preview' ] ) ) {
		$url = trailingslashit( get_permalink( $campaign_id ) ) . 'donate/';
	}
	else {
		$url = esc_url_raw( add_query_arg( array( 'donate' => 1 ), get_permalink( $campaign_id ) ) );	
	}
			
	return $url;
}	

add_filter( 'charitable_permalink_campaign_donation_page', 'charitable_get_campaign_donation_page_permalink', 2, 2 );		

/**
 * Returns the URL for the campaign donation page. 
 *
 * This is used when you call charitable_get_permalink( 'donation_receipt_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_donation_receipt_page_permalink( $url, $args = array() ) {    
    global $wp_rewrite;

    $donation_id = isset( $args[ 'donation_id' ] ) ? $args[ 'donation_id' ] : get_the_ID();

    if ( $wp_rewrite->using_permalinks() ) {
        $url = sprintf( '%s/donation-receipt/%d', untrailingslashit( site_url() ), $donation_id );
    }
    else {
        $url = esc_url_raw( add_query_arg( array( 'donation_receipt' => 1, 'donation_id' => $donation_id ), site_url() ) );
    }
    
    return $url;
}   

add_filter( 'charitable_permalink_donation_receipt_page', 'charitable_get_donation_receipt_page_permalink', 2, 2 );       

/**
 * Returns the url of the widget page. 
 *
 * This is used when you call charitable_get_permalink( 'campaign_widget_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @param 	string 		$url
 * @param 	array 		$args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_campaign_widget_page_permalink( $url, $args = array() ) {	
	global $wp_rewrite;

    $campaign_id = isset( $args[ 'campaign_id' ] ) ? $args[ 'campaign_id' ] : get_the_ID();

    if ( $wp_rewrite->using_permalinks() && ! isset( $_GET[ 'preview' ] ) ) {
        $url = trailingslashit( get_permalink( $campaign_id ) ) . 'widget/';
    }
    else {
        $url = esc_url_raw( add_query_arg( array( 'widget' => 1 ), get_permalink( $campaign_id ) ) );   
    }
            
    return $url;
}   

add_filter( 'charitable_permalink_campaign_widget_page', 'charitable_get_campaign_widget_page_permalink', 2, 2 );

/**
 * Checks whether the current request is for the given page. 
 *
 * This is used when you call charitable_is_page( 'campaign_donation_page' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global 	WP_Query 	$wp_query
 * @param 	boolean 	$ret	 
 * @param 	array 		$args 
 * @return 	boolean
 * @since 	1.0.0
 */
function charitable_is_campaign_donation_page( $ret = false, $args = array() ) {		
	global $wp_query;

	$ret = is_main_query() && isset ( $wp_query->query_vars[ 'donate' ] ) && is_singular( 'campaign' );

	return $ret;
}

add_filter( 'charitable_is_page_campaign_donation_page', 'charitable_is_campaign_donation_page', 2, 2 );

/**
 * Checks whether the current request is for the campaign widget page.
 *
 * This is used when you call charitable_is_page( 'campaign_widget_page' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global  WP_Query    $wp_query
 * @param   string      $page
 * @param   array       $args 
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_campaign_widget_page( $ret = false, $args = array()  ) {     
    global $wp_query;

    $ret = is_main_query() && isset ( $wp_query->query_vars[ 'widget' ] ) && is_singular( 'campaign' );

    return $ret;
}

add_filter( 'charitable_is_page_campaign_widget_page', 'charitable_is_campaign_widget_page', 2, 2 );

/**
 * Checks whether the current request is for the donation receipt page.
 *
 * This is used when you call charitable_is_page( 'donation_receipt_page' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global 	WP_Query 	$wp_query
 * @param 	string 		$page
 * @param 	array 		$args 
 * @return 	boolean
 * @since 	1.0.0
 */
function charitable_is_donation_receipt_page( $ret = false, $args = array()  ) {		
	global $wp_query;

	$ret = is_main_query() && isset ( $wp_query->query_vars[ 'donation_receipt' ] ) && isset ( $wp_query->query_vars[ 'donation_id' ] );

	return $ret;
}

add_filter( 'charitable_is_page_donation_receipt_page', 'charitable_is_donation_receipt_page', 2, 2 );

/**
 * Checks whether the current request is for an email preview.
 *
 * This is used when you call charitable_is_page( 'email_preview' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @param   string      $page
 * @param   array       $args 
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_email_preview( $ret = false, $args = array()  ) {     
    return isset( $_GET[ 'charitable_action' ] ) && 'preview_email' == $_GET[ 'charitable_action' ];
}

add_filter( 'charitable_is_page_email_preview', 'charitable_is_email_preview', 2, 2 );

/**
 * Checks whether the current request is for a single campaign. 
 *
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_campaign_page() {
    return is_singular() && Charitable::CAMPAIGN_POST_TYPE == get_post_type();
}

/**
 * Returns the URL for the user login page. 
 *
 * This is used when you call charitable_get_permalink( 'login_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @see     charitable_get_permalink
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_login_page_permalink( $url, $args = array() ) {     
    $page = charitable_get_option( 'login_page', 'wp' );
    $url = 'wp' == $page ? wp_login_url() : get_permalink( $page );
    return $url;
}   

add_filter( 'charitable_permalink_login_page', 'charitable_get_login_page_permalink', 2, 2 );      

/**
 * Checks whether the current request is for the campaign editing page. 
 *
 * This is used when you call charitable_is_page( 'login_page' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_login_page( $ret = false ) {
    global $post;

    $page = charitable_get_option( 'login_page', 'wp' );

    if ( 'wp' == $page ) {
        $ret = wp_login_url() == charitable_get_current_url();
    }
    elseif ( is_object( $post ) ) {
        $ret = $page == $post->ID;
    }

    return $ret;
}

add_filter( 'charitable_is_page_login_page', 'charitable_is_login_page', 2 );

/**
 * Returns the URL for the user registration page. 
 *
 * This is used when you call charitable_get_permalink( 'registration_page' ).In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 * 
 * @see     charitable_get_permalink
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_registration_page_permalink( $url, $args = array() ) {      
    $page = charitable_get_option( 'registration_page', 'wp' );
    $url = 'wp' == $page ? wp_registration_url() : get_permalink( $page );
    return $url;
}   

add_filter( 'charitable_permalink_registration_page', 'charitable_get_registration_page_permalink', 2, 2 );    

/**
 * Checks whether the current request is for the campaign editing page. 
 *
 * This is used when you call charitable_is_page( 'registration_page' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_registration_page( $ret = false ) {
    global $post;

    $page = charitable_get_option( 'registration_page', 'wp' );

    if ( 'wp' == $page ) {
        $ret = wp_registration_url() == charitable_get_current_url();
    }
    elseif ( is_object( $post ) ) {
        $ret = $page == $post->ID;
    }

    return $ret;
}

add_filter( 'charitable_is_page_registration_page', 'charitable_is_registration_page', 2 );

/**
 * Returns the URL for the user profile page. 
 *
 * This is used when you call charitable_get_permalink( 'profile_page' ).In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 * 
 * @see     charitable_get_permalink
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_profile_page_permalink( $url, $args = array() ) {       
    $page = charitable_get_option( 'profile_page', false );

    if ( $page ) {
        $url = get_permalink( $page );        
    }

    return $url;
}   

add_filter( 'charitable_permalink_profile_page', 'charitable_get_profile_page_permalink', 2, 2 );  

/**
 * Checks whether the current request is for the campaign editing page. 
 *
 * This is used when you call charitable_is_page( 'profile_page' ). 
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_profile_page( $ret = false ) {
    global $post;

    $page = charitable_get_option( 'profile_page', false );

    return false == $page || is_null( $post ) ? false : $page == $post->ID;
}

add_filter( 'charitable_is_page_profile_page', 'charitable_is_profile_page', 2 );

/**
 * Verifies whether the current user can access the donation receipt. 
 *
 * @param   Charitable_Donation $donation
 * @return  boolean
 * @since   1.1.2
 */
function charitable_user_can_access_receipt( Charitable_Donation $donation ) {
    /* If the donation key is stored in the session, the user can access this receipt */
    if ( charitable_get_session()->has_donation_key( $donation->get_donation_key() ) ) {
        return true;
    }

    if ( ! is_user_logged_in() ) {
        return false;
    }   

    /* Retrieve the donor and current logged in user */
    $donor = $donation->get_donor();
    $user = wp_get_current_user();

    /* Make sure they match */
    if ( $donor->ID ) {
        return $donor->ID == $user->ID;
    }

    return $donor->get_email() == $user->user_email;
}

/**
 * Returns the URL to which the user should be redirected after signing on or registering an account. 
 *
 * @return  string
 * @since   1.0.0
 */
 function charitable_get_login_redirect_url() {
    if ( isset( $_REQUEST[ 'redirect_to' ] ) ) {
        $redirect = $_REQUEST[ 'redirect_to' ];
    }
    elseif ( charitable_get_permalink( 'profile_page' ) ) {
        $redirect = charitable_get_permalink( 'profile_page' );
    }
    else {
        $redirect = site_url();
    }

    return apply_filters( 'charitable_signon_redirect_url', $redirect );
}

/**
 * Returns the current URL. 
 *
 * @see 	https://gist.github.com/leereamsnyder/fac3b9ccb6b99ab14f36
 * @global 	WP 		$wp
 * @return  string
 * @since   1.0.0
 */
function charitable_get_current_url() {
	global $wp;

	$url = esc_url_raw( add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) ) );	

	return $url;
}