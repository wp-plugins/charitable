<?php 
/**
 * Renders a benefactors addon metabox. Used by any plugin that utilizes the Benefactors Addon.
 *
 * @since 		1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2014, Studio 164a 
 */
global $post;

if ( ! isset( $view_args['extension'] ) ) {
	_doing_it_wrong( 'charitable_campaign_meta_boxes', 'Campaign benefactors metabox requires an extension argument.', '1.0.0' );
	return;
}

$extension		= $view_args['extension'];
$benefactors 	= charitable_get_table( 'benefactors' )->get_campaign_benefactors_by_extension( $post->ID, $extension );
?>
<div class="charitable-metabox">
	<?php 
	if ( empty( $benefactors ) ) : ?>
		
		<p><?php _e( 'No benefactor relationships have been set up yet.', 'charitable' ) ?></p>

	<?php else :
		foreach ( $benefactors as $benefactor ) :
		?>
		<div class="charitable-metabox-block charitable-benefactor">
			<?php do_action( 'charitable_campaign_benefactor_meta_box', Charitable_Benefactor::get_object( $benefactor, $extension ), $extension ) ?>
		</div>
		<?php
		endforeach;
	endif;
	
	charitable_admin_view( 'metaboxes/campaign-benefactors/form', array( 'benefactor' => null, 'extension' => $extension ) ); 
	?>
	<p><a href="#" class="button" data-charitable-toggle="campaign_benefactor_0"><?php _e( '+ Create Relationship', 'charitable' ) ?></a></p>	
</div>