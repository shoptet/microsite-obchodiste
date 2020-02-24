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
    echo 'Can not open file: ' . $file_path . PHP_EOL;
    return;
  }
  wp_defer_term_counting( true );
  $i = 0;
  $inserted_terms = [];
  while ( $row = fgetcsv( $fp ) ) {
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
      echo $inserted_term->get_error_message() . PHP_EOL;
      continue;
    }
    $inserted_terms[] = get_term( $inserted_term['term_id'], $taxonomy );
    // echo 'Row ' . $i . ': Created term "' . $term['name'] . '" with parent "' . $term['parent_name'] . '" for ' . $taxonomy . PHP_EOL;
    stop_the_insanity();
    $i++;
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

function migrate_terms ( $post_type, $taxonomy ) {
  $file_path = __DIR__ . '/term_migration.csv';
  $fp = fopen( $file_path, 'r' );
  if ( ! $fp ) {
    echo 'Can not open file: ' . $file_path;
    return;
  }

  $terms_to_recount = [];
  $migration = [];
  while ( $row = fgetcsv( $fp ) ) {
    $old = get_term_by( 'name', $row[0], $taxonomy );
    $new = get_term_by( 'name', $row[1], $taxonomy );
    $migration[ $old->term_id ] = $new->term_id;
    $terms_to_recount[] = $new->term_id;
    $terms_to_recount[] = $old->term_id;
  }

  fclose( $fp );

  $wp_query = new WP_Query( [
    'posts_per_page' => -1,
    'post_type' => $post_type,
    'post_status' => 'any',
  ] );
  
  $i = 0;
  $posts_with_cat = 0;
  $posts_with_mc1 = 0;
  $posts_with_mc2 = 0;
  foreach( $wp_query->posts as $post ) {
    $terms_to_replace = [];
    $old_cat = get_post_meta( $post->ID, 'category', true );
    $old_mc1 = get_post_meta( $post->ID, 'minor_category_1', true );
    $old_mc2 = get_post_meta( $post->ID, 'minor_category_2', true );

    if ( $taxonomy == 'producttaxonomy' ) {
      $terms = wp_get_post_terms( $post->ID, $taxonomy, ['fields' => 'ids'] );
      if ( ! empty( $terms ) ) {
        $old_cat = $terms[0];
      }
    }

    // Set new main category
    if ( $old_cat && ! empty( $migration[ $old_cat ] ) ) {
      $new_cat = $migration[ $old_cat ];
      $posts_with_cat++;
    } else {
      echo 'Skipped post ' . $post->ID . PHP_EOL;
      continue;
    }

    // Set new first minor category
    if ( $old_mc1 && ! empty( $migration[ $old_mc1 ] ) && $migration[ $old_mc1 ] != $new_cat ) {
      $new_mc1 = $migration[ $old_mc1 ];
      $posts_with_mc1++;
    } elseif ( $old_mc2 && ! empty( $migration[ $old_mc2 ] ) && $migration[ $old_mc2 ] != $new_cat ) {
      $new_mc1 = $migration[ $old_mc2 ];
      $posts_with_mc1++;
    } else {
      $new_mc1 = NULL;
    }

    // Set new second minor category
    if ( $old_mc2 && $new_mc1 && ! empty( $migration[ $old_mc2 ] ) && $migration[ $old_mc2 ] != $new_mc1 ) {
      $new_mc2 = $migration[ $old_mc2 ];
      $posts_with_mc2++;
    } else {
      $new_mc2 = NULL;
    }

    update_field( 'category', $new_cat, $post->ID );
    $terms_to_replace[] = $new_cat;
    if ( $new_mc1 ) {
      update_field( 'minor_category_1', $new_mc1, $post->ID );
      $terms_to_replace[] = $new_mc1;
    }
    if ( $new_mc2 ) {
      update_field( 'minor_category_2', $new_mc2, $post->ID );
      $terms_to_replace[] = $new_mc2;
    }

    wp_set_post_terms( $post->ID, $terms_to_replace, $taxonomy, false ); // Replace post terms
    $i++;
    //echo 'Migrated post ' . $post->ID . ' ( "' . $old_cat . '", "' . $old_mc1 . '", "' . $old_mc2 . '" ) -> ( "' . $new_cat . '", "' . $new_mc1 . '", "' . $new_mc2 . '" ) ' . PHP_EOL;
  }

  echo 'Successfully migrated '. $posts_with_cat . ' posts with main category' . PHP_EOL;
  echo 'Successfully migrated '. $posts_with_mc1 . ' posts with minor category 1' . PHP_EOL;
  echo 'Successfully migrated '. $posts_with_mc2 . ' posts with minor category 2' . PHP_EOL;
  wp_update_term_count_now( $terms_to_recount, $taxonomy );
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

function generate_redirect_csv ( $taxonomy ) {
  $fp_in = fopen( __DIR__ . '/term_migration.csv', 'r' );
  $fp_out = fopen( __DIR__ . '/redirects_' . $taxonomy . '.csv', 'w' );

  fwrite( $fp_out, 'source,target,regex,type,code,match' . PHP_EOL );
  
  while ( $row = fgetcsv( $fp_in ) ) {
    $old = get_term_by( 'name', $row[0], $taxonomy );
    $new = get_term_by( 'name', $row[1], $taxonomy );
    if ( $old->slug == $new->slug . '-legacy' ) {
      continue;
    }
    $old_link = wp_make_link_relative( get_term_link( $old, $taxonomy ) );
    $new_link = wp_make_link_relative( get_term_link( $new, $taxonomy ) );
    fwrite( $fp_out, '"^' .$old_link . '","' . $new_link . '","1","url","301","url"' . PHP_EOL );
  }

  fclose( $fp_in );
  fclose( $fp_out );

  echo 'Generated redirects file' . PHP_EOL;
}

function migrate_google_product_categories () {
  echo 'Migrating customtaxonomy...' . PHP_EOL;
  rename_old_terms( 'customtaxonomy' );
  stop_the_insanity();
  create_google_terms( 'customtaxonomy', 1 );
  stop_the_insanity();
  migrate_terms( 'custom', 'customtaxonomy' );
  stop_the_insanity();
  generate_redirect_csv( 'customtaxonomy' );
  stop_the_insanity();
  remove_old_terms( 'customtaxonomy' );
  stop_the_insanity();
  echo 'Migrating product taxonomy...' . PHP_EOL;
  rename_old_terms( 'producttaxonomy' );
  stop_the_insanity();
  create_google_terms( 'producttaxonomy' );
  stop_the_insanity();
  generate_redirect_csv( 'producttaxonomy' );
  stop_the_insanity();
  migrate_terms( 'product', 'producttaxonomy' );
  stop_the_insanity();
  remove_old_terms( 'producttaxonomy' );
}

function create_all_google_terms_for_customtaxonomy () {
  echo 'Migrating customtaxonomy...' . PHP_EOL;
  rename_old_terms( 'customtaxonomy' );
  stop_the_insanity();
  create_google_terms( 'customtaxonomy' );
  stop_the_insanity();
  migrate_terms( 'custom', 'customtaxonomy' );
  stop_the_insanity();
  remove_old_terms( 'customtaxonomy' );
}