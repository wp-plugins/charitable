<?php
/**
 * The template used to display the login form. Provided here primarily as a way to make it easier to override using theme templates.
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Account
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="charitable-login-form">
    <?php

    /**
     * @hook    charitable_login_form_before
     */
    do_action( 'charitable_login_form_before' );

    wp_login_form( $view_args[ 'login_form_args' ] );

    ?>
    <p>
        <a href="<?php echo wp_lostpassword_url() ?>"><?php _e( 'Forgot Password', 'charitable' ) ?></a>
    </p>
    <?php

    /**
     * @hook    charitable_login_form_after
     */
    do_action( 'charitable_login_form_after' )

    ?>
</div>