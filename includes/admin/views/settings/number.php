<?php
/**
 * Display number field. 
 *
 * @author 	Studio 164a
 * @package Charitable/Admin View/Settings
 * @since 	1.0.0
 */

$value = charitable_get_option( $view_args[ 'key' ] );

if ( false === $value ) {
	$value = isset( $view_args['default'] ) ? $view_args['default'] : '';
}

$min = isset( $view_args['min'] ) ? 'min="' . $view_args['min'] . '"' : '';
$max = isset( $view_args['max'] ) ? 'max="' . $view_args['max'] . '"' : '';
?>
<input type="number" 
    id="<?php printf( 'charitable_settings_%s', implode( '_', $view_args[ 'key' ] ) ) ?>" 
    name="<?php printf( 'charitable_settings[%s]', $view_args[ 'name' ] ) ?>"
    value="<?php echo $value ?>" <?php echo $min ?> <?php echo $max ?> 
    class="<?php echo esc_attr( $view_args[ 'classes' ] ) ?>"
    />
<?php if ( isset( $view_args['help'] ) ) : ?>

	<div class="charitable-help"><?php echo $view_args['help']  ?></div>
    
<?php endif;