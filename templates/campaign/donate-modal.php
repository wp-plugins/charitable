<?php 
/**
 * Displays the donate button to be displayed on campaign pages. 
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

$campaign = $view_args[ 'campaign' ];

?>
<div class="campaign-donation">
    <a data-trigger-modal class="donate-button button" href="#charitable-donation-form-modal" title="<?php echo esc_attr( sprintf( '%s %s', _x( 'Make a donation to', 'make a donation to campaign', 'charitable' ), get_the_title( $campaign->ID ) ) ) ?>"><?php _e( 'Donate', 'charitable' ) ?></a>
</div>