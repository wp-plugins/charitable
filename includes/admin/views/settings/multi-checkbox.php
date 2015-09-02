<?php
/**
 * Display a series of checkboxes.
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$value = charitable_get_option( $view_args['key'], array() );

if ( empty( $value ) ) :
    $value = isset( $view_args['default'] ) ? (array) $view_args['default'] : array();
endif;

if ( ! is_array( $value ) ) {
    $value = (array) $value;
}

?>
<ul class="charitable-checkbox-list <?php echo esc_attr( $view_args[ 'classes' ] ) ?>">

    <?php foreach ( $view_args[ 'options' ] as $option => $label ) : ?>

        <li><input type="checkbox" 
                id="<?php printf( 'charitable_settings_%s_%s', implode( '_', $view_args[ 'key' ] ), $option ) ?>" 
                name="<?php printf( 'charitable_settings[%s][]', $view_args[ 'name' ] ) ?>"
                value="<?php echo esc_attr( $option ) ?>"
                <?php checked( in_array( $option, $value ) ) ?> />
            <?php echo $label ?>
        </li>

    <?php endforeach ?>

</ul>
<?php if ( isset( $view_args['help'] ) ) : ?>

    <div class="charitable-help"><?php echo $view_args['help']  ?></div>

<?php endif;