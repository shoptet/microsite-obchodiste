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
function get_post_count_by_meta( $meta_key, $meta_value, $post_type, $compare = '=' ): int
{
  $args = [
    'post_type' => $post_type,
    'numberposts' => -1,
    'post_status' => 'publish',
  ];
  $args[ 'meta_query' ][] = [
    'key' => $meta_key,
    'value' => $meta_value,
    'compare' => $compare,
  ];
  $posts = get_posts( $args );
  $count = count( $posts );
  return $count;
}

/**
 * Get posts by related wholesalers
 */
function get_posts_by_related_wholesalers( $post_type, $wholesalers ): array
{
  $posts_by_wholesalers = [];
  foreach ( get_all_posts( $post_type ) as $special_offer ) {
    $related_wholesaler_id = get_field( 'related_wholesaler', $special_offer )->ID;
    if ( ! in_array( $related_wholesaler_id, $wholesalers ) ) continue;
    $posts_by_wholesalers[] = $special_offer;
  }
  return $posts_by_wholesalers;
}

/**
 * Get posts by related wholesaler term
 */
function get_posts_by_related_wholesaler_term( $post_type, $term_id ): array
{
  $wp_query = new WP_Query( [
    'post_type' => 'custom',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
    'tax_query' => [
      [
        'taxonomy' => 'customtaxonomy',
        'field' => 'term_id',
        'terms' => $term_id,
      ]
    ]
  ] );
  $wholesalers_with_term = $wp_query->posts;
  
  $posts_by_term = get_posts_by_related_wholesalers( $post_type, $wholesalers_with_term );
  
  return $posts_by_term;
}

/**
 * Get posts by region
 */
function get_posts_by_region( $post_type, $region_id ): array
{
  $wholesalers_with_post = get_wholesalers_with_post( $post_type );

  $wholesalers_in_region = [];
  foreach ( $wholesalers_with_post as $id ) {
    if ( ! get_field( 'region', $id ) || $region_id != get_field( 'region', $id )['value'] ) continue;
    $wholesalers_in_region[] = $id;
  }
  
  $post_in_region = get_posts_by_related_wholesalers( $post_type, $wholesalers_in_region );

  return $post_in_region;
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
 * Get all posts
 */
function get_all_posts( $post_type ): array
{
  $wp_query = new WP_Query( [
    'post_type' => $post_type,
    'posts_per_page' => -1,
    'post_status' => 'publish',
  ] );
  return $wp_query->posts;
}

/**
 * Get all wholesalers related to a post
 */
function get_wholesalers_with_post( $post_type ): array
{
  $wholesalers_with_post = [];
  foreach ( get_all_posts( $post_type ) as $post ) {
    $wholesalers_with_post[] = get_field( 'related_wholesaler', $post->ID )->ID;
  }
  $wholesalers_with_post = array_unique( $wholesalers_with_post );

  return $wholesalers_with_post;
}

/**
 * Get all related wholesalers
 */
function get_related_wholesalers( $post_type ): array
{
  $related_wholesalers = [];
  foreach ( get_all_posts( $post_type ) as $post ) {
    $related_wholesalers[] = get_field( 'related_wholesaler', $post->ID )->ID;
  }
  $related_wholesalers = array_unique( $related_wholesalers );

  return $related_wholesalers;
}

/**
 * Get not empty wholesaler regions by country
 */
function get_used_regions_by_country( $post_type ): array
{
  $countries = [
    'cz' => [
      'name' => __( 'Česko', 'shp-obchodiste' ),
      'field' => 'field_5b5ed2ca0a22d',
    ],
    'sk' => [
      'name' => __( 'Slovensko', 'shp-obchodiste' ),
      'field' => 'field_5bbdc19430685',
    ],
  ];
  $regions_by_country = [];

  $is_region_used = function ( $region_id ) use ( &$post_type ) {
    if ( $post_type === 'custom' )
      $region_post_count = get_post_count_by_meta( 'region', $region_id, $post_type );
    else
      $region_post_count = count( get_posts_by_region( $post_type, $region_id ) );
    return ( $region_post_count > 0 );
  };

  foreach ( $countries as $country_code => $country ) {
    $regions_in_country = get_field_object( $country[ 'field' ] )[ 'choices' ];
    $used_regions = [];

    foreach ( $regions_in_country as $region_id => $region_name ) {
      if ( $is_region_used( $region_id ) ) $used_regions[] = [
        'id' => $region_id,
        'name' => $region_name,
      ];
    }

    if ( empty( $used_regions ) ) continue;
    
    $regions_by_country[ $country_code ] = [
      'name' => $country[ 'name' ],
      'used_regions' => $used_regions,
    ];
  }
  
  return $regions_by_country;
}

/**
 * Get all services
 */
function get_all_services(): array
{
  return get_field_object( 'field_5b5ed686ddd58' )[ 'choices' ];
}

/**
 * Get all wholesaler terms related to a post type
 */
function get_wholesaler_terms_related_to_post_type( $post_type ): array
{
  $related_wholesalers = get_related_wholesalers( $post_type );

  // Collect all terms related to a post type
  $related_terms = [];
  foreach ( $related_wholesalers as $id ) {
    foreach ( get_the_terms( $id, 'customtaxonomy' ) as $term ) {
       $related_terms[ $term->term_id ] = $term; // Rewrite current value and make array unique
    }
  }
  ksort(  $related_terms ); // Sort by key

  return  $related_terms;
}

/**
 * Separate thousands by non-break space
 */
function separate_thousands( $num ): string
{
  if ( ! is_numeric( $num ) ) return $num;
  return number_format( $num, 0 , ',', '&nbsp;' );
}

/**
 * Is number of posts exceeded for current user
 */
function is_number_of_posts_exceeded( $post_type ): bool
{
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  $wp_query = new WP_Query( [
    'post_type' => $post_type,
    'posts_per_page' => -1,
    'author' => $current_user->ID,
  ] );

  $options = get_fields( 'options' );
  $post_type_limit = $options[ $post_type . '_limit' ];
  
  return ( $wp_query->found_posts >= $post_type_limit );
}

/**
 * Get category link for post type
 */
function get_archive_category_link( $post_type, $category ): string
{
  $archive_link = get_post_type_archive_link( $post_type );
  $category_id = $category->term_id;
  return $archive_link . '?category[]=' . $category_id;
}

function get_user_wholesaler( $user ) {
  $wp_query = new WP_Query( [
    'post_type' => 'custom',
    'posts_per_page' => 1,
    'author' => $user->ID,
  ] );
  return $wp_query->post;
}

function get_post_type_in_archive_or_taxonomy () {
  global $wp_query;
  $post_type = $wp_query->get( 'post_type' );
  if ( is_tax() ) {
    $taxonomy = get_queried_object();
    if ( $taxonomy->taxonomy === 'customtaxonomy' )
      $post_type = 'custom';
    elseif ( $taxonomy->taxonomy === 'producttaxonomy' )
      $post_type = 'product';
  }
  return $post_type;
}