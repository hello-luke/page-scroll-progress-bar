(function($) {

	'use strict';
	//console.log(pspb_val);

	var progressBar = '<div class="bar"><div class="mprogress '+ pspb_val.pspb_transition +'"></div></div>';

	if ( isMobile.phone && pspb_val.pspb_hide_on_mobile == 1 ) {
		return;
	} else {
		$('body').prepend(progressBar);
		addProgressBarStyles();
		showScrollBarWidth();
	}


	function addProgressBarStyles(){

		var windowWidth = $(window).width();

		$(window).resize(function() {
			windowWidth = $(window).width();
		}); 
 		
		var initialBarWidth = 0;
		if ( pspb_val.pspb_reverse == 1 ){
			var initialBarWidth = 100;
		}

		// check if user is logged in to show Scroll Bar under the Admin Bar
		if ( pspb_val.pspb_position === 'top' && pspb_val.is_user_logged_in == 1 && pspb_val.pspb_offset < 32 && windowWidth > 860 ){
			var scrollBarOffset = 32;
		}

		else if ( windowWidth <= 860 ){
			var scrollBarOffset = 0;
		}

		else {
			var scrollBarOffset = pspb_val.pspb_offset;
		}

		$('.bar')
			.css({
				'position': 'fixed',
				'left': '0px',
				'background': pspb_val.pspb_rail_color,
				'width': '100%',
				'height': pspb_val.pspb_height,
				'z-index': '999'
				})
			.css(pspb_val.pspb_position, scrollBarOffset + 'px');

		$('.mprogress')
			.css({
				background: pspb_val.pspb_fill_color,
				width: initialBarWidth + '%',
				height: pspb_val.pspb_height
			});
	}

	function showScrollBarWidth(){

		$(window).scroll(function () {

			var scrolledFromTop = $(document).scrollTop();
			var documentHeight = $(document).height();
			var windowHeight = $(window).height();
			var currentlyScrolled = ((scrolledFromTop / (documentHeight - windowHeight)) * 100).toFixed(2);  
			var reversedScroll = 100 - currentlyScrolled; 

			if ( pspb_val.pspb_reverse == 1 ){
				$('.mprogress').css({ width: reversedScroll + '%'});
			} else {
				$('.mprogress').css({ width: currentlyScrolled + '%'});
			}

		});	

	}
	
})( jQuery );