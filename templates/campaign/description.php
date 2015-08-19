<?php 
/**
 * Displays the campaign description. 
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$campaign = $view_args[ 'campaign' ];

?>
<div class="campaign-description">  
    <?php echo $campaign->description ?>
</div>