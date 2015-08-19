<?php
/**
 * The template used to display the donation amount form. Unlike the main donation form, this does not include any user fields.
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Form
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$form = $view_args[ 'form' ];

if ( ! $form ) {
    return;
}
?>
<form method="post" id="charitable-donation-form" class="charitable-form charitable-form-amount">
    <?php 
    /**
     * @hook    charitable_form_before_fields
     */
    do_action( 'charitable_form_before_fields', $form ) ?>
    
    <div class="charitable-form-fields cf">

    <?php 

    $i = 1;

    foreach ( $form->get_fields() as $key => $field ) :

        do_action( 'charitable_form_field', $field, $key, $form, $i );
    
        $i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

    endforeach;

    ?>
    
    </div>

    <?php
    /**
     * @hook    charitable_form_after_fields
     */
    do_action( 'charitable_form_after_fields', $form );

    ?>
    <div class="charitable-form-field charitable-submit-field">
        <input class="button button-primary" type="submit" name="donate" value="<?php esc_attr_e( 'Donate', 'charitable' ) ?>" />
    </div>    
</form>