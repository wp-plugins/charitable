<?php
/**
 * Display a widget with donation stats.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

$widget_title = apply_filters( 'widget_title', $view_args[ 'title' ] );
$campaigns_count = Charitable_Campaigns::query( array( 'posts_per_page' => -1, 'fields' => 'ids' ) )->found_posts;

echo $view_args[ 'before_widget' ];

if ( ! empty( $widget_title ) ) :
    echo $view_args['before_title'] . $widget_title . $view_args['after_title'];
endif;

?>
<ul class="donation-stats">
    <li>
        <?php printf( '<span class="figure">%d</span> %s', $campaigns_count, _n( 'Campaign', 'Campaigns', $campaigns_count, 'charitable' ) ) ?>
    </li>
    <li>                
        <?php printf( '<span class="figure">%s</span> %s', charitable_get_currency_helper()->get_monetary_amount( charitable_get_table( 'campaign_donations' )->get_total(), 0 ), __( 'Donated', 'charitable' ) ) ?>
    </li>
    <li>
        <?php printf( '<span class="figure">%d</span> %s', charitable_get_table( 'donors' )->count_all(), __( 'Donors', 'charitable' ) ) ?>        
    </li>
</ul>

<?php
echo $view_args['after_widget'];