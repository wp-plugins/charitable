<?php 
/**
 * Renders the campaign benefactors form.
 *
 * @since 		1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a 
 */

$benefactor = isset( $view_args[ 'benefactor' ] ) 	? $view_args[ 'benefactor' ] 	: null;
$extension 	= isset( $view_args['extension'] ) 		? $view_args[ 'extension' ] 	: '';

if ( is_null( $benefactor ) ) {
	$args = array(
		'id_base'							=> 'campaign_benefactor_0', 
		'name_base'							=> '_campaign_benefactor[0]', 
		'contribution_amount' 				=> '',
		'contribution_amount_is_per_item'	=> 0, 
		'date_created'						=> date( 'F d, Y' ), 
		'date_deactivated'					=> 0
	);
}
else {
	$args = array(
		'id_base' 							=> 'campaign_benefactor_' . $benefactor->campaign_benefactor_id,
		'name_base' 						=> '_campaign_benefactor[' . $benefactor->campaign_benefactor_id . ']', 
		'contribution_amount' 				=> $benefactor->get_contribution_amount(), 
		'contribution_amount_is_per_item'	=> $benefactor->contribution_amount_is_per_item, 
		'date_created'						=> date( 'F d, Y', strtotime( $benefactor->date_created ) ), 
		'date_deactivated'					=> date( 'F d, Y', strtotime( $benefactor->date_deactivated ) )		
	);	
}
?>
<div id="<?php echo $args['id_base'] ?>" class="charitable-metabox-wrap charitable-benefactor-wrap" style="display: none;">	
	<p><strong><?php _e( 'Contribution Amount', 'charitable' ) ?></strong></p>
	<fieldset class="charitable-benefactor-contribution-amount">		
		<input type="text" id="<?php echo $args['id_base'] ?>_contribution_amount" class="contribution-amount" name="<?php echo $args['name_base'] ?>[contribution_amount]" value="<?php echo $args['contribution_amount'] ?>" placeholder="<?php _e( 'Enter amount. e.g. 10%, $2', 'charitable' ) ?>" />		
		<select id="<?php echo $args['id_base'] ?>_contribution_amount_is_per_item" class="contribution-type" name="<?php echo $args['name_base'] ?>[contribution_amount_is_per_item]">
			<option value="1" <?php selected( 1, $args['contribution_amount_is_per_item'] ) ?>><?php _e( 'Every matching item', 'charitable' ) ?></option>
			<option value="0" <?php selected( 0, $args['contribution_amount_is_per_item'] ) ?>><?php _e( 'Once per purchase', 'charitable' ) ?></option>
		</select>
	</fieldset>
	<?php 
		do_action( 'charitable_campaign_benefactor_form_extension_fields', $benefactor, $extension );
	?>
	<div class="charitable-benefactor-date-wrap cf">
		<label for="<?php echo $args['id_base'] ?>_date_created"><?php _e( 'Starting From:', 'charitable' ) ?>
			<input type="text" id="<?php echo $args['id_base'] ?>_date_created" name="<?php echo $args['name_base'] ?>[date_created]" tabindex="3" class="charitable-datepicker" data-date="<?php echo $args['date_created'] ?>" data-min-date="+0" />
		</label>
		<label for="<?php echo $args['id_base'] ?>_date_deactivated"><?php _e( 'Ending:', 'charitable' ) ?>
			<input type="text" id="<?php echo $args['id_base'] ?>_date_deactivated" name="<?php echo $args['name_base'] ?>[date_deactivated]"  placeholder="&#8734;" tabindex="3" class="charitable-datepicker" data-date="<?php echo $args['date_deactivated'] ?>" data-min-date="+1" />
		</label>
	</div>
</div>