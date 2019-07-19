<?php

function stop_the_insanity () {
	global $wpdb, $wp_object_cache;

	$wpdb->queries = [];

	if ( is_object( $wp_object_cache ) ) {
		$wp_object_cache->group_ops      = [];
		$wp_object_cache->stats          = [];
		$wp_object_cache->memcache_debug = [];
		$wp_object_cache->cache          = [];

		if ( method_exists( $wp_object_cache, '__remoteset' ) ) {
			$wp_object_cache->__remoteset();
		}
	}
}

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
    echo 'Can not open file: ' . $file_path . PHP_EOL;
    return;
  }
  wp_defer_term_counting( true );
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
        echo 'Row ' . $i . ': Term "' . $term['parent_name'] . '" not found' . PHP_EOL;
        return;
      }
    }
    // if ( find_inserted_term_by_name( $term['name'], $inserted_terms ) ) {
    //   echo 'Row ' . $i . ': Duplication term "' . $term['name'] . '". Skipping...' . PHP_EOL;
    //   continue;
    // }
    $inserted_term = wp_insert_term(
      $term['name'],
      $taxonomy,
      [ 'parent' => array_key_exists( 'parent_id', $term ) ? $term['parent_id'] : 0 ]
    );
    if ( is_wp_error( $inserted_term ) ) {
      echo $inserted_term . PHP_EOL;
      continue;
    }
    $inserted_terms[] = get_term( $inserted_term['term_id'], $taxonomy );
    // echo 'Row ' . $i . ': Created term "' . $term['name'] . '" with parent "' . $term['parent_name'] . '" for ' . $taxonomy . PHP_EOL;
    stop_the_insanity();
  }
  echo 'Succsessfully created ' . $i . ' terms' . PHP_EOL;
  fclose( $fp );
  wp_defer_term_counting( false );
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

function rename_old_terms ( $taxonomy ) {
  $file_path = __DIR__ . '/term_migration.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    echo 'Can not open file: ' . $file_path . PHP_EOL;
    return;
  }
  $i = 0;
  global $wpdb;
  while ( $row = fgetcsv( $fp ) ) {
    if ( $row[0] != ( $row[1] . ' legacy' ) ) continue;
    $term = get_term_by( 'name', $row[1], $taxonomy );
    if ( ! $term ) continue;
    wp_update_term( $term->term_id, $taxonomy, [
      'name' => $term->name . ' legacy',
      'slug' => $term->slug . '-legacy',
    ]);
    $i++;
  }
  echo 'Renamed ' . $i . ' terms' . PHP_EOL;
}

function migrate_terms ( $taxonomy ) {
  $file_path = __DIR__ . '/term_migration.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    echo 'Can not open file: ' . $file_path;
    return;
  }
  $terms = [];
  $i = 0;
  global $wpdb;
  while ( $row = fgetcsv( $fp ) ) {
    $old = get_term_by( 'name', $row[0], $taxonomy );
    $new = get_term_by( 'name', $row[1], $taxonomy );
    if ( ! $old || ! $new ) {
      echo 'Term(s) not found: ' . $row[0] . ' or ' . $row[1] . PHP_EOL;
      continue;
    }
    $terms[] = $new->term_id;
    $terms[] = $old->term_id;
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
          meta_value = ' . $old->term_id . '
    ');
    $i++;
  }
  echo 'Migrated ' . $i . ' terms' . PHP_EOL;
  wp_update_term_count_now( $terms, $taxonomy );
}

function remove_old_terms ( $taxonomy ) {
  $file_path = __DIR__ . '/term_migration.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    echo 'Can not open file: ' . $file_path;
    return;
  }
  $i = 0;
  while ( $row = fgetcsv( $fp ) ) {
    $old = get_term_by( 'name', $row[0], $taxonomy );
    if ( ! $old ) {
      echo 'Term(s) not found: ' . $row[0] . PHP_EOL;
      continue;
    }
    wp_delete_term( $old->term_id, $taxonomy );
    $i++;
  }
  echo 'Removed ' . $i . ' terms' . PHP_EOL;
}

function migrate_google_product_categories () {
  echo 'Migrating customtaxonomy...' . PHP_EOL;
  rename_old_terms( 'customtaxonomy' );
  stop_the_insanity();
  create_google_terms( 'customtaxonomy', 1 );
  stop_the_insanity();
  migrate_terms( 'customtaxonomy' );
  stop_the_insanity();
  remove_old_terms( 'customtaxonomy' );
  stop_the_insanity();
  echo 'Migrating product taxonomy...' . PHP_EOL;
  rename_old_terms( 'producttaxonomy' );
  stop_the_insanity();
  create_google_terms( 'producttaxonomy' );
  stop_the_insanity();
  migrate_terms( 'producttaxonomy' );
  stop_the_insanity();
  remove_old_terms( 'producttaxonomy' );
}