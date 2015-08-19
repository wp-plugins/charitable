<?php
/**
 * Display the donations page. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

require_once charitable()->get_path( 'admin' ) . 'donations/class-charitable-donations-table.php';

$donation_post_type = get_post_type_object( 'donation' );

$donations_table = new Charitable_Donations_Table();
$donations_table->prepare_items();

$start_date = isset( $_GET['start-date'] )  ? sanitize_text_field( $_GET['start-date'] ) : null;
$end_date   = isset( $_GET['end-date'] )    ? sanitize_text_field( $_GET['end-date'] )   : null;
$status     = isset( $_GET['posts_status'] )? $_GET['posts_status'] : '';

ob_start();
?>
<div class="wrap">
    <h2><?php echo $donation_post_type->labels->menu_name ?></h2>
    <?php do_action( 'charitable_donations_page_top' ); ?>
    <form id="charitable-donations" method="get" action="<?php echo admin_url( 'admin.php?page=charitable-donations-table' ); ?>">
        <input type="hidden" name="page" value="charitable-donations-table" />

        <?php $donations_table->views() ?>

        <div id="charitable-donation-filters">
            <span id="charitable-donation-date-filters">
                <label for="start-date"><?php _e( 'Start Date:', 'charitable' ); ?></label>
                <input type="text" id="start-date" name="start-date" class="charitable-datepicker" value="<?php echo $start_date; ?>" />
                <label for="end-date"><?php _e( 'End Date:', 'charitable' ); ?></label>
                <input type="text" id="end-date" name="end-date" class="charitable-datepicker" value="<?php echo $end_date; ?>" />
                <input type="submit" class="button-secondary" value="<?php _e( 'Apply', 'charitable' ) ?>" />
            </span>
            <?php if( ! empty( $status ) ) : ?>
                <input type="hidden" name="post_status" value="<?php echo esc_attr( $status ); ?>"/>
            <?php endif; ?>
            <?php if( ! empty( $start_date ) || ! empty( $end_date ) ) : ?>
                <a href="<?php echo admin_url( 'admin.php?page=charitable-donations-table' ); ?>" class="button-secondary"><?php _e( 'Clear Filter', 'charitable' ); ?></a>
            <?php endif; ?>
            <?php //$this->search_box( __( 'Search', 'charitable' ), 'edd-donations' ); ?>
        </div>
        
        <?php $donations_table->display() ?>
    </form>    
</div>
<?php
echo ob_get_clean();