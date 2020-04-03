<?php

/**
 * CLI command that updates all attachments' post author to their parent post's author.
 */

class Update_Attachment_Author_Command {
    public function __invoke( $args, $assoc_args ) {

      start_bulk_operation();

      // Prevent infinite loop
      remove_action( 'edit_attachment', 'Shoptet\Attachment::handle_author' );

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
      $updated_attachs = 0;

      $args = [
        'post_type' => 'attachment',
        'post_status' => 'any',
        'fields' => 'ids',
        'posts_per_page' => $posts_per_page,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
      ];

      $query = new WP_Query( $args );

      $progress = \WP_CLI\Utils\make_progress_bar(
        sprintf( 'Processing %d attachments', $query->found_posts ),
        $query->found_posts
      );

      $i = $j = 0;

      do {
        $args['paged'] = $paged;
        $query = new WP_Query( $args );

        foreach ( $query->posts as $attach_id ) {
          $attach = get_post($attach_id);

          $parent = get_post($attach->post_parent);
          if ( !$parent ) {
            // Parent post not exists
            continue;
          }

          if ( $parent->post_author && $attach->post_author != $parent->post_author ) {
            if ( !$dry_run ) {
              wp_update_post([
                'ID' => $attach_id,
                'post_author' => $parent->post_author,
              ]);
            }
            $updated_attachs++;
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
        WP_CLI::success( sprintf( '%d attachments will be updated', $updated_attachs ) );
      } else {
        WP_CLI::success( sprintf( '%d attachments has been updated', $updated_attachs ) );
      }

    }
}

WP_CLI::add_command( 'update-attachment-author', 'Update_Attachment_Author_Command' );