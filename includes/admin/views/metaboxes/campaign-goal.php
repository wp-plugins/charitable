<?php 
/**
 * Renders the campaign goal block in the settings metabox for the Campaign post type.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $post;

$title 			= isset( $view_args['title'] ) 		? $view_args['title'] 	: '';
$tooltip 		= isset( $view_args['tooltip'] )	? '<span class="tooltip"> '. $view_args['tooltip'] . '</span>'	: '';
$description	= isset( $view_args['description'] )? '<span class="charitable-helper">' . $view_args['description'] . '</span>' 	: '';
$goal 			= get_post_meta( $post->ID, '_campaign_goal', true );
$goal 			= ! $goal ? '' : charitable()->get_currency_helper()->get_monetary_amount( $goal );
?>
<div id="charitable-campaign-goal-metabox-wrap" class="charitable-metabox-wrap">
	<label class="screen-reader-text" for="campaign_goal"><?php echo $title ?></label>
	<input type="text" id="campaign_goal" name="_campaign_goal"  placeholder="&#8734;" tabindex="2" value="<?php echo $goal ?>" />
	<?php echo $description ?>
</div>