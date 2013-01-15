<?php
/**
 * Plugin Custom User Meta
 */
 
/**
 * Create comment notification user meta field
 */

function cbent_macn_add_user_meta_field( $user ) { ?>

	<h3>Comment Email Notification</h3>

	<table class="form-table">

		<tr>
			<th><label for="comment_notify">Comment Email Notification</label></th>

			<td>
				<input type="checkbox" name="cbnet_macn_comment_notify" value="true" <?php checked( true == get_the_author_meta( 'cbnet_macn_comment_notify', $user->ID ) ); ?>>
				<span class="description">Receive email notification of comments to all posts, regardless of post author</span>
			</td>
		</tr>

	</table>
<?php }
add_action( 'show_user_profile', 'cbent_macn_add_user_meta_field' );
add_action( 'edit_user_profile', 'cbent_macn_add_user_meta_field' );


/**
 * Save comment notification user meta data
 */
function cbent_macn_save_user_meta_data( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	update_usermeta( $user_id, 'cbnet_macn_comment_notify', ( isset( $_POST['cbnet_macn_comment_notify'] ) ? true : false ) );
}
add_action( 'personal_options_update', 'cbent_macn_save_user_meta_data' );
add_action( 'edit_user_profile_update', 'cbent_macn_save_user_meta_data' );

?>