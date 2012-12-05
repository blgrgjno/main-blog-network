jQuery( document ).ready( function() {
	window.send_to_editor = function( html ) {
		alert( "Please set as a featured image to set a profile picture" );
		tb_remove();
	};
} );
function WPSetThumbnailID( id ) {
	tb_remove();
	var user_id = jQuery( "#metronet_profile_id" ).val();
	var post_id = jQuery( "#metronet_post_id" ).val();
	jQuery.post( ajaxurl, { action: "metronet_add_thumbnail", thumbnail_id: id, post_id: post_id, user_id: user_id }, function( response ) {
		jQuery( "#metronet-profile-image" ).html( response );
		tb_remove();
	} );
};
function WPSetThumbnailHTML( html ) {
};