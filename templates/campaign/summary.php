<?php 
/**
 * Displays the campaign summary. 
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

$campaign = charitable_get_current_campaign();

/**
 * @hook charitable_campaign_summary_before
 */
do_action( 'charitable_campaign_summary_before', $campaign ); 

?>
<div class="campaign-summary">	
    <?php 

    /**
     * @hook charitable_campaign_summary
     */
    do_action( 'charitable_campaign_summary', $campaign ); 

    ?>
</div>
<?php

/**
 * @hook charitable_campaign_summary_after
 */
do_action( 'charitable_campaign_summary_after', $campaign );