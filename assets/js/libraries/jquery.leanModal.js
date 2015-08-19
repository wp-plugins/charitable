(function($){
 
    $.fn.extend({ 
         
        leanModal: function(options) {
 
            var defaults = {
                verticalOffset: 100,
                overlay: 0.5,
                closeButton: null
            }
            
            var overlay = $("<div id='lean_overlay'></div>");            
            
            $("body").append(overlay);
                 
            options =  $.extend(defaults, options);
 
            return this.each(function() {
            
                var o = options,
                    window_height = $(window).height(); 
               
                $(this).click(function(e) {
              
                var modal_id = $(this).attr("href");

                $("#lean_overlay").click(function() { 
                     close_modal(modal_id);                    
                });
                
                $(o.closeButton).click(function() { 
                     close_modal(modal_id);                    
                });
                            
                var modal_height = $(modal_id).outerHeight();
                var modal_width = $(modal_id).outerWidth();

                $('#lean_overlay').css({ 'display' : 'block', opacity : 0 });

                $('#lean_overlay').fadeTo(200,o.overlay);

                $(modal_id).css({ 
                
                    'display' : 'block',
                    'position' : 'fixed',
                    'opacity' : 0,
                    'z-index': 100000,
                    'left' : 50 + '%',
                    'margin-left' : -(modal_width/2) + "px",
                    'top' : o.verticalOffset + "px"
                
                });

                if ( modal_is_too_tall( window_height, modal_height, o.verticalOffset ) ) {

                    resize_modal( modal_id, window_height, modal_height );                    

                }
                
                $(modal_id).fadeTo(200,1);

                e.preventDefault();
                        
                });
             
            });

            function close_modal(modal_id){

                $("#lean_overlay").fadeOut(200);

                $(modal_id).css({ 'display' : 'none' });
            
            }            

            function modal_is_too_tall( window_height, modal_height, offset ) {
                return window_height < modal_height + ( 2 * offset );
            }

            function resize_modal( modal_id, window_height, modal_height ) {
                var available_offset = window_height - modal_height;

                if ( available_offset > 0 ) {
                    var v_offset = available_offset / 2;

                    $(modal_id).css({
                        'top' : v_offset + 'px',
                        'bottom' : v_offset + 'px'
                    });
                }

                if ( available_offset <= 0 ) {
                    $(modal_id).css({
                        'top' : '10px',
                        'bottom' : '10px',
                        'overflow' : 'scroll'
                    })
                }
            }
        }
    });
     
})(jQuery);