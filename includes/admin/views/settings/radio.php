<?php
/**
 * Display select field. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$value = charitable_get_option( $view_args['key'] );

if ( empty( $value ) ) :
    $value = isset( $view_args['default'] ) ? $view_args['default'] : '';
endif;

?>
<ul class="charitable-radio-list <?php echo esc_attr( $view_args[ 'classes' ] ) ?>">

    <?php foreach ( $view_args[ 'options' ] as $option => $label ) : ?>

        <li><input type="radio" 
                id="<?php printf( 'charitable_settings_%s_%s', implode( '_', $view_args[ 'key' ] ), $option ) ?>" 
                name="<?php printf( 'charitable_settings[%s]', $view_args[ 'name' ] ) ?>"
                value="<?php echo esc_attr( $option ) ?>"
                <?php checked( $value, $option ) ?> />
            <?php echo $label ?>
        </li>

    <?php endforeach ?>

</ul>
<?php if ( isset( $view_args['help'] ) ) : ?>

    <div class="charitable-help"><?php echo $view_args['help']  ?></div>

<?php endif;