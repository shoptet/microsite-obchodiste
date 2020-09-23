<?php

namespace Shoptet;

class DBXCli {

  public function synctable( $args, $assoc_args ) {

    if ( empty($args[0]) ) {
      \WP_CLI::error( 'Post type is required!' );
    }
    $post_type = $args[0];

    global $dbx;
    if ( ! in_array( $post_type, $dbx->get_registered_post_types() ) ) {
      \WP_CLI::error( 'Post type is not registered!' );
    }

    $dbx->get_registered_post_type($post_type)->get_store()->create_table();
  }

  public function syncalltables( $args, $assoc_args ) {
    global $dbx;
    foreach( $dbx->get_registered_post_types() as $post_type ) {
      $dbx->get_registered_post_type($post_type)->get_store()->create_table();
    }
  }

  public function migrate( $args, $assoc_args ) {

    start_bulk_operation();

    wp_cache_flush();

    $dry_run = false;
    if ( !empty($assoc_args['dry-run']) && 'true' == $assoc_args['dry-run'] ) {
      $dry_run = true;
    }

    $remove_original_meta = false;
    if ( !empty($assoc_args['remove-original']) && 'true' == $assoc_args['remove-original'] ) {
      $remove_original_meta = true;
    }
    
    if ( empty($args[0]) ) {
      \WP_CLI::error( 'Post type is required!' );
    }
    $post_type = $args[0];

    global $dbx;
    if ( ! in_array( $post_type, $dbx->get_registered_post_types() ) ) {
      \WP_CLI::error( 'Post type is not registered!' );
    }

    if ( empty($args[1]) ) {
      \WP_CLI::error( 'Meta key is required!' );
    }
    $meta_key = $args[1];

    $dbx_post_type = $dbx->get_registered_post_type($post_type);
    $extended_meta_keys = $dbx_post_type->get_extended_meta_keys($post_type);

    if ( ! $dbx_post_type->get_store()->table_exists() ) {
      \WP_CLI::error( 'Table not exists!' );
    }

    if ( !in_array($meta_key, $extended_meta_keys) ) {
      \WP_CLI::error( 'Meta key is not registered!' );
    }
    
    // Let the user know in what mode the command runs.
    if ( $dry_run ) {
      \WP_CLI::log( 'Running in dry-run mode.' );
    } else {
      \WP_CLI::log( 'We\'re doing it live!' );
    }

    $posts_per_page = 350;
    $paged = 1;
    $migrated_posts = 0;
    
    $args = [
      'post_type' => $post_type,
      'post_status' => 'any',
      'fields' => 'ids',
      'posts_per_page' => $posts_per_page,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ];

    $query = new \WP_Query( $args );

    $progress = \WP_CLI\Utils\make_progress_bar( 'Migrating posts', $query->found_posts );

    do {
      $args['paged'] = $paged;
      $query = new \WP_Query( $args );

      foreach ( $query->posts as $post_id ) {

        if ( $dbx_post_type->get_store()->row_exists($post_id) ) {
          if ( empty(get_post_meta($post_id, $meta_key)) ) {
            $meta_data = DBXUtility::get_original_meta_data($post_id);
            if ( array_key_exists( $meta_key, $meta_data )) {
              if ( ! $dry_run ) {
                update_post_meta( $post_id, $meta_key, $meta_data[$meta_key] );
              }
            }
            $migrated_posts++;
          } else {
            // Meta key for post exists
          }
        } else {
          \WP_CLI::log( sprintf( 'Row for post %d does not exists', $post_id ) );
        }

        if ( $remove_original_meta ) {
          if ( ! $dry_run ) {
            DBXUtility::delete_original_meta_data($post_id, [ $meta_key ]);
          } 
        }

      }

      // Free up memory.
      stop_the_insanity();

      $progress->tick( count($query->posts) );
      $paged++;

    } while ( count($query->posts) );

    $progress->finish();

    end_bulk_operation();

    if ( $dry_run ) {
      \WP_CLI::success( sprintf( '%d posts will be migrated', $migrated_posts) );
    } else {
      \WP_CLI::success( sprintf( '%d posts have been migrated', $migrated_posts) );
    }

  }

  public function migrateall( $args, $assoc_args ) {

    start_bulk_operation();

    wp_cache_flush();

    $dry_run = false;
    if ( !empty($assoc_args['dry-run']) && 'true' == $assoc_args['dry-run'] ) {
      $dry_run = true;
    }
    
    if ( empty($args[0]) ) {
      \WP_CLI::error( 'Post type is required!' );
    }
    $post_type = $args[0];

    global $dbx;
    if ( ! in_array( $post_type, $dbx->get_registered_post_types() ) ) {
      \WP_CLI::error( 'Post type is not registered!' );
    }

    $dbx_post_type = $dbx->get_registered_post_type($post_type);

    if ( ! $dbx_post_type->get_store()->table_exists() ) {
      \WP_CLI::error( 'Table not exists!' );
    }

    // Let the user know in what mode the command runs.
    if ( $dry_run ) {
      \WP_CLI::log( 'Running in dry-run mode.' );
    } else {
      \WP_CLI::log( 'We\'re doing it live!' );
    }

    $posts_per_page = 350;
    $paged = 1;
    $migrated_posts = 0;
    
    $args = [
      'post_type' => $post_type,
      'post_status' => 'any',
      'fields' => 'ids',
      'posts_per_page' => $posts_per_page,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ];

    $query = new \WP_Query( $args );

    $progress = \WP_CLI\Utils\make_progress_bar( 'Migrating posts', $query->found_posts );

    do {
      $args['paged'] = $paged;
      $query = new \WP_Query( $args );

      foreach ( $query->posts as $post_id ) {
        if ( ! $dbx_post_type->get_store()->row_exists($post_id) ) {
          if ( ! $dry_run ) {
            $dbx_post_type->get_store()->maybe_insert_row($post_id);
          }
          $migrated_posts++;
        }
      }

      // Free up memory.
      stop_the_insanity();

      $progress->tick( count($query->posts) );
      $paged++;

    } while ( count($query->posts) );

    $progress->finish();

    end_bulk_operation();

    if ( $dry_run ) {
      \WP_CLI::success( sprintf( '%d posts will be migrated', $migrated_posts) );
    } else {
      \WP_CLI::success( sprintf( '%d posts have been migrated', $migrated_posts) );
    }

  }

  public function removeoriginal( $args, $assoc_args ) {

    start_bulk_operation();

    wp_cache_flush();

    $dry_run = false;
    if ( !empty($assoc_args['dry-run']) && 'true' == $assoc_args['dry-run'] ) {
      $dry_run = true;
    }

    $force = false;
    if ( !empty($assoc_args['force']) && 'true' == $assoc_args['force'] ) {
      $force = true;
    }
    
    if ( empty($args[0]) ) {
      \WP_CLI::error( 'Post type is required!' );
    }
    $post_type = $args[0];

    global $dbx;
    if ( ! in_array( $post_type, $dbx->get_registered_post_types() ) ) {
      \WP_CLI::error( 'Post type is not registered!' );
    }

    // Let the user know in what mode the command runs.
    if ( $dry_run ) {
      \WP_CLI::log( 'Running in dry-run mode.' );
    } else {
      \WP_CLI::log( 'We\'re doing it live!' );
    }

    if ( $force ) {
      \WP_CLI::log( 'Force mode!' );
    }

    $dbx_post_type = $dbx->get_registered_post_type($post_type);
    $extended_meta_keys = $dbx_post_type->get_extended_meta_keys($post_type);
    $static_meta_data = $dbx_post_type->get_static_meta_data($post_type);

    // Normalize static meta keys
    $static_meta_keys = [];
    foreach ( $static_meta_data as $key => $val ) {
      $static_meta_keys[] = $key;
    }

    $meta_keys_to_remove = array_merge( $extended_meta_keys, $static_meta_keys );

    $posts_per_page = 350;
    $paged = 1;
    $cleaned_posts = 0;
    
    $args = [
      'post_type' => $post_type,
      'post_status' => 'any',
      'fields' => 'ids',
      'posts_per_page' => $posts_per_page,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ];

    $query = new \WP_Query( $args );

    $progress = \WP_CLI\Utils\make_progress_bar( 'Removing post meta', $query->found_posts );

    do {
      $args['paged'] = $paged;
      $query = new \WP_Query( $args );

      foreach ( $query->posts as $post_id ) {
        if ( ! $dry_run ) {
          DBXUtility::delete_original_meta_data( $post_id, $meta_keys_to_remove, $force );
        }
        $cleaned_posts++;
      }

      // Free up memory.
      stop_the_insanity();

      $progress->tick( count($query->posts) );
      $paged++;

    } while ( count($query->posts) );

    $progress->finish();

    end_bulk_operation();

    if ( $dry_run ) {
      \WP_CLI::success( sprintf( '%d posts will be cleaned', $cleaned_posts) );
    } else {
      \WP_CLI::success( sprintf( '%d posts have been cleaned', $cleaned_posts) );
    }

  }
}

\WP_CLI::add_command( 'dbx', 'Shoptet\DBXCli' );