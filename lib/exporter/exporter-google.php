<?php

namespace Shoptet;

class ExporterGoogle {

  const ACTION_HOOK = 'exporter_google/cache_csv';
  const RECURRENCE = 'daily';

  static function init() {

    add_action( 'wp' , [ get_called_class(), 'handle_data_feed' ] );
    add_action( self::ACTION_HOOK, [ get_called_class(), 'cache_all_csv_feeds' ] );

    if ( ! wp_next_scheduled( self::ACTION_HOOK ) ) {
      wp_schedule_event( time(), self::RECURRENCE, self::ACTION_HOOK );
    }
  }
  
  static function get_post_term_breadcrumbs( $post_id, $taxonomy ) {
    $terms = wp_get_post_terms( $post_id, $taxonomy );
  
    $result = '';
    
    if ( ! empty( $terms ) ) {
      $term = $terms[0];
      $args = [
        'link' => false,
        'separator' => ' » ',
        'inclusive' => false,
      ];
      $parents = get_term_parents_list( $term->term_id, $taxonomy, $args );
      $result = $parents . $term->name;
    }
  
    return $result;
  }
  
  static function cache_posts_csv_feed( $post_type, $taxonomy ) {

    $file_name = self::get_cached_csv_feed_file_name( $post_type );
    $fp = fopen( $file_name, 'w' );

    $header = [
      'URL',
      'ID',
      'Category',
    ];
    fputcsv( $fp, $header );

    $posts_per_page = 350;
    $paged = 1;

    $args = [
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $posts_per_page,
      'fields' => 'ids',
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ];
    
    $wp_query;
    $result = [];

    do {
      $args['paged'] = $paged;
      $wp_query = new \WP_Query( $args );
      foreach( $wp_query->posts as $post_id ) {
        $row = [
          get_permalink( $post_id ),
          get_global_id( $post_id, 'post' ),
          self::get_post_term_breadcrumbs( $post_id, $taxonomy ),
        ];
        fputcsv( $fp, $row );
      }
      stop_the_insanity();
      $paged++;
    } while ( count($wp_query->posts) );

    fclose( $fp );
  }
  
  static function cache_terms_csv_feed( $taxonomy ) {

    $file_name = self::get_cached_csv_feed_file_name( $taxonomy );
    $fp = fopen( $file_name, 'w' );

    $header = [
      'URL',
      'ID',
    ];
    fputcsv( $fp, $header );

    $wp_term_query = new \WP_Term_Query( [
      'taxonomy' => $taxonomy,
      'update_term_meta_cache' => false,
    ] );
  
    $result = [];
    foreach( $wp_term_query->terms as $term ) {
      if ( $term->count < 5 ) continue;
      $row = [
        get_term_link( $term ),
        get_global_id( $term->term_id, 'term' ),
      ];
      fputcsv( $fp, $row );
    }

    fclose( $fp );
  }
  
  static function get_cached_csv_feed_file_name( $file_name_postfix ) {
    $temp_dir = __DIR__ . '/../../../tmp';
    return sprintf( '%s/feed-%s.csv', $temp_dir, $file_name_postfix );
  }
  
  static function cache_all_csv_feeds() {
    $taxonomies_by_post_type = [
      'product' => 'producttaxonomy',
      'custom' => 'customtaxonomy',
    ];
  
    foreach( $taxonomies_by_post_type as $post_type => $taxonomy ) {
      self::cache_posts_csv_feed( $post_type, $taxonomy );
      self::cache_terms_csv_feed( $taxonomy );
    }
  }
  
  static function handle_data_feed () {
    if ( empty( $_GET['data-feed'] ) ) return;
    $data_feed = $_GET['data-feed'];
  
    switch ( $data_feed ) {
      case 'products':
      $file_name = self::get_cached_csv_feed_file_name( 'product' );
      break;
      case 'wholesalers':
      $file_name = self::get_cached_csv_feed_file_name( 'custom' );
      break;
      case 'product-categories':
      $file_name = self::get_cached_csv_feed_file_name( 'producttaxonomy' );
      break;
      case 'wholesaler-categories':
      $file_name = self::get_cached_csv_feed_file_name( 'customtaxonomy' );
      break;
      default:
      // no match, ignore
      return;
    }
  
    if ( ! file_exists( $file_name ) ) {
      do_action( self::ACTION_HOOK );
    }
  
    $csv = file_get_contents( $file_name );
  
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename=feed-' . $data_feed . '.csv' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );
  
    echo $csv;
  
    die();
  }

}

ExporterGoogle::init();