<?php

function create_google_terms ( $taxonomy, $level = 0 ) {
  $file_path = __DIR__ . '/categories.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    wp_die( 'Can not open file: ' . $file_path );
    return;
  }
  $i = 0;
  while ( $row = fgetcsv( $fp ) ) {
    $i++;
    if ( $level >= 1 && ! empty( $row[$level] ) ) continue;
    $term = get_term_from_row( $row );
    if ( $term['parent_name'] ) {
      if ( $parent_term = get_term_by( 'name', $term['parent_name'], $taxonomy ) ) {
        $term['parent_id'] = $parent_term->term_id;
      } else {
        var_dump( 'Row ' . $i . ': Term "' . $term['parent_name'] . '" not found' ); die();
      }
    }
    if ( ! empty ( get_term_by( 'name', $term['name'], $taxonomy ) ) ) {
      var_dump( 'Row ' . $i . ': Duplication term "' . $term['name'] . '". Skipping...' );
      continue;
    }
    wp_insert_term(
      $term['name'],
      $taxonomy,
      [ 'parent' => array_key_exists( 'parent_id', $term ) ? $term['parent_id'] : 0 ]
    );
    var_dump( 'Row ' . $i . ': Created term "' . $term['name'] . '" with parent "' . $term['parent_name'] . '" for ' . $taxonomy );
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

function migrate_terms ( $taxonomy ) {
  $file_path = __DIR__ . '/term_migration.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    wp_die( 'Can not open file: ' . $file_path );
    return;
  }
  $i = 1;
  global $wpdb;
  while ( $row = fgetcsv( $fp ) ) {
    $old_id = get_term_by( 'name', $row[0], $taxonomy );
    $new_id = get_term_by( 'name', $row[1], $taxonomy );
    if ( ! $old_id || ! $new_id ) {
      var_dump( 'Term(s) not found: ' . $row[0] . ' or ' . $row[1] );
    }
    // Update term
    $result = $wpdb->update( 
      'wp_term_relationships', // TABLE
      [ 'term_taxonomy_id' => $row[1] ], // SET
      [ 'term_taxonomy_id' => $row[0] ] // WHERE
    );
    // Update post meta
    $result = $wpdb->get_results('
      UPDATE wp_postmeta
        SET meta_value = ' . $row[1] . '
        WHERE
          meta_key IN ("category", "minor_category_1", "minor_category_2")
          AND
          mata_value = ' . $row[0] . '
    ');
    // Migrate descriptions
    var_dump( 'Migrated term: ' . $row[0] . ' > ' . $row[1] );
  }
}

add_action( 'admin_init', function () {
  // if ( get_option( 'create_google_product_categories_01' ) != 'completed' ) {
  //create_google_terms( 'customtaxonomy', 1 );
  //create_google_terms( 'producttaxonomy' );
  //   //update_option( 'create_google_product_categories_01', 'completed' );
  //die();
  // }
  //if ( get_option( 'migrate_google_product_categories_01' ) != 'completed' ) {
    //migrate_terms( 'customtaxonomy' );
    // migrate_terms( 'producttaxonomy' );
    //update_option( 'migrate_google_product_categories_01', 'completed' );
    //die();
  //}
} );