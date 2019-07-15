<?php

function find_inserted_term_by_name ( $term_name, $inserted_terms ) {
  foreach( $inserted_terms as $inserted_term ) {
    if ( $term_name == $inserted_term->name ) return $inserted_term;
  }
  return null;
}

function create_google_terms ( $taxonomy, $level = 0 ) {
  $file_path = __DIR__ . '/categories-v2.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    wp_die( 'Can not open file: ' . $file_path );
    return;
  }
  $i = 0;
  $inserted_terms = [];
  while ( $row = fgetcsv( $fp ) ) {
    $i++;
    if ( $level >= 1 && ! empty( $row[$level] ) ) continue;
    $term = get_term_from_row( $row );
    if ( $term['parent_name'] ) {
      if ( $parent_term = find_inserted_term_by_name( $term['parent_name'], $inserted_terms ) ) {
        $term['parent_id'] = $parent_term->term_id;
      } else {
        var_dump( 'Row ' . $i . ': Term "' . $term['parent_name'] . '" not found' ); die();
      }
    }
    // if ( find_inserted_term_by_name( $term['name'], $inserted_terms ) ) {
    //   var_dump( 'Row ' . $i . ': Duplication term "' . $term['name'] . '". Skipping...' );
    //   continue;
    // }
    $inserted_term = wp_insert_term(
      $term['name'],
      $taxonomy,
      [ 'parent' => array_key_exists( 'parent_id', $term ) ? $term['parent_id'] : 0 ]
    );
    if ( is_wp_error( $inserted_term ) ) {
      var_dump( $inserted_term );
      continue;
    }
    $inserted_terms[] = get_term( $inserted_term['term_id'], $taxonomy );
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

function rename_terms ( $taxonomy ) {
  $file_path = __DIR__ . '/term_migration.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    wp_die( 'Can not open file: ' . $file_path );
    return;
  }
  $i = 1;
  global $wpdb;
  while ( $row = fgetcsv( $fp ) ) {
    if ( $row[0] != ( $row[1] . ' legacy' ) ) continue;
    $term = get_term_by( 'name', $row[1], $taxonomy );
    if ( ! $term ) continue;
    wp_update_term( $term->term_id, $taxonomy, [
      'name' => $term->name . ' legacy',
      'slug' => $term->slug . '-legacy',
    ]);
    var_dump( 'Renamed term: ' . $term->name );
  }
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
    $old = get_term_by( 'name', $row[0], $taxonomy );
    $new = get_term_by( 'name', $row[1], $taxonomy );
    if ( ! $old || ! $new ) {
      var_dump( 'Term(s) not found: ' . $row[0] . ' or ' . $row[1] );
      continue;
    }
    // Update term
    $result = $wpdb->update( 
      'wp_term_relationships', // TABLE
      [ 'term_taxonomy_id' => $new->term_id ], // SET
      [ 'term_taxonomy_id' => $old->term_id ] // WHERE
    );
    // Update post meta
    $result = $wpdb->get_results('
      UPDATE wp_postmeta
        SET meta_value = ' . $new->term_id . '
        WHERE
          meta_key IN ("category", "minor_category_1", "minor_category_2")
          AND
          mata_value = ' . $old->term_id . '
    ');
    var_dump( 'Migrated term: ' . $row[0] . ' > ' . $row[1] );
  }
  $result = $wpdb->get_results('
    UPDATE wp_term_taxonomy tt
      SET count = (SELECT count(p.ID)
      FROM wp_term_relationships tr
      LEFT JOIN wp_posts p ON p.ID = tr.object_id
      WHERE tr.term_taxonomy_id = tt.term_taxonomy_id)
  ');
}

add_filter( 'register_taxonomy_args', function ( $args, $taxonomy ) {
  if (
    get_option( 'create_google_product_categories_producttaxonomy_01' ) != 'completed' &&
    $taxonomy == 'producttaxonomy'
  ) {
    $args['hierarchical'] = false;
  }
  return $args;
}, 10, 2 );

add_action( 'admin_init', function () {

  if ( get_option( 'rename_google_product_categories_customtaxonomy_01' ) != 'completed' ) {
    rename_terms('customtaxonomy');
    update_option( 'rename_google_product_categories_customtaxonomy_01', 'completed' );
    return;
  }

  if ( get_option( 'create_google_product_categories_customtaxonomy_01' ) != 'completed' ) {
    create_google_terms( 'customtaxonomy', 1 );
    update_option( 'create_google_product_categories_customtaxonomy_01', 'completed' );
    return;
  }

  if ( get_option( 'rename_google_product_categories_producttaxonomy_01' ) != 'completed' ) {
    rename_terms('producttaxonomy');
    update_option( 'rename_google_product_categories_producttaxonomy_01', 'completed' );
    return;
  }

  if ( get_option( 'create_google_product_categories_producttaxonomy_01' ) != 'completed' ) {
    create_google_terms( 'producttaxonomy' );
    update_option( 'create_google_product_categories_producttaxonomy_01', 'completed' );
    return;
  }
  if ( get_option( 'migrate_google_product_categories_customtaxonomy_01' ) != 'completed' ) {
    migrate_terms( 'customtaxonomy' );
    global $wp_object_cache;
    $wp_object_cache->flush();
    update_option( 'migrate_google_product_categories_customtaxonomy_01', 'completed' );
    return;
  }
  if ( get_option( 'migrate_google_product_categories_producttaxonomy_01' ) != 'completed' ) {
    migrate_terms( 'producttaxonomy' );
    global $wp_object_cache;
    $wp_object_cache->flush();
    update_option( 'migrate_google_product_categories_producttaxonomy_01', 'completed' );
    return;
  }
} );