<?php
/**
 * The template used to display the profile form.
 *
 * @author 	Studio 164a
 * @package Charitable/Templates/Account
 * @since 	1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$form 	= $view_args[ 'form' ];
$donor	= new Charitable_User( wp_get_current_user() );

/**
 * @hook 	charitable_user_profile_before
 */
do_action('charitable_user_profile_before');

?>
<form method="post" id="charitable-profile-form" class="charitable-form" enctype="multipart/form-data">
	<?php 
	/**
	 * @hook 	charitable_form_before_fields
	 */
	do_action( 'charitable_form_before_fields', $form ) ?>
	
	<div class="charitable-form-fields cf">

	<?php 

	$i = 1;

	foreach ( $form->get_fields() as $key => $field ) :

		do_action( 'charitable_form_field', $field, $key, $form );

		$i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

	endforeach;

	?>
	
	</div>

	<?php
	/**
	 * @hook 	charitable_form_after_fields
	 */
	do_action( 'charitable_user_profile_after_fields', $form );

	?>
	<div class="charitable-form-field charitable-submit-field">
		<input class="button button-primary" type="submit" name="update-profile" value="<?php esc_attr_e( 'Update', 'charitable' ) ?>" />
	</div>
</form>
<?php

/**
 * @hook 	charitable_user_profile_after
 */
do_action('charitable_user_profile_after');