<?php

/**
 * CLI command that updates all products' thumbanil to their ACF thumbnail field value.
 */

class Update_Product_Thumbnails_Command {
    public function __invoke( $args, $assoc_args ) {

      start_bulk_operation();

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

      // Let the user know in what mode the command runs.
      if ( $dry_run ) {
        WP_CLI::log( 'Running in dry-run mode.' );
      } else {
        WP_CLI::log( 'We\'re doing it live!' );
      }

      $posts_per_page = 350;
      $paged = 1;
      $count = 0;
      $updated_wholesalers = [];

      $args = [
        'post_type' => 'product',
        'post_status' => 'any',
        'fields' => 'ids',
        'posts_per_page' => $posts_per_page,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
      ];

      $query = new WP_Query( $args );

      $progress = \WP_CLI\Utils\make_progress_bar( 'Processing products', $query->found_posts );

      do {
        $args['paged'] = $paged;
        $query = new WP_Query( $args );

        foreach ( $query->posts as $product_id ) {
          if ( !$dry_run ) {
            $_thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
            $thumbnail = get_post_meta( $product_id, 'thumbnail', true );
            delete_post_meta( $product_id, '_thumbnail_id' );
            if ( $thumbnail ) {
              add_post_meta( $product_id, '_thumbnail_id', $thumbnail );
            }
          }
          $count++;
        }

        // Free up memory.
        stop_the_insanity();

        $progress->tick( count($query->posts) );
        $paged++;

      } while ( count($query->posts) );

      $progress->finish();

      end_bulk_operation();

      if ( $dry_run ) {
        WP_CLI::success( sprintf( '%d products will be updated', $count ) );
      } else {
        WP_CLI::success( sprintf( '%d products have been updated', $count ) );
      }

    }
}

WP_CLI::add_command( 'update-product-thumbnails', 'Update_Product_Thumbnails_Command' );