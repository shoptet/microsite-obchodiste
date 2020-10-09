<?php

/**
 * CLI command that clean posts.
 */

class Clean_Posts_Command {
    public function empty( $args, $assoc_args ) {


      if ( empty($args[0]) ) {
        \WP_CLI::error( 'Post type is required!' );
      }
      $post_type = $args[0];

      // If --dry-run is not set, then it will default to true.
      // Must set --dry-run explicitly to false to run this command.
      if ( isset( $assoc_args['dry-run'] ) ) {
        /*
          * passing `--dry-run=false` to the command leads to the `false` value being
          * set to string `'false'`, but casting `'false'` to bool produces `true`.
          * Thus the special handling.
          */
        if ( 'false' === $assoc_args['dry-run'] ) {
          $dry_run = false;
        } else {
          $dry_run = (bool) $assoc_args['dry-run'];
        }
      } else {
        $dry_run = true;
      }

      $force_delete = ( isset( $assoc_args['force-delete'] ) && 'true' === $assoc_args['force-delete'] );

      // Let the user know in what mode the command runs.
      if ( $dry_run ) {
        WP_CLI::log( 'Running in dry-run mode.' );
      } else {
        WP_CLI::log( 'We\'re doing it live!' );
      }

      $posts_per_page = 350;
      $paged = 1;
      $count = 0;
      $deleted_posts = 0;

      $args = [
        'post_type' => $post_type,
        'post_status' => 'any',
        'posts_per_page' => $posts_per_page,
      ];

      $query = new WP_Query( $args );

      $progress = \WP_CLI\Utils\make_progress_bar( 'Cleaning posts', $query->found_posts );

      do {
        $args['paged'] = $paged;
        $query = new WP_Query( $args );

        foreach ( $query->posts as $post ) {
          if ( $post->post_title != '' ) continue;
          if ( !$dry_run ) {
            wp_delete_post( $post->ID, $force_delete );
          }
          $deleted_posts++;
        }

        // Free up memory.
        stop_the_insanity();

        $progress->tick( count($query->posts) );
        $paged++;

      } while ( count($query->posts) );

      $progress->finish();

      end_bulk_operation();

      if ( $dry_run ) {
        WP_CLI::success( sprintf( '%d posts will be deleted', $deleted_posts ) );
      } else {
        WP_CLI::success( sprintf( '%d posts have been deleted', $deleted_posts ) );
      }

    }
}

WP_CLI::add_command( 'clean-posts', 'Clean_Posts_Command' );