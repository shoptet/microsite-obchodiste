<?php

/**
 * CLI command that clean post meta table.
 */

class Clean_Postmeta_Command {
    public function __invoke( $args, $assoc_args ) {

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

      global $wpdb;

      $orphan_post_meta_sql = "
        FROM $wpdb->postmeta pm
        WHERE NOT EXISTS (
          SELECT 1
          FROM $wpdb->posts p
          WHERE p.ID = pm.post_id
        )
      ";

      if ( $dry_run ) {
        $orphan_post_meta_sql_statement = 'SELECT *';
      } else {
        $orphan_post_meta_sql_statement = 'DELETE pm';
      }
      
      $orphan_post_meta_count = $wpdb->query( $orphan_post_meta_sql_statement . $orphan_post_meta_sql, $orphan_post_meta_sql_statement );
      
      if ( $dry_run ) {
        WP_CLI::success( sprintf( '%d orphan post meta will be deleted', $orphan_post_meta_count ) );
      } else {
        WP_CLI::success( sprintf( '%d orphan post meta has been deleted', $orphan_post_meta_count ) );
      }

    }
}

WP_CLI::add_command( 'clean-postmeta', 'Clean_Postmeta_Command' );