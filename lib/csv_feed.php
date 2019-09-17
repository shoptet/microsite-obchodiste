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
      'Custom label' => get_post_term_breadcrumbs( $post_id, $taxonomy ),
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
    ];
  }

  return array2csv( $result );
}

// Check for external company token and authenticate
add_action( 'wp' , function () {
	if ( ! isset( $_GET['data-feed'] ) || '' === $_GET['date-feed'] ) return;
  $data_feed = $_GET['data-feed'];

  switch ( $data_feed ) {
    case 'products':
    $csv = get_posts_csv_feed( 'product', 'producttaxonomy' );
    break;
    case 'wholesalers':
    $csv = get_posts_csv_feed( 'custom', 'customtaxonomy' );
    break;
    case 'product-categories':
    $csv = get_terms_csv_feed( 'producttaxonomy' );
    break;
    case 'wholesaler-categories':
    $csv = get_terms_csv_feed( 'customtaxonomy' );
    break;
    default:
    return;
  }

  header( 'Content-Type: application/csv' );
  header( 'Content-Disposition: attachment; filename=feed-' . $data_feed . '.csv' );
  echo $csv;

  die();
} );