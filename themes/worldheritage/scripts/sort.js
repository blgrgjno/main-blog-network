jQuery(document).ready(function($) {		
	var postList = $('#post-list');

	postList.sortable({
		update: function(event, ui) {
			$('#loading-animation').show(); // Show the animate loading gif while waiting
 
			opts = {
				url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
				type: 'POST',
				async: true,
				cache: false,
				dataType: 'json',
				data:{
					action: 'post_sort', // Tell WordPress how to handle this ajax request
					order: postList.sortable('toArray').toString(), // Passes ID's of list items in	1,3,2 format
					nonce: sortnonce,
				},
				success: function(response) {
					$('#loading-animation').hide(); // Hide the loading animation
					console.log(response);
					return; 
				},
				error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
					alert('There was an error saving the updates');
					$('#loading-animation').hide(); // Hide the loading animation
					return; 
				}
			};
			$.ajax(opts);
		}
	});	
});