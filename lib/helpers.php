<?php

/**
 * Check whether a post is new
 */
function is_post_new(): bool
{
  $is_new_interval = 15; // in days
  $today = new DateTime();
  $post_date = new DateTime( get_the_date( 'Y-m-d' ) );
  $interval = $today->diff( $post_date );
  return $interval->days <= $is_new_interval;
}

/**
 * Remove protocol and last slash from url
 */
function display_url( $url, $display_www = true ): string
{
  $url = preg_replace( '#^https?://|^//|/$#', '', $url ); // Remove protocol and last slash
  if ( ! $display_www ) {
    $url = preg_replace( '#^www\.#', '', $url ); // Remove www.
  }
  return $url;
}

/**
 * Get post count by meta key and value
 */
function get_post_count_by_meta( $meta_key, $meta_value, $post_type ): int
{
  $args = [
    'post_type' => $post_type,
    'numberposts' => -1,
    'post_status' => 'publish',
  ];
  $args[ 'meta_query' ][] = [
    'key' => $meta_key,
    'value' => $meta_value,
  ];
  $posts = get_posts( $args );
  $count = count( $posts );
  return $count;
}


/**
 * Get terms by id
 */
function get_terms_by_id( $taxonomy ): array
{
  $terms = get_terms( $taxonomy );
  $terms_by_id = array_reduce( $terms, function( $result, $term ) {
    $result[ $term->term_id ] = $term->slug;
    return $result;
  }, [] );
  return $terms_by_id;
}

/**
 * Get truncated string
 */
function truncate( $string, $limit, $separator = '...' ): string
{
  if ( strlen( $string ) <= $limit ) return $string;
  $newlimit = $limit - strlen( $separator );
  $s = substr( $string, 0, $newlimit + 1 );
  return substr( $s, 0, strrpos( $s, ' ' ) ) . $separator;
}

/**
 * New line to paragraph
 */
function nl2p( $text ): string
{
  return '<p>' . str_replace( [ "\r\n\r\n", "\n\n" ], '</p><p>', $text ) . '</p>';
}

/**
 * Get not empty wholesaler regions
 */
function get_used_regions(): array
{
  $used_regions = [];
  foreach ( get_field_object( 'field_5b5ed2ca0a22d' )[ 'choices' ] as $region_id => $region_name ) {
    $region_post_count = get_post_count_by_meta( 'region', $region_id, 'custom' );
    if ( $region_post_count > 0 ) $used_regions[] = [
      'id' => $region_id,
      'name' => $region_name,
    ];
  }
  return $used_regions;
}
