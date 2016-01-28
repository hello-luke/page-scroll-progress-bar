(function( $ ) {
    'use strict';

	// set default values for rail color picker
	var railOptions = {

		defaultColor: false,
		hide: true,
		palettes: ['#2c3e50', '#34495e', '#22313F', '#95a5a6', '#bdc3c7', '#7f8c8d']

    };

    // set default values for fill color picker
	var fillOptions = {

		defaultColor: false,
		hide: true,
		palettes: ['#3498db', '#2ecc71', '#1abc9c', '#e74c3c', '#d35400', '#f1c40f', '#9b59b6']

    };
 
	// Add color picker field to admin panel
	$('.rail-color-picker').wpColorPicker(railOptions);

	$('.fill-color-picker').wpColorPicker(fillOptions);

	// Use select2 for our select fields
	$("#rpsb_position, #pspb_transition").select2({

	minimumResultsForSearch: Infinity

	});

	$("#include_post_types")
		.select2({placeholder: "Exclude post types"})
		.select2('val', pspb_params.include_post_types);

	$("#exclude_by_id")
		.select2({ placeholder: "Select post ID" })
		.select2('val', pspb_params.exclude_by_id);

    // Set checkbox value on change    
	$( '#hide_on_homepage, #hide_on_mobile, #pspb_reverse' ).change(function() {
            
		var $this = $(this);

		if($this.is(":checked")){

			$this.val('1');

		} else {

			$this.val('0');

		}

	});
    
})( jQuery );