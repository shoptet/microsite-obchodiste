<?php

class ShoptetCategoriesMap {

  static function map() {
    $shoptet_categories = self::getShoptetCategories();
    $wp_categories = self::getWPCategories();

    $matches = 0;
    foreach ( $shoptet_categories as $shoptet_id => $shoptet_path ) {
      $match = false;
      foreach ( $wp_categories as $wp_id => $wp_path ) {
        if ( $shoptet_path == $wp_path ) {
          update_term_meta( $wp_id, 'shoptet_category_id', $shoptet_id );
          $matches++;
          $match = true;
          break;
        }
      }
      if ( ! $match ) {
        error_log( sprintf( 'No match found: [%d] "%s"', $shoptet_id, $shoptet_path ) );
      }
    }

    error_log( sprintf( '%s shoptet categories', count( $shoptet_categories ) ) );
    error_log( sprintf( '%s wp categories', count( $wp_categories ) ) );
    error_log( sprintf( '%s total matches', $matches ) );
  }

  static function getShoptetCategories() {
    $file_path = __DIR__ . '/shoptet_categories.csv';
    $fp = fopen( $file_path, 'r' );
    if ( ! $fp ) {
      error_log( 'Can not open file: ' . $file_path );
      return;
    }

    $categories_by_id = [];
    while ( $row = fgetcsv( $fp, 0, '	' ) ) {
      $id = $row[0];
      $path = $row[5];
      $categories_by_id[ $id ] = $path;
    }

    return $categories_by_id;
  }

  static function getWPCategories() {
    $wp_term_query = new WP_Term_Query( [
      'taxonomy' => 'producttaxonomy',
      'hide_empty' => false,
    ] );
    $parent_list_args = [
      'link' => false,
      'separator' => ' > ',
      'inclusive' => false,
    ];
    
    $categories_by_id = [];
    foreach( $wp_term_query->terms as $term ) {
      $path = get_term_parents_list( $term->term_id, 'producttaxonomy', $parent_list_args );
      $path .= $term->name;
      $categories_by_id[ $term->term_id ] = $path;
    }
    return $categories_by_id;
  }
  
}