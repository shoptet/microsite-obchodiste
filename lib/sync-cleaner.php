<?php

class SyncCleaner {

  static function init() {
    self::scheduleEvents();
    add_action( 'clean_sync_posts', [ get_called_class(), 'clean' ] );
  }

  static function scheduleEvents() {
    if ( ! wp_next_scheduled( 'clean_sync_posts' ) ) {
      wp_schedule_event( time(), 'hourly', 'clean_sync_posts' );
    }
  }

  static function clean() {
    $post_ids = self::getSyncPostsToRemove();
    foreach( $post_ids as $post_id ) {
      wp_delete_post( $post_id, true );
    }
  }

  static function getSyncPostsToRemove() {
    $query = new WP_Query( [
      'post_type' => 'sync',
      'post_status' => [ 'done', 'error' ],
      'fields' => 'ids',
      'posts_per_page' => 500,
      'date_query' => [
        'before' => '-7 days',
      ],
    ] );
    return $query->posts;
  }

}