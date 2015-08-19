<?php
/**
 * The template used to display file form fields.
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
$classes        = $view_args[ 'classes' ];
$is_required    = isset( $field[ 'required' ] ) ? $field[ 'required' ] : false;
$placeholder    = isset( $field[ 'placeholder' ] ) ? esc_attr( $field[ 'placeholder' ] ) : '';
?>
<div id="charitable_field_<?php echo $field[ 'key' ] ?>" class="<?php echo $classes ?>">    
    <?php if ( isset( $field[ 'label' ] ) ) : ?>
        <label for="charitable_field_<?php echo $field[ 'key' ] ?>">
            <?php echo $field[ 'label' ] ?>         
            <?php if ( $is_required ) : ?>
                <abbr class="required" title="required">*</abbr>
            <?php endif ?>
        </label>
    <?php endif ?>
    <?php if ( strlen( $field[ 'value' ] ) ) : 
        echo $field[ 'value' ]; 
    endif; ?>
        
    <input type="file" name="<?php echo $field[ 'key' ] ?>" /> 
</div>