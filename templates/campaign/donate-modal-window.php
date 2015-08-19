<?php 
/**
 * Displays the donate button to be displayed on campaign pages. 
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

$campaign = $view_args[ 'campaign' ];

if ( $campaign->has_ended() ) :
    return;
endif;

$modal_class = apply_filters( 'charitable_modal_window_class', 'charitable-modal' );

wp_print_scripts( 'lean-modal' );
wp_enqueue_style( 'lean-modal-css' );
?>
<div id="charitable-donation-form-modal" style="display: none;" class="<?php echo esc_attr( $modal_class ) ?>">
    <a class="modal-close"></a>
    <?php $campaign->get_donation_form()->render() ?>
</div>
<script type="text/javascript">
( function( $ ) {
    $('[data-trigger-modal]').leanModal({
        closeButton : ".modal-close"
    });
})( jQuery );
</script>
