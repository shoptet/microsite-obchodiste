<?php

namespace Shoptet;

class DBXUtility {

  static function get_original_meta_data( $post_id ) {

    global $wpdb;
    
    $meta_list = $wpdb->get_results( "
      SELECT meta_key, meta_value
      FROM $wpdb->postmeta
      WHERE post_id = $post_id
      ORDER BY meta_id ASC
    ", ARRAY_A );
    
    $original_meta = [];
    foreach ( $meta_list as $metarow ) {
      $key = $metarow['meta_key'];
      $val = $metarow['meta_value'];
      $original_meta[$key] = $val;
    }

    return $original_meta;
  }

}