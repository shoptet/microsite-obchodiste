<?php

class Migrations {

  const OPTION_PREFIX = '_migration_';
  const MIGRATIONS = [
    'fix_product_thumbnail_meta_ids',
  ];

  static function init() {
    add_action( 'init', [ get_called_class(), 'makeMigrations' ] );    
  }

  static function makeMigrations() {
    foreach( self::MIGRATIONS as $migration ) {
      $option = self::OPTION_PREFIX . $migration;
      if ( get_option( $option . '_done' ) !== false || get_option( $option . '_doing' ) !== false ) continue;
      add_option( $option . '_doing' , true );
      self::{$migration}();
      delete_option( $option . '_doing' );
      add_option( $option . '_done' , true );
    }
  }

  static function fix_product_thumbnail_meta_ids() {
    $query = new WP_Query( [
      'post_type' => 'product',
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
    ] );
    foreach( $query->posts as $post_id ) {
      $_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
      $thumbnail = get_post_meta( $post_id, 'thumbnail', true );
      delete_post_meta( $post_id, '_thumbnail_id' );
      if ( $thumbnail ) {
        add_post_meta( $post_id, '_thumbnail_id', $thumbnail );
      }
    }
  }

}