<?php 
/**
 * Renders the suggested donation amounts field inside the donation options metabox for the Campaign post type.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $post;

$title 					= isset( $view_args['label'] ) 		? $view_args['label'] 	: '';
$tooltip 				= isset( $view_args['tooltip'] )	? '<span class="tooltip"> '. $view_args['tooltip'] . '</span>'	: '';
$description			= isset( $view_args['description'] )? '<span class="charitable-helper">' . $view_args['description'] . '</span>' 	: '';
$suggested_donations 	= get_post_meta( $post->ID, '_campaign_suggested_donations', true );

if ( ! $suggested_donations ) {
	$suggested_donations = array();
}
?>
<div id="charitable-campaign-suggested-donations-metabox-wrap" class="charitable-metabox-wrap">
	<table id="charitable-campaign-suggested-donations" class="widefat">
		<thead>
			<tr class="table-header">
				<th colspan="2"><label for="campaign_suggested_donations"><?php echo $title ?></label></th>
			</tr>
			<tr>
				<th class="amount-col"><?php _e( 'Amount', 'charitable' ) ?></th>
				<th class="description-col"><?php _e( 'Description (optional)', 'charitable' ) ?></th>
			</tr>
		</thead>		
		<tbody>
			<?php 
			if ( $suggested_donations ) : 

				foreach ( $suggested_donations as $i => $donation ) : 

					$amount = is_array( $donation ) ? $donation[ 'amount' ] : $donation; 
					$description = is_array( $donation ) ? $donation[ 'description' ] : ''; 
					
					?>
					<tr data-index="<?php echo $i ?>">
						<td class="amount-col"><input 
							type="text" 
							id="campaign_suggested_donations_<?php echo $i ?>" 
							name="_campaign_suggested_donations[<?php echo $i ?>][amount]" 
							value="<?php echo $amount ?>" 
							placeholder="<?php _e( 'Amount', 'charitable' ) ?>" />
						</td>
						<td class="description-col"><input 
							type="text" 
							id="campaign_suggested_donations_<?php echo $i ?>" 
							name="_campaign_suggested_donations[<?php echo $i ?>][description]" 
							value="<?php echo $description ?>" 
							placeholder="<?php _e( 'Optional Description', 'charitable' ) ?>" />
						</td>
					</tr>
					<?php 

				endforeach;

			else : 

			?>
			<tr class="no-suggested-amounts">
				<td colspan="2"><?php _e( 'No suggested amounts have been created yet.', 'charitable' ) ?></td>
			</tr>
			<?php 

			endif;

			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><a class="button" href="#" data-charitable-add-row="suggested-amount"><?php _e( 'Add a suggested amount', 'charitable' ) ?></a></td>
			</tr>
		</tfoot>
	</table>	
</div>