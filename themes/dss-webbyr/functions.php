<?php

// REMOVE TWENTY ELEVEN DEFAULT HEADER IMAGES
function wptips_remove_header_images() {
  unregister_default_headers( array('wheel','shore','trolley','pine-cone','chessboard','lanterns','willow','hanoi') );

  // remove singular so that we can use sidebars
  remove_filter( 'body_class', 'twentyeleven_body_classes' );
}
add_action( 'after_setup_theme', 'wptips_remove_header_images', 11 );

?>