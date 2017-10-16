jQuery.noConflict(); // Reverts '$' variable back to other JS libraries

jQuery(document).ready(function($) {

	jQuery('body').on("click",'.PLUGINPREFIX_admin_toggle_button',function(e) {
		target_div = document.getElementById($(this).attr( 'target' ));
		$(target_div).fadeToggle(1800);
	});
	
	jQuery(document).on( 'click', '.PLUGINPREFIX_notice .notice-dismiss', function(e) {

		var id_to_send = e.target.id;
	
		console.log($(this).parent().attr("id"));
		console.log(jQuery(this).attr("id"));
		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'dismiss_admin_notice',
				PLUGINPREFIX_action: 'dismiss_admin_notice',
				notice_id: $(this).parent().attr("id"),
				notice_type: $(this).parent().attr("notice_type"),
			}
		});

	});
	
	jQuery(document).on('click', '.PLUGINPREFIX_file_upload', function(e) {		
		var PLUGINPREFIX_input_target = jQuery(this);
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
             PLUGINPREFIX_input_target.val(image_url);
			 
        });
    });
	
	jQuery(document).on('click', '.PLUGINPREFIX_clear_prevfield', function(e) {
		e.preventDefault();
		
		jQuery(this).prev().val('');
	
	});		
  
});