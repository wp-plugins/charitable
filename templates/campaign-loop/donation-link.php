<?php 
/**
 * Displays the donate button to be displayed within campaign loops. 
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @var Charitable_Campaign
 */
$campaign = $view_args[ 'campaign' ];

if ( $campaign->has_ended() ) :
    return;
endif;

?>
<div class="campaign-donation">
    <a class="donate-button button" href="<?php echo charitable_get_permalink( 'campaign_donation_page', array( 'campaign' => $campaign ) ) ?>" title="<?php echo esc_attr( sprintf( '%s %s', _x( 'Make a donation to', 'make a donation to campaign', 'charitable' ), get_the_title( $campaign->ID ) ) ) ?>"><?php _e( 'Donate', 'charitable' ) ?></a>
</div>