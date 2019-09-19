<?php

require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );
require_once( ABSPATH . 'wp-admin/includes/image.php' );

function capture_sentry_message( $message ) {
  if ( ! class_exists( 'WP_Sentry_Php_Tracker' ) || empty( $message ) ) return;
  $sentry_client = WP_Sentry_Php_Tracker::get_instance()->get_client();
  $sentry_client->captureMessage( $message );
}

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
  $terms = get_terms( [ 'taxonomy' => $taxonomy, 'parent' => 0 ] );
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
      // TODO: Optimize is_region_used function
      // if ( $is_region_used( $region_id ) ) $used_regions[] = [
      //   'id' => $region_id,
      //   'name' => $region_name,
      // ];
      $used_regions[] = [
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
function separate_thousands( $num, $decimals = false ): string
{
  if ( ! is_numeric( $num ) ) return $num;
  if ( $decimals )
    return str_replace(',00', '', (string)number_format( $num, 2, ',', '&nbsp;' ) );
  return number_format( $num, 0, ',', '&nbsp;' );
}

/**
 * Is number of posts exceeded for current user
 */
function is_number_of_posts_exceeded( $post_type, $user_id = NULL ): bool
{
  if ( ! $user_id ) {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    $user_id = $current_user->ID;
  }

  $wp_query = new WP_Query( [
    'post_type' => $post_type,
    'posts_per_page' => -1,
    'author' => $user_id,
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

function get_user_wholesaler( $user, $post_status = null ) {
  $args = [
    'post_type' => 'custom',
    'posts_per_page' => 1,
    'author' => $user->ID,
  ];
  if ( $post_status ) $args['post_status'] = $post_status;
  $wp_query = new WP_Query( $args );
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

function insert_image_from_url( $url, $post_id ) {
  $timeout_seconds = 5;
  $tmp_file = download_url( $url, $timeout_seconds );

  if ( is_wp_error( $tmp_file ) ) {
    capture_sentry_message( $tmp_file->get_error_message() );
    return false;
  }
  
  // fix file filename for query strings
  preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches );
  $file = [
    'name' => basename( $matches[0] ),
    'tmp_name' => $tmp_file,
  ];

  $id = media_handle_sideload( $file, $post_id );

  // If error storing permanently, unlink.
  if ( is_wp_error( $id ) ) {
    capture_sentry_message( $id->get_error_message() );
    @unlink( $tmp_file );
    return false;
  }

  // attach image to post thumbnail
  $post_meta_id = set_post_thumbnail( $post_id, $id );
  if ( ! $post_meta_id  ) return false;

  return $id;
}

function has_query_terms( $terms, $taxonomy ): bool
{
  $result = false;
  while ( have_posts() ) {
    the_post();
    if ( ! has_term( $terms, $taxonomy ) ) continue;
    $result = true;
    break;
  }
  wp_reset_query();
  return $result;
}

/**
 * Export wholesaler and number of its products to csv file
 */
function export_wholesalers(): void
{
  $file_path = get_temp_dir() . 'export_wholesalers.csv';
  $fp = fopen( $file_path, 'w' );
  $header = [
    'company name',
    'status',
    'products',
    'contact person name',
    'contact person e-mail',
  ];
  fputcsv( $fp, $header );

  $wp_query = new WP_Query( [
    'post_type' => 'custom',
    'posts_per_page' => -1,
    'post_status' => 'any',
    'no_found_rows' => true,
    'update_post_term_cache' => false,
  ] );
  foreach( $wp_query->posts as $post ) {
    $row = [];
    $contact_person_name = get_post_meta( $post->ID, 'contact_full_name', true );
    $contact_person_email = get_post_meta( $post->ID, 'contact_email', true );

    $wp_query_all_products = new WP_Query( [
      'post_type' => 'product',
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'meta_query' => [
        [
          'key' => 'related_wholesaler',
          'value' => $post->ID,
        ],
      ],
    ] );

    $row[] = $post->post_title;
    $row[] = $post->post_status;
    $row[] = $wp_query_all_products->found_posts;
    $row[] = $contact_person_name;
    $row[] = $contact_person_email;

    fputcsv( $fp, $row );
  }

  fclose( $fp );

  // Http headers for downloads
  header( 'Pragma: public' );
  header( 'Expires: 0' );
  header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ); 
  header( 'Content-Type: application/octet-stream' );
  header( 'Content-Disposition: attachment; filename=export.csv' );
  header( 'Content-Transfer-Encoding: binary' );
  header( 'Content-Length: ' . filesize( $file_path ) );
  while ( ob_get_level() ) {
    ob_end_clean();
    @readfile( $file_path );
  }

  unlink( $file_path );
}

function count_posts_by_term ( $post_type, $term, $taxonomy ): int
{
  $query = new WP_Query( [
    'post_type' => $post_type,
    'post_status' => 'publish',
    'fields' => 'ids',
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    'tax_query' => [
      [
        'taxonomy' => $taxonomy,
        'terms' => $term->term_id,
        'include_children' => true,
      ],
    ],
  ] );
  return $query->found_posts;
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

function remove_placeholder_logo( $post_id ): void
{
  $placeholder_logo_dir = get_placeholder_logo_dir();
  $placeholder_logo_path = sprintf( '%s/%s.png', $placeholder_logo_dir, $post_id );
  if ( file_exists( $placeholder_logo_path ) ) {
    unlink( $placeholder_logo_path );
  }
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

/**
 * Get unique global id across all posts and terms
 */
function get_global_id( $id, $type ) {
  switch ( $type ) {
    case 'post':
    $prefix = 1;
    break;
    case 'term':
    $prefix = 2;
    break;
    default:
    return false;
    break;
  }
  $global_id = $prefix . $id;
  return $global_id;
}
