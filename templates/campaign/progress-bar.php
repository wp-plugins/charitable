<?php 
/**
 * Displays the campaign progress bar.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @var Charitable_Campaign
 */
$campaign = $view_args[ 'campaign' ];

if ( ! $campaign->has_goal() ) :
    return;
endif;

?>
<div class="campaign-progress-bar"><span class="bar" style="width: <?php echo $campaign->get_percent_donated_raw() ?>%;"></span></div>