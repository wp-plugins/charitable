<?php 
/**
 * Displays the amount of time left in the campaign.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

$campaign = $view_args[ 'campaign' ];

?>
<div class="campaign-time-left campaign-summary-item">
    <?php echo $campaign->get_time_left() ?>
</div>