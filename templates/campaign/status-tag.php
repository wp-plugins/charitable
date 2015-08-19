<?php 
/**
 * Displays the campaign status tag.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$campaign = $view_args[ 'campaign' ];
$tag = $campaign->get_status_tag();

if ( empty( $tag ) ) {
    return;
}

?>
<div class="campaign-status-tag campaign-status-tag-<?php echo strtolower( str_replace( ' ', '-', $tag ) ) ?>">  
    <?php echo $tag ?>
</div>