<?php

/**
 * CLI command that clean action scheduler logs table.
 */

class Clean_AS_Logs_Command {
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
      $deleted = 0;

      $logs_table_name = $wpdb->prefix . 'actionscheduler_logs';
      $actions_table_name = $wpdb->prefix . 'actionscheduler_actions';

      $all_logs = $wpdb->get_results( "SELECT log_id, action_id FROM $logs_table_name", ARRAY_A );

      $progress = \WP_CLI\Utils\make_progress_bar(
        sprintf( 'Processing %d logs', count($all_logs) ),
        count($all_logs)
      );

      foreach( $all_logs as $log ) {
        $action_id = $log['action_id'];
        $log_id = $log['log_id'];
        $action_row = $wpdb->get_var( $wpdb->prepare( "SELECT action_id FROM $actions_table_name WHERE action_id = %d", $action_id ) );
        if ( null == $action_row ) {
          if ( ! $dry_run ) {
            $wpdb->delete( $logs_table_name, [ 'log_id' => intval($log_id) ] );
          }
          $deleted++;
        }
        $progress->tick();
      }

      if ( $dry_run ) {
        WP_CLI::success( sprintf( '%d AS logs will be deleted', $deleted ) );
      } else {
        WP_CLI::success( sprintf( '%d AS logs have been deleted', $deleted ) );
      }

    }
}

WP_CLI::add_command( 'clean-as-logs', 'Clean_AS_Logs_Command' );