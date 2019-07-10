<?php

function create_google_terms ( $taxonomy, $level = 0 ) {
  $file_path = __DIR__ . '/categories.csv';
  $fp = fopen( $file_path, 'r' );
  $i = 0;
  while ( $row = fgetcsv( $fp ) ) {
    if ( $level >= 1 && ! empty( $row[$level] ) ) continue;
    $term = get_term_from_row( $row );
    if ( $term['parent_name'] ) {
      if ( $parent_term = get_term_by( 'name', $term['parent_name'], $taxonomy ) ) {
        $term['parent_id'] = $parent_term->term_id;
      } else {
        var_dump( 'Term "' . $term['parent_name'] . '" not found' ); die();
      }
    }
    wp_insert_term(
      $term['name'],
      $taxonomy,
      [ 'parent' => array_key_exists( 'parent_id', $term ) ? $term['parent_id'] : 0 ]
    );
    var_dump( 'Row ' . $i . ': Created term "' . $term['name'] . '" with parent "' . $term['parent_name'] . '" for ' . $taxonomy );
    $i++;
  }
  fclose( $fp );
}

function get_term_from_row ( $row ) {
  $term = [
    'name' => null,
    'parent_name' => null,
  ];
  foreach ( $row as $column ) {
    if ( empty( $column ) ) break;
    if ( $term['name'] ) $term['parent_name'] = $term['name'];
    $term['name'] = $column;
  }
  return $term;
}

function migrate_terms () {
}

add_action( 'admin_init', function () {
  if ( get_option( 'create_google_product_categories_01' ) != 'completed' ) {
    //create_google_terms( 'customtaxonomy', 1 );
    //create_google_terms( 'producttaxonomy' );
    //update_option( 'create_google_product_categories_01', 'completed' );
    //die();
  } else if ( get_option( 'migrate_google_product_categories_01' ) != 'completed' ) {
    // migrate_terms( 'producttaxonomy' );
    // migrate_terms( 'customtaxonomy' );
    //update_option( 'migrate_google_product_categories_01', 'completed' );
  }
} );