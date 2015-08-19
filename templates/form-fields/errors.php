<?php
/**
 * The template used to display the suggested amounts field.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! isset( $view_args[ 'form' ] ) || ! isset( $view_args[ 'errors' ] ) ) {
    return;
}

$form           = $view_args[ 'form' ];
$errors         = $view_args[ 'errors' ];
?>
<div class="charitable-form-errors charitable-notice">
    <ul class="errors">
        <?php foreach ( $errors as $error ) : ?>
            <li><?php echo $error ?></li>
        <?php endforeach ?>
    </ul>
</div>