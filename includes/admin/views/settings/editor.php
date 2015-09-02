<?php
/**
 * Display WP Editor in a settings field. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$value = charitable_get_option( $view_args[ 'key' ] );

if ( empty( $value ) ) :
    $value = isset( $view_args['default'] ) ? $view_args['default'] : '';
endif;

$editor_args = isset( $view_args[ 'editor' ] ) ? $view_args[ 'editor' ] : array();

$default_editor_args = array(
    'textarea_name'     => sprintf( 'charitable_settings[%s]', $view_args[ 'name' ] )
);

$editor_args = wp_parse_args( $editor_args, $default_editor_args );

wp_editor( $value, sprintf( 'charitable_settings_%s', implode( '_', $view_args[ 'key' ] ) ), $editor_args );

if ( isset( $view_args['help'] ) ) : ?>

    <div class="charitable-help"><?php echo $view_args[ 'help' ]  ?></div>

<?php endif;