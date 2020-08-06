<?php

namespace Shoptet;

class DBXUtility {

  static function get_original_meta_data( $post_id ) {

    global $wpdb;
    
    $meta_list = $wpdb->get_results( $wpdb->prepare( "
      SELECT meta_key, meta_value
      FROM $wpdb->postmeta
      WHERE post_id = %d
      ORDER BY meta_id ASC
    ", $post_id ), ARRAY_A );
    
    $original_meta = [];
    foreach ( $meta_list as $metarow ) {
      $key = $metarow['meta_key'];
      $val = $metarow['meta_value'];
      $original_meta[$key] = $val;
    }

    return $original_meta;
  }

  static function delete_original_meta_data( $post_id, array $meta_keys ) {

    global $wpdb;
    
    foreach ( $meta_keys as $key ) {
      $wpdb->query( $wpdb->prepare( "
        DELETE FROM $wpdb->postmeta
        WHERE post_id = %d
        AND meta_key = %s
      ", $post_id, $key ) );
    }
  }
}