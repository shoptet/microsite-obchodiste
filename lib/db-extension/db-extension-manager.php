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

  public function get_extended_meta_keys( $post_type ) {

    if ( ! array_key_exists($post_type, $this->dbx_post_types) ) {
      throw new \Exception( 'Post type not added' );
    }

    return $this->dbx_post_types[$post_type]->get_extended_meta_keys();
  }

  public function get_static_meta_data( $post_type ) {

    if ( ! array_key_exists($post_type, $this->dbx_post_types) ) {
      throw new \Exception( 'Post type not added' );
    }

    return $this->dbx_post_types[$post_type]->get_static_meta_data();
  }

  public function init() {
    foreach( $this->dbx_post_types as $dbx_post_type ) {
      $dbx_post_type->init();
    }
  }

}
