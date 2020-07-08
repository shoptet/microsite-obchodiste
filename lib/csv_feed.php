<?php

function array2csv( &$array ) {
  if ( count($array) == 0 ) {
    return null;
  }
  ob_start();
  $df = fopen( 'php://output', 'w' );
  fputcsv( $df, array_keys( reset( $array ) ) );
  foreach ( $array as $row ) {
    fputcsv( $df, $row );
  }
  fclose( $df );
  return ob_get_clean();
}

function get_post_term_breadcrumbs( $post_id, $taxonomy ) {
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

function get_posts_csv_feed( $post_type, $taxonomy ) {
  $wp_query = new WP_Query( [
    'post_type' => $post_type,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
  ] );

  $result = [];
  foreach( $wp_query->posts as $post_id ) {
    $result[] = [
      'URL' => get_permalink( $post_id ),
      'ID' => get_global_id( $post_id, 'post' ),
      'Category' => get_post_term_breadcrumbs( $post_id, $taxonomy ),
    ];
  }

  return array2csv( $result );
}

function get_terms_csv_feed( $taxonomy ) {
  $wp_term_query = new WP_Term_Query( [
    'taxonomy' => $taxonomy,
  ] );

  $result = [];
  foreach( $wp_term_query->terms as $term ) {
    if ( $term->count < 5 ) continue;
    $result[] = [
      'URL' => get_term_link( $term ),
      'ID' => get_global_id( $term->term_id, 'term' ),
    ];
  }

  return array2csv( $result );
}

function get_cached_csv_feed_file_name( $file_name_postfix ) {
  $temp_dir = __DIR__ . '/../../tmp';
  return sprintf( '%s/feed-%s.csv', $temp_dir, $file_name_postfix );
}

function cache_all_csv_feeds() {
  $temp_dir = get_temp_dir();
  $taxonomies_by_post_type = [
    'product' => 'producttaxonomy',
    'custom' => 'customtaxonomy',
  ];

  foreach( $taxonomies_by_post_type as $post_type => $taxonomy ) {

    $csv_data = get_posts_csv_feed( $post_type, $taxonomy );
    $file_name = get_cached_csv_feed_file_name( $post_type );
    file_put_contents( $file_name , $csv_data );

    $csv_data = get_terms_csv_feed( $taxonomy );
    $file_name = get_cached_csv_feed_file_name( $taxonomy );
    file_put_contents( $file_name , $csv_data );
  }
}

add_action( 'wp' , function () {
	if ( ! isset( $_GET['data-feed'] ) || '' === $_GET['date-feed'] ) return;
  $data_feed = $_GET['data-feed'];

  switch ( $data_feed ) {
    case 'products':
    $file_name = get_cached_csv_feed_file_name( 'product' );
    break;
    case 'wholesalers':
    $file_name = get_cached_csv_feed_file_name( 'custom' );
    break;
    case 'product-categories':
    $file_name = get_cached_csv_feed_file_name( 'producttaxonomy' );
    break;
    case 'wholesaler-categories':
    $file_name = get_cached_csv_feed_file_name( 'customtaxonomy' );
    break;
    default:
    // no match, ignore
    return;
  }

  if ( ! file_exists( $file_name ) ) {
    do_action( 'cache_all_csv_feeds');
  }

  $csv = file_get_contents( $file_name );

  header( 'Content-Type: text/csv' );
  header( 'Content-Disposition: attachment; filename=feed-' . $data_feed . '.csv' );
  header( 'Pragma: no-cache' );
  header( 'Expires: 0' );

  echo $csv;

  die();
} );

// Set caching cron
if ( ! wp_next_scheduled( 'cache_all_csv_feeds' ) ) {
  // wp_schedule_event( time(), 'hourly', 'cache_all_csv_feeds' );
}
add_action( 'cache_all_csv_feeds', 'cache_all_csv_feeds' );