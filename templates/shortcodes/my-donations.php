<?php
/**
 * Display a list of the current user's donations.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$donations = $view_args[ 'donations' ];

if ( ! $donations->have_posts() ) :
    return;
endif;

?>