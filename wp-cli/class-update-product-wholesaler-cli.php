<?php

class Update_Product_Wholesaler_Command {
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
      $updated_products = 0;
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
          $product_wholesaler_id = get_post_meta( $product_id, 'related_wholesaler', true );

          $product_author_id = get_post_field( 'post_author', $product_id );
          $product_author_wholesaler = get_user_wholesaler( $product_author_id );
          
          if ( !$product_wholesaler_id ) {
            // Product has no related wholesaler
          } elseif ( !$product_author_id ) {
            // Product has no author
          } elseif ( !isset($product_author_wholesaler->ID) ) {
            // Author of product has no wholesaler
            // Wholesaler was probably deleted by user
          } elseif ( $product_wholesaler_id != $product_author_wholesaler->ID ) {
            // Product's wholesaler does not match with prroduct author's wholesaler
            // update product post's meta
            if ( !$dry_run ) {
              update_post_meta( $product_id, 'related_wholesaler', $product_author_wholesaler->ID );
            }
            $updated_wholesalers[] = $product_author_wholesaler->ID;
            $updated_products++;
          }

        }

        // Free up memory.
        stop_the_insanity();

        $progress->tick( count($query->posts) );
        $paged++;

      } while ( count($query->posts) );

      $updated_wholesalers = array_unique($updated_wholesalers);

      $progress->finish();

      end_bulk_operation();

      if ( $dry_run ) {
        WP_CLI::success( sprintf( '%d products in %d wholesalers will be updated', $updated_products, count($updated_wholesalers) ) );
      } else {
        WP_CLI::success( sprintf( '%d products in %d wholesalers have been updated', $updated_products, count($updated_wholesalers) ) );
      }

    }
}

WP_CLI::add_command( 'update-product-wholesaler', 'Update_Product_Wholesaler_Command' );