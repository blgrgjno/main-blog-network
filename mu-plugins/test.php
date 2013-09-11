<?php

function add_twitter_contactmethod( $contactmethods ) {
  // Add Twitter
  if ( !isset( $contactmethods['twitter'] ) )
    $contactmethods['twitter'] = 'Twitter';

$user_id = $_GET['user_id'];
$b = get_user_meta( $user_id );//, 'ea_sub_' . $subscription );
echo '<textarea>';
print_r( $b );
echo '</textarea>';

  return $contactmethods;
}
add_filter( 'user_contactmethods', 'add_twitter_contactmethod', 10, 1 );
