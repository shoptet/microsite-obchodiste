<?php

namespace Shoptet;

class DBXManager {

  protected $dbx_post_types = [];
  
  public function add_post_type( $post_type ) {

    if ( array_key_exists($post_type, $this->dbx_post_types) ) {
      throw new \Exception( 'Post type has been already added' );
    }

    $this->dbx_post_types[$post_type] = new DBXPostType($post_type);
  }

  public function get_registered_post_types() {
    return array_keys($this->dbx_post_types);
  }

  public function get_registered_post_type( $post_type ) {

    if ( ! array_key_exists($post_type, $this->dbx_post_types) ) {
      throw new \Exception( 'Post type not registered' );
    }

    return $this->dbx_post_types[$post_type];
  }

  public function set_extended_meta_keys( $post_type, array $extended_meta_keys ) {

    if ( ! array_key_exists($post_type, $this->dbx_post_types) ) {
      throw new \Exception( 'Post type not added' );
    }

    $this->dbx_post_types[$post_type]->set_extended_meta_keys($extended_meta_keys);
  }

  public function set_static_meta_data( $post_type, array $static_meta_data ) {

    if ( ! array_key_exists($post_type, $this->dbx_post_types) ) {
      throw new \Exception( 'Post type not added' );
    }

    $this->dbx_post_types[$post_type]->set_static_meta_data($static_meta_data);
  }

  public function init() {
    
    foreach( $this->dbx_post_types as $dbx_post_type ) {
      $dbx_post_type->init();
    }

    add_filter( 'update_post_metadata_cache', [ $this, 'filter_update_meta_cache' ], 10, 2 );
  }

  public function filter_update_meta_cache( $check, array $post_ids ) {

    // This code taken from core WP function update_meta_cache

    global $wpdb;

    $ids = [];
    $cache = [];
    foreach ( $post_ids as $id ) {
      $cached_post = wp_cache_get( $id, 'post_meta' );
      if ( false === $cached_post ) {
        $ids[] = $id;
      } else {
        $cache[ $id ] = $cached_post;
      }
    }
 
    if ( empty($ids) ) {
      return $cache;
    }

    // Get meta info.
    $id_list = join( ',', $ids );
    $table = $wpdb->postmeta;
    $meta_list = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM $table WHERE post_id IN ($id_list) ORDER BY meta_id ASC", ARRAY_A );
    if ( ! empty( $meta_list ) ) {
      foreach ( $meta_list as $metarow ) {
        $mpid = intval( $metarow[ 'post_id' ] );
        $mkey = $metarow['meta_key'];
        $mval = $metarow['meta_value'];

        // Force subkeys to be array type.
        if ( ! isset( $cache[ $mpid ] ) || ! is_array( $cache[ $mpid ] ) ) {
          $cache[ $mpid ] = [];
        }
        if ( ! isset( $cache[ $mpid ][ $mkey ] ) || ! is_array( $cache[ $mpid ][ $mkey ] ) ) {
          $cache[ $mpid ][ $mkey ] = [];
        }

        // Add a value to the current pid/key.
        $cache[ $mpid ][ $mkey ][] = $mval;
      }
    }

    foreach ( $ids as $id ) {
      if ( ! isset( $cache[ $id ] ) ) {
        $cache[ $id ] = [];
      }

      // Merge all original meta data with exteded and static ones
      $post_type = get_post_type($id);
      $registered_post_types = $this->get_registered_post_types();
      if ( in_array( $post_type, $registered_post_types ) ) {
        $dbx_post_type = $this->dbx_post_types[$post_type];
        $meta_data = $dbx_post_type->get_store()->get_extended_meta_data($id);
        $cache[$id] = array_merge( $cache[$id], $dbx_post_type->get_normalized_static_meta_data() );
        $cache[$id] = array_merge( $cache[$id], $meta_data );
      }
      
      wp_cache_add( $id, $cache[ $id ], 'post_meta' );
    }

    return $cache;
  }

}
