<?php

namespace Shoptet;

class DBXStore {

  protected $table_name;
  protected $columns;

  function __construct( $post_type, array $columns ) {
    global $wpdb;
    $this->table_name = $wpdb->postmeta . '_' . $post_type;
    $this->columns = $columns;
  }

  protected function insert_row( $post_id, array $meta = [] ) {
    
    global $wpdb;

    // Take meta keys coresponding to table columns only
    $meta = array_filter( $meta, function( $val, $key ) {
      return in_array( $key, $this->columns ) && ! empty($val);
    }, ARRAY_FILTER_USE_BOTH );

    $data = array_merge(
      [ 'post_id' => intval($post_id) ],
      $meta
    );

    return $wpdb->insert( $this->table_name, $data );
  }

  public function create_table() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $this->table_name (
      post_id bigint(20) UNSIGNED NOT NULL,
      PRIMARY KEY (post_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
  }

  public function add_column( $column_name ) {
    global $wpdb;
    $wpdb->query( "ALTER TABLE $this->table_name ADD `$column_name` longtext NULL" );
  }

  public function delete_row( $post_id ) {
    global $wpdb;
    $wpdb->delete( $this->table_name, [ 'post_id' => intval($post_id) ] );
  }

  public function update_row( $data, $where ) {
    global $wpdb;
    return $wpdb->update( $this->table_name, $data, $where );
  }

  public function row_exists( $post_id ) {
    global $wpdb;
    $row = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $this->table_name WHERE post_id = %d", $post_id ) );
    return ( $row != null );
  }

  public function get_extended_meta_data( $post_id ) {

    if ( ! $this->row_exists($post_id) ) {
      return [];
    }

    global $wpdb;
    $columns = '`' . implode('`,`', $this->columns) . '`';
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

  public function maybe_insert_row( $post_id ) {

    if ( $this->row_exists($post_id) ) {
      return false;
    }
    
    $original_meta = DBXUtility::get_original_meta_data($post_id);
    return $this->insert_row($post_id, $original_meta);
  }

}