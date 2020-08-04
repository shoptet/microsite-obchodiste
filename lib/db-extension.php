<?php

namespace Shoptet;

class DBX {

  protected $post_type;
  protected $table_name;
  protected $meta_keys;
  protected $static_meta_data = [];

  function __construct( $post_type ) {
    global $wpdb;
    $this->post_type = $post_type;
    $this->table_name = $wpdb->postmeta . '_' . $this->post_type;
  }

  public function init() {

    if ( ! isset( $this->meta_keys ) ) {
      throw new \Exception( 'Meta keys not defined' );
    }

    add_action( 'init', [ $this, 'action_check_table' ] );

    add_action( 'wp_insert_post', [ $this, 'action_insert_post' ], 10, 3 );
    add_action( 'delete_post', [ $this, 'action_delete_post' ], 10, 3 );

    add_filter( 'update_post_metadata_cache', [ $this, 'filter_update_meta_cache' ], 10, 2 );

    add_filter( 'add_post_metadata', [ $this, 'filter_update_meta' ], 10, 5 );
    add_filter( 'update_post_metadata', [ $this, 'filter_update_meta' ], 10, 5 );
    add_filter( 'delete_post_metadata', [ $this, 'filter_delete_meta' ], 10, 5 );
  }

  public function set_meta_keys( array $meta_keys ) {
    $this->meta_keys = $meta_keys;
  }

  public function set_static_meta_data( array $static_meta_data ) {
    $this->static_meta_keys = $static_meta_data;
  }
  
  protected function row_exists( $post_id ) {
    global $wpdb;
    $row = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $this->table_name WHERE post_id = %d", $post_id ) );
    return ( $row != null );
  }

  protected function insert_row( $post_id, array $meta = [] ) {
    
    global $wpdb;

    $data = array_merge(
      [ 'post_id' => intval($post_id) ],
      $meta
    );

    return $wpdb->insert( $this->table_name, $data );
  }

  protected function get_original_meta_data( $post_id ) {
    global $wpdb;
    
    $key_list = join( ',', $this->meta_keys );
    $meta_list = $wpdb->get_results( "
      SELECT meta_key, meta_value
      FROM $wpdb->postmeta
      WHERE post_id = $post_id AND meta_key IN ($key_list)
      ORDER BY meta_id ASC
    ", ARRAY_A );
    
    $original_meta = [];
    foreach ( $meta_list as $metarow ) {
      $mkey = $metarow['meta_key'];
      $mval = $metarow['meta_value'];
      $original_meta[$mkey] = $mval;
    }

    return $original_meta;
  }

  protected function get_meta_data( $post_id ) {
    $meta_data = [];

    if ( ! $this->row_exists($post_id) ) {
      return $meta_data;
    }

    global $wpdb;
    $columns = join( ',', $this->meta_keys );
    $meta_data = $wpdb->get_row( $wpdb->prepare( "SELECT $columns FROM $this->table_name WHERE post_id = %d", $post_id ), ARRAY_A );

    // Remove empty values and normalize meta data
    $normalized_meta = [];
    foreach ( $meta_data as $key => $value ) {
      if ( ! empty($value) ) {
        $normalized_meta[$key] = [$value];
      }
    }

    return $normalized_meta;
  }

  protected function maybe_insert_row( $post_id ) {
    if ( ! $this->row_exists($post_id) ) {
      $original_meta = $this->get_original_meta_data($post_id);
      $this->insert_row($post_id, $original_meta);
    }
  }

  public function action_check_table() {
    // TODO
  }
  
  public function action_insert_post( $post_id, $post, $update ) {
  
    if ( $this->post_type != get_post_type($post_id) ) {
      return;
    }
  
    if ( $post->post_status == 'trash' ) {
      return;
    }
    
    $this->maybe_insert_row($post_id);
  }

  public function action_delete_post( $post_id ) {
  
    if ( $this->post_type != get_post_type($post_id) ) {
      return;
    }
    
    global $wpdb;
    $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE post_id = %d", $post_id ) );
  }

  public function filter_update_meta_cache( $check, array $post_ids ) {
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

      if ( $this->post_type == get_post_type($id) ) {
        $meta_data = $this->get_meta_data($id);
        $cache[$id] = array_merge( $cache[$id], $this->static_meta_data );
        $cache[$id] = array_merge( $cache[$id], $meta_data );
      }
      
      wp_cache_add( $id, $cache[ $id ], 'post_meta' );
    }

    return $cache;
  }
  
  public function filter_update_meta( $check, $post_id, $meta_key, $meta_value, $prev_value ) {
    
    if ( $this->post_type != get_post_type($post_id) ) {
      return $check;
    }

    if ( isset( $this->static_meta_keys[$meta_key] ) ) {
      return $this->static_meta_keys[$meta_key];
    }
    
    if ( ! in_array( $meta_key, $this->meta_keys ) ) {
      return $check;
    }

    $this->maybe_insert_row($post_id);
  
    global $wpdb;
    $where = [ 'post_id' => $post_id ];

    if ( ! empty($prev_value) ) {
      $prev_value = maybe_serialize($prev_value);
      $where[$meta_key] = $prev_value;
    }

    $updated = $wpdb->update(
      $this->table_name,
      [ $meta_key => maybe_serialize( $meta_value ) ],
      $where
    );

    wp_cache_delete( $post_id, 'post_meta' );

    return $updated;
  }

  public function filter_delete_meta( $check, $post_id, $meta_key, $meta_value, $delete_all ) {

    if ( $this->post_type != get_post_type($post_id) ) {
      return $check;
    }
    
    if ( ! in_array( $meta_key, $this->meta_keys ) ) {
      return $check;
    }

    global $wpdb;
    $where = [];

    if ( ! $delete_all ) {
      $where['post_id'] = $post_id ;
    }

    $updated = $wpdb->update(
      $this->table_name,
      [ $meta_key => null ],
      $where
    );

    if ( ! $delete_all ) {
      wp_cache_delete( $post_id, 'post_meta' );
    } else {
      wp_cache_flush(); // Delete all cache items
    }

    return $updated;
  }

}
