( function($){

	var setup_charitable_ajax = function() {
		$('[data-charitable-action]').on( 'click', function( e ){
			var data 	= $(this).data( 'charitable-args' ) || {}, 
				action 	= 'charitable-' + $(this).data( 'charitable-action' );

			$.post( ajaxurl, 
				{
					'action'	: action,
					'data'		: data
				}, 
				function( response ) {
					console.log( "Response: " + response );
				} 
			);

			return false;
		} );
	};

	var setup_charitable_toggle = function() {
		$( '[data-charitable-toggle]' ).on( 'click', function( e ){
			var toggle_id 	= $(this).data( 'charitable-toggle' );

			$('#' + toggle_id).toggle();

			return false;
		} );
	};

	var setup_advanced_meta_box = function() {
		var $meta_box = $('#charitable-campaign-advanced-metabox');

		$meta_box.tabs();

		var min_height = $meta_box.find('.charitable-tabs').height();

		$meta_box.find('.ui-tabs-panel').each( function(){
			$(this).css( 'min-height', min_height );
		});
	};

	var add_suggested_amount_row = function() {
		var $table = $( '#charitable-campaign-suggested-donations tbody' ),
			index = function() {
				var $rows = $table.find( '[data-index]' ), 
					index = 0;

				if ( $rows.length ) {
					index = parseInt( $rows.last().data( 'index' ), 10 ) + 1;
				}

				return index;
			}(),
			row = '<tr data-index="' + index + '">'
				+ '<td><input type="text" id="campaign_suggested_donations_' + index + '" name="_campaign_suggested_donations[' + index + '][amount]" placeholder="' + CHARITABLE.suggested_amount_placeholder + '" />'
				+ '<td><input type="text" id="campaign_suggested_donations_' + index + '" name="_campaign_suggested_donations[' + index + '][description]" placeholder="' + CHARITABLE.suggested_amount_description_placeholder + '" />'
				+ '</tr>';

		$table.find( '.no-suggested-amounts' ).hide();
		$table.append( row );
	};	

	$(document).ready( function(){

		if ( $.fn.datepicker ) {

			$('.charitable-datepicker').datepicker( {
				dateFormat 	: 'MM d, yy', 
				minDate 	: $(this).data('min-date') || '',
				beforeShow	: function( input, inst ) {
					$('#ui-datepicker-div').addClass('charitable-datepicker-table');
				}
			} );

			$('.charitable-datepicker').each( function(){				
				if ( $(this).data('date') ) {
					$(this).datepicker( 'setDate', $(this).data('date') );
				}

				if ( $(this).data('min-date') ) {
					$(this).datepicker( 'option', 'minDate', $(this).data('min-date') );
				}
			});
		}

		$('body.post-type-campaign .handlediv, body.post-type-donation .handlediv').remove();
		$('body.post-type-campaign .hndle, body.post-type-donation .hndle').removeClass( 'hndle ui-sortable-handle' ).addClass( 'postbox-title' );

		setup_advanced_meta_box();

		setup_charitable_ajax();	
		setup_charitable_toggle();	

		$('[data-charitable-add-row]').on( 'click', function() {
			var type = $( this ).data( 'charitable-add-row' );

			if ( 'suggested-amount' === type ) {
				add_suggested_amount_row();
			}

			return false;
		});

		$('body').on( 'click', '[data-campaign-benefactor-delete]', function() {			
			var $block = $( this ).parents( '.charitable-benefactor' ),
				data = {
					action 			: 'charitable_delete_benefactor',
					benefactor_id 	: $(this).data( 'campaign-benefactor-delete' ), 
					nonce 			: $(this).data( 'nonce' )
				};

			$.ajax({
	            type: "POST",
	            data: data,
	            dataType: "json",
	            url: ajaxurl,
	            xhrFields: {
	                withCredentials: true
	            },
	            success: function (response) {
	            	if ( response.deleted ) {
	            		$block.remove();
	            	}
	            }
	        }).fail(function (data) {
	            if ( window.console && window.console.log ) {
	            	console.log( 'failture' );
	                console.log( data );
	            }
	        });

			return false;
		});

		$('#change-donation-status').on( 'change', function() {
			$(this).parents( 'form' ).submit();
		});
	});

})( jQuery );