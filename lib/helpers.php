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
 * Get special offers by wholesalers
 */
function get_special_offers_by_wholesalers( $wholesalers ): array
{
  $special_offers = [];
  foreach ( get_special_offers() as $special_offer ) {
    $related_wholesaler_id = get_field( 'related_wholesaler', $special_offer )->ID;
    if ( ! in_array( $related_wholesaler_id, $wholesalers ) ) continue;
    $special_offers[] = $special_offer;
  }
  return $special_offers;
}

/**
 * Get special offers by wholesaler term
 */
function get_special_offers_by_term( $term_id ): array
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
  
  $special_offer_with_term = get_special_offers_by_wholesalers( $wholesalers_with_term );
  
  return $special_offer_with_term;
}

/**
 * Get special offers by region
 */
function get_special_offers_by_region( $region_id ): array
{
  $wholesalers_with_special_offer = get_wholesalers_with_special_offer();

  $wholesalers_in_region = [];
  foreach ( $wholesalers_with_special_offer as $id ) {
    if ( ! get_field( 'region', $id ) || $region_id != get_field( 'region', $id )['value'] ) continue;
    $wholesalers_in_region[] = $id;
  }
  
  $special_offer_in_region = get_special_offers_by_wholesalers( $wholesalers_in_region );

  return $special_offer_in_region;
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
 * Get all special offers
 */
function get_special_offers(): array
{
  $wp_query = new WP_Query( [
    'post_type' => 'special_offer',
    'posts_per_page' => -1,
    'post_status' => 'publish',
  ] );
  return $wp_query->posts;
}

/**
 * Get all wholesalers related to a special offer
 */
function get_wholesalers_with_special_offer(): array
{
  $wholesalers_with_special_offer = [];
  foreach ( get_special_offers() as $special_offer ) {
    $wholesalers_with_special_offer[] = get_field( 'related_wholesaler', $special_offer->ID )->ID;
  }
  $wholesalers_with_special_offer = array_unique( $wholesalers_with_special_offer );

  return $wholesalers_with_special_offer;
}

/**
 * Get not empty wholesaler regions by country
 */
function get_used_regions_by_country( $only_with_spacial_offers = false ): array
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

  $is_region_used = function ( $region_id ) use ( &$only_with_spacial_offers ) {
    if ( $only_with_spacial_offers )
      $region_post_count = count( get_special_offers_by_region( $region_id ) );
    else
      $region_post_count = get_post_count_by_meta( 'region', $region_id, 'custom' );
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
 * Get all terms related to wholesalers with a special offer
 */
function get_terms_with_special_offer(): array
{
  $wholesalers_with_special_offer = get_wholesalers_with_special_offer();

  // Collect all terms related to wholesalers with a special offer
  $terms_with_special_offers = [];
  foreach ( $wholesalers_with_special_offer as $id ) {
    foreach ( get_the_terms( $id, 'customtaxonomy' ) as $term ) {
      $terms_with_special_offers[ $term->term_id ] = $term; // Rewrite current value and make array unique
    }
  }
  ksort( $terms_with_special_offers ); // Sort by key

  return $terms_with_special_offers;
}

/**
 * Separate thousands by non-break space
 */
function separate_thousands( $num ): string
{
  if ( ! is_numeric( $num ) ) return $num;
  return number_format( $num, 0 , ',', '&nbsp;' );
}
