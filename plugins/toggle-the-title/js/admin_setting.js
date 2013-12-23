(function($) {
$(function() {
	bind_save_setting();
});

function bind_save_setting() {
	message ='<div id="message" class="updated"><p><strong>Settings saved</strong></p></div>';
	
	$('#toggle_title_submit').bind('click', _do_update_option);
	
	function _do_update_option() {
		if ($('#hook_toggle_btn_title_autosave').is(':checked')) var valve = 'checked="checked"';
		else var valve = '';
			
		var data = {
			action: 'update_title_options',
			checked: valve,
		};
		TitleToggler_post(data);
	}
}

function TitleToggler_post(data) {
	jQuery.post(ajaxurl, data, function(response) {
		$('#message').slideUp('slow', function() {
			$(this).remove();
		});		

		$('.wrap').append(message).find('#message').hide().slideDown('slow');
	});
}

})(jQuery);