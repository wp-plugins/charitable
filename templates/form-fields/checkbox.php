<?php
/**
 * The template used to display checkbox form fields.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 * @version 1.0.0
 */

if ( ! isset( $view_args[ 'form' ] ) || ! isset( $view_args[ 'field' ] ) ) {
	return;
}

$form 			= $view_args[ 'form' ];
$field 			= $view_args[ 'field' ];
$classes 		= $view_args[ 'classes' ];
$is_required    = isset( $field[ 'required' ] ) ? $field[ 'required' ] : false;
$value			= isset( $field[ 'value' ] ) ? esc_attr( $field[ 'value' ] ) : '1';

if ( isset( $field[ 'checked' ] ) ) {
    $checked    = $field[ 'checked' ];
}
else {
    $checked    = isset( $field[ 'default' ] ) ? $field[ 'default' ] : 0;
}
?>
<div id="charitable_field_<?php echo $field[ 'key' ] ?>" class="<?php echo $classes ?>">	
	<input type="checkbox" name="<?php echo $field[ 'key' ] ?>" value="<?php echo $value ?>" <?php checked( $checked ) ?> />
	<?php if ( isset( $field[ 'label' ] ) ) : ?>
		<label for="charitable_field_<?php echo $field[ 'key' ] ?>">
			<?php echo $field[ 'label' ] ?>			
            <?php if ( $is_required ) : ?>
                <abbr class="required" title="required">*</abbr>
            <?php endif ?>
		</label>
	<?php endif ?>
</div>