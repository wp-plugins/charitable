<?php
/**
 * Display a list of campaigns.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$campaigns = $view_args[ 'campaigns' ];

if ( ! $campaigns->have_posts() ) :
    return;
endif;

if ( $view_args[ 'columns' ] > 1 ) :
    $loop_class = sprintf( 'campaign-loop campaign-grid campaign-grid-%d', $view_args[ 'columns' ] );
else : 
    $loop_class = 'campaign-loop';
endif;

?>
<ol class="<?php echo $loop_class ?>">

<?php 
while( $campaigns->have_posts() ) : 

    $campaigns->the_post();

    charitable_template( 'campaign-loop/campaign.php' );

endwhile;
?>
</ol>