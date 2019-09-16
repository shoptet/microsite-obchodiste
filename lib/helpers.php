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

/**
 * Is special offer limit exceeded for current user
 */
function is_special_offer_limit_exceeded(): bool
{
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  $wp_query = new WP_Query( [
    'post_type' => 'special_offer',
    'posts_per_page' => -1,
    'author' => $current_user->ID,
  ] );

  $options = get_fields( 'options' );
  $special_offer_limit = $options[ 'special_offer_limit' ];
  
  return ( $wp_query->found_posts >= $special_offer_limit );
}

function hex2RGB( $hex ): array {
  list( $r, $g, $b ) = sscanf( $hex, '#%02x%02x%02x' );
  return [ 'r' => $r, 'g' => $g, 'b' => $b ];
}

function get_placeholder_logo_dir( $indice = 'basedir' ): string
{
  $upload_dir = wp_upload_dir();
  return $upload_dir[ $indice ] . '/wholesaler-logos';
}

function generate_placeholder_logo( $post_id ): void
{
  $image_width = 500;
  $image_height = $image_width;
  $color_variants = [
    [ '#ad0003', '#ff613d' ],
    [ '#38a85e', '#95d685' ],
    [ '#130806', '#f6efe4' ],
    [ '#b35900', '#ffc466' ],
    [ '#8ccdd9', '#539dc6' ],
    [ '#97b7c3', '#7c8af3' ],
    [ '#a6a6a6', '#474747' ],
    [ '#c6adff', '#9233ff' ],
  ];

  // Generate color variant form post id
  $length = count( $color_variants ); // 8
  $base_modulo = $post_id % ( $length * 2); // 0 - 15

  $color_variant = $color_variants[ $base_modulo % $length ]; // 0 - 7
  $color_variant_direction = intdiv( $base_modulo, $length ); // 0 - 1

  if ( $color_variant_direction == 1 ) {
    // switch bg a text colors
    array_push( $color_variant, array_shift( $color_variant ) );
  }

  // Generate initials
  $title = get_the_title( $post_id );
  $title = trim( $title );
  $title_words = explode( ' ', $title );
  $initials = '';
  $i = 0;
  $counter = 0;
  $excluded_words = [ 's.r.o.', 's. r. o.', 'a.s.', 'a. s.', 'spol.', 'a', 'mgr.', 'ing.', 'bc.' ];
  while( $i < count( $title_words ) && $counter < 2 ) {
    $word = strtolower( $title_words[$i++] );
    $first_letter = mb_substr($word, 0, 1);
    if (
      in_array( $word, $excluded_words ) || // Skip excluded words
      '&' == $first_letter // Skip html entities
    ) continue; 
    $initials .= $first_letter;
    $counter++;
  }
  $initials = strtoupper( $initials );

  $bg_rgb = hex2RGB( $color_variant[0] );
  $text_rgb = hex2RGB( $color_variant[1] );

  $im = imagecreate($image_width, $image_height);

  // White background and blue text
  $bg_color = imagecolorallocate($im, $bg_rgb['r'], $bg_rgb['g'], $bg_rgb['b']);
  $text_color = imagecolorallocate($im, $text_rgb['r'], $text_rgb['g'], $text_rgb['b']);
  $font_size = 150;
  $font = __DIR__ . '/misc/Lato-Black.ttf';
  $save_dir = get_placeholder_logo_dir();
  if ( ! file_exists($save_dir) ) {
    mkdir( $save_dir, 0755, true );
  }
  $save_file_path = sprintf( '%s/%s.png', $save_dir, $post_id );

  // Get Bounding Box Size
  $text_box = imagettfbbox( $font_size, 0, $font, $initials );

  // Get your Text Width and Height
  $text_width = $text_box[2] - $text_box[0];
  $text_height = $text_box[7] - $text_box[1];

  // Calculate coordinates of the text
  $x = ($image_width/2) - ($text_width/2);
  $y = ($image_height/2) - ($text_height/2);

  imagefill( $im, 0, 0, $bg_color );
  imagettftext( $im, $font_size, 0, $x, $y, $text_color, $font, $initials );

  imagepng( $im, $save_file_path );

  imagedestroy( $im );
}

function generate_all_placeholder_logos(): int
{
  // Remove all placeholder logos
  $placeholder_logo_dir = get_placeholder_logo_dir();
  array_map( 'unlink', glob( $placeholder_logo_dir . '/*' ) );

  $query = new WP_Query( [
    'post_type' => 'custom',
    'posts_per_page' => -1,
    'post_status' => 'any',
    'fields' => 'ids',
  ] );

  $counter = 0;
  foreach( $query->posts as $post_id ) {
    if ( ! get_field( 'logo', $post_id ) ) {
      generate_placeholder_logo( $post_id );
      $counter++;
    }
  }
  return $counter;
}

function get_wholesaler_logo_url( $post_id = NULL ) {
  
  if ( ! $post_id ) {
    global $post;
    $post_id = $post->ID;
  }

  $logo_url = '';
  if ( $logo = get_field( "logo", $post_id ) ) {
    $logo_url = $logo[ "sizes" ][ "medium" ];
  } else {
    $placeholder_logo_dir = get_placeholder_logo_dir( 'baseurl' );
    $logo_url = sprintf( '%s/%s.png', $placeholder_logo_dir, $post_id );
  }

  return $logo_url;
}