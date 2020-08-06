<?php

namespace Shoptet;

class DBXPostType {

  protected $post_type;
  protected $extended_meta_keys = [];
  protected $static_meta_data = [];
  protected $store;

  function __construct( $post_type ) {
    $this->post_type = $post_type;
  }

  public function init() {

    if ( empty($this->extended_meta_keys) ) {
      throw new \Exception( 'Extended meta keys not defined' );
    }

    $this->store = new DBXStore( $this->post_type, $this->extended_meta_keys );

    add_action( 'wp_insert_post', [ $this, 'action_insert_post' ], 10, 3 );
    add_action( 'delete_post', [ $this, 'action_delete_post' ], 10, 3 );

    add_filter( 'update_post_metadata_cache', [ $this, 'filter_update_meta_cache' ], 10, 2 );

    add_filter( 'add_post_metadata', [ $this, 'filter_update_meta' ], 10, 5 );
    add_filter( 'update_post_metadata', [ $this, 'filter_update_meta' ], 10, 5 );
    add_filter( 'delete_post_metadata', [ $this, 'filter_delete_meta' ], 10, 5 );
  }

  public function set_extended_meta_keys( array $extended_meta_keys ) {
    $this->extended_meta_keys = $extended_meta_keys;
  }

  public function set_static_meta_data( array $static_meta_data ) {
    $this->static_meta_data = $static_meta_data;
  }

  public function get_extended_meta_keys() {
    return $this->extended_meta_keys;
  }

  public function get_static_meta_data() {
    return $this->static_meta_data;
  }

  protected function get_normalized_static_meta_data() {
    return array_map( function( $val ) {
      return [ $val ];
    }, $this->static_meta_data );
  }
  
  public function action_insert_post( $post_id, $post, $update ) {
  
    // Check correct post type
    if ( $this->post_type != get_post_type($post_id) ) {
      return;
    }
  
    // Check correct post status
    if ( $post->post_status == 'trash' ) {
      return;
    }
    
    $this->store->maybe_insert_row($post_id);
  }

  public function action_delete_post( $post_id ) {
  
    // Check correct post type
    if ( $this->post_type != get_post_type($post_id) ) {
      return;
    }

    $this->store->delete_row($post_id);
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
      if ( $this->post_type == get_post_type($id) ) {
        $meta_data = $this->store->get_extended_meta_data($id);
        $cache[$id] = array_merge( $cache[$id], $this->get_normalized_static_meta_data() );
        $cache[$id] = array_merge( $cache[$id], $meta_data );
      }
      
      wp_cache_add( $id, $cache[ $id ], 'post_meta' );
    }

    return $cache;
  }
  
  public function filter_update_meta( $check, $post_id, $meta_key, $meta_value, $prev_value ) {

    // Check correct meta key
    if ( ! in_array( $meta_key, $this->extended_meta_keys ) ) {
      return $check;
    }
    
    // Check correct post type
    if ( $this->post_type != get_post_type($post_id) ) {
      return $check;
    }

    // Do not update static meta key
    if ( isset( $this->static_meta_data[$meta_key] ) ) {
      return $this->static_meta_data[$meta_key];
    }
    
    // Make sure a row in table exists
    $this->store->maybe_insert_row($post_id);
  
    $where = [ 'post_id' => $post_id ];

    // Handle a previous value
    if ( ! empty($prev_value) ) {
      $prev_value = maybe_serialize($prev_value);
      $where[$meta_key] = $prev_value;
    }

    $updated = $this->store->update_row(
      [ $meta_key => maybe_serialize( $meta_value ) ],
      $where
    );

    wp_cache_delete( $post_id, 'post_meta' );

    return $updated;
  }

  public function filter_delete_meta( $check, $post_id, $meta_key, $meta_value, $delete_all ) {

    // Check correct meta key
    if ( ! in_array( $meta_key, $this->extended_meta_keys ) ) {
      return $check;
    }
    
    // Check correct post type
    if ( $this->post_type != get_post_type($post_id) ) {
      return $check;
    }
    
    $where = [];
    
    // Handle deleting all meta keys
    if ( ! $delete_all ) {
      $where['post_id'] = $post_id;
    }    

    $updated = $this->store->update_row( [ $meta_key => null ], $where );

    if ( ! $delete_all ) {
      wp_cache_delete( $post_id, 'post_meta' );
    } else {
      // For simplicity delete all cache items instead of affected posts only
      wp_cache_flush(); 
    }

    return $updated;
  }

}
