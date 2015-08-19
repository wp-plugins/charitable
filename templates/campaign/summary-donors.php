<?php 
/**
 * Displays the campaign's donor summary. 
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

$campaign = $view_args[ 'campaign' ];

?>
<div class="campaign-donors campaign-summary-item">
    <?php printf( 
        _x( '%s Donors', 'number of donors', 'charitable' ), 
        '<span class="donors-count">' . $campaign->get_donor_count() . '</span>'
    ) ?>
</div>