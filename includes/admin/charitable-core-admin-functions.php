<?php 

/**
 * Charitable Core Admin Functions
 *
 * General core functions available only within the admin area.
 * 
 * @package 	Charitable/Functions/Admin
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License   
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Load a view from the admin/views folder. 
 * 
 * If the view is not found, an Exception will be thrown.
 *
 * Example usage: charitable_admin_view('metaboxes/cause-metabox');
 *
 * @param 	string 		$view 			The view to display. 
 * @param 	array 		$view_args 		Optional. Arguments to pass through to the view itself
 * @return 	void
 * @since 	1.0.0
 */
function charitable_admin_view( $view, $view_args = array() ) {
	$filename = apply_filters( 'charitable_admin_view_path', charitable()->get_path( 'admin' ) . 'views/' . $view . '.php', $view, $view_args );

	if ( ! is_readable( $filename ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Passed view (' . $filename . ') not found or is not readable.', 'charitable' ), '1.0.0' );
	}

	ob_start();

	include( $filename );

	ob_end_flush();
}

/**
 * Returns the Charitable_Admin_Settings helper.
 *
 * @return 	Charitable_Admin_Settings
 * @since 	1.0.0
 */
function charitable_get_admin_settings() {
	return charitable_get_helper( 'settings' );
}

/**
 * Print out the settings fields for a particular settings section.
 *
 * This is based on WordPress' do_settings_fields but allows the possibility
 * of leaving out a field lable/title, for fullwidth fields.
 * 
 * @see     do_settings_fields
 *
 * @global  $wp_settings_fields Storage array of settings fields and their pages/sections
 *
 * @param   string  $page       Slug title of the admin page who's settings fields you want to show.
 * @param   string  $section    Slug title of the settings section who's fields you want to show.
 * @return  string
 * @since   1.0.0
 */
function charitable_do_settings_fields( $page, $section ) {
    global $wp_settings_fields;

    if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
        return;
    }

    foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
        $class = '';

        if ( ! empty( $field['args']['class'] ) ) {
            $class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
        }

        echo "<tr{$class}>";

        if ( ! empty( $field['args']['label_for'] ) ) {
            echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
            echo '<td>';
            call_user_func($field['callback'], $field['args']);
            echo '</td>';
        } 
        elseif ( ! empty( $field[ 'title' ] ) ) {
            echo '<th scope="row">' . $field['title'] . '</th>';
            echo '<td>';
            call_user_func($field['callback'], $field['args']);
            echo '</td>';
        }
        else {
            echo '<td colspan="2" class="charitable-fullwidth">';
            call_user_func($field['callback'], $field['args']);
            echo '</td>';
        }

        echo '</tr>';
    }
}