<?php

/**
 * CLI command that cache all CSV feed.
 */

class Cache_CSV_Feed_Command {
  public function __invoke( $args, $assoc_args ) {
    Shoptet\ExporterGoogle::cache_all_csv_feeds();
  }
}

WP_CLI::add_command( 'cache-csv-feed', 'Cache_CSV_Feed_Command' );