<?php

/**
 * Hide redundant meta boxes in wholesaler edit page
 */
add_filter( 'add_meta_boxes', function() {
   // Hide category meta box
  remove_meta_box( 'tagsdiv-wholesaler_category', 'custom', 'side' );
	remove_meta_box( 'wholesaler_categorydiv', 'custom', 'side' ); // if taxonomy is hierarchical
   // Hide featured image metabox
  remove_meta_box( 'postimagediv', 'custom', 'side' );
} );


/**
 * Set wholesaler logo as featured image
 */
add_filter( 'acf/update_value/name=logo', function( $value, $post_id, $field ) {
  // Not the correct post type, bail out
	if ( 'custom' !== get_post_type( $post_id ) ) {
		return $value;
	}
  // Skip empty value
  if ( $value != ''  ) {
    // Add the value which is the image ID to the _thumbnail_id meta data for the current post
    add_post_meta( $post_id, '_thumbnail_id', $value );
  }
  return $value;
}, 10, 3 );
