<?php 
/**
 * Displays the donate button to be displayed on campaign pages. 
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

$campaign = $view_args[ 'campaign' ];
?>
<form class="campaign-donation" method="post">
	<?php wp_nonce_field( 'charitable-donate-' . charitable_get_session()->get_session_id(), 'charitable-donate-now' ) ?>
	<input type="hidden" name="charitable_action" value="start_donation" />
	<input type="hidden" name="campaign_id" value="<?php echo $campaign->ID ?>" />
	<input type="submit" name="charitable_submit" value="<?php esc_attr_e( 'Donate', 'charitable' ) ?>" class="donate-button button button-primary" />
</form>