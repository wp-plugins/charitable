<?php
/**
 * The template used to display text form fields.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! isset( $view_args[ 'form' ] ) || ! isset( $view_args[ 'field' ] ) ) {
    return;
}

$form           = $view_args[ 'form' ];
$field          = $view_args[ 'field' ];
$classes        = esc_attr( $view_args[ 'classes' ] );
$field_type     = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';
$is_required    = isset( $field[ 'required' ] ) ? $field[ 'required' ] : false;
$value          = isset( $field[ 'value' ] ) ? $field[ 'value' ] : '';
$placeholder    = isset( $field[ 'placeholder' ] ) ? esc_attr( $field[ 'placeholder' ] ) : '';
$pattern        = isset( $field[ 'pattern' ] ) ? 'pattern="' . esc_attr( $field[ 'pattern' ] ) . '"' : ' ';
?>
<div id="charitable_field_<?php echo $field['key'] ?>" class="<?php echo $classes ?>">
    <?php if ( isset( $field['label'] ) ) : ?>
        <label for="charitable_field_<?php echo $field['key'] ?>">
            <?php echo $field['label'] ?>
            <?php if ( $is_required ) : ?>
                <abbr class="required" title="required">*</abbr>
            <?php endif ?>
        </label>
    <?php endif ?>
    <input type="<?php echo esc_attr( $field_type ) ?>" name="<?php echo $field[ 'key' ] ?>" value="<?php echo esc_attr( stripslashes( $value ) ) ?>" placeholder="<?php echo $placeholder ?>" <?php echo $pattern ?> />
</div>