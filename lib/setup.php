<?php

/**
 * Register custom post types and taxonomies
 */
add_action( 'init', function() {
  register_post_type( 'custom', get_cpt_wholesaler_args() );
  register_taxonomy( 'wholesaler_category', 'custom', get_cpt_wholesaler_taxonomy_args() );
  register_post_type( 'wholesaler_message', get_cpt_wholesaler_message_args() );
} );

/**
 * Pass data to javascript
 */
add_action( 'wp_footer', function() {
  echo '<script>';
  // wordpress ajax url
  printf( 'window.ajaxurl = \'%s\';', admin_url( 'admin-ajax.php' ) );
  // wholesaler terms by id
  printf( 'window.wholesalerTerms = %s;', json_encode( get_terms_by_id( 'customtaxonomy' ) ) );
  // wholesaler terms by id
  printf( 'window.wholesalerArchiveUrl = \'%s\';', get_post_type_archive_link( 'custom' ) );
  echo '</script>';
} );


/**
 * Handle filtering and ordering wholesaler archive and category
 */
add_action('pre_get_posts', function ( $wp_query ){
	// bail early if is in admin, if not main query (allows custom code / plugins to continue working) or if not wholesaler archive or taxonomy page
	if ( is_admin() || !$wp_query->is_main_query() || ( $wp_query->get( 'post_type' ) !== 'custom' && !$wp_query->is_tax( 'customtaxonomy' ) ) ) return;

	$meta_query = $wp_query->get( 'meta_query' );

	if ( $meta_query == '' ) {
		$meta_query = [];
	}

	$wp_query->set( 'posts_per_page', 12 );

	/**
	 * Handle searching
	 */

	if( isset( $_GET[ 'q' ] ) ) {
		$wp_query->set( 's', $_GET[ 'q' ] );
	}

	/**
	 * Handle ordering queries
	 */

	if( isset( $_GET[ 'orderby' ] ) ) {
		$query = explode( '_', $_GET[ 'orderby' ] );

		// skip default ordering by post_date DESC
		// e.g. '?orderby=date_asc'
		if ( $query != [ 'date', 'desc' ] ) {
      if ( $query[0] == 'title' ) {
        $wp_query->set( 'orderby', 'title' );
      } else if ( $query[0] == 'favorite' ) {
        $wp_query->set( 'orderby', 'meta_value_num' );
        $wp_query->set( 'meta_key', 'contact_count' );
      }
			$wp_query->set( 'order', $query[1] );
		}
	}

	/**
	 * Handle filtering queries
	 */

	 // Get array meta query
	 // e.g. '?query[]=0&query[]=1...'
	$get_array_meta_query = function($query) {
		$result = [];
		if( isset( $_GET[ $query ] ) && is_array( $_GET[ $query ] ) ) {
			$result[] = [
				'key' => $query,
				'value' => $_GET[ $query ],
				'compare'	=> 'IN',
			];
		}
		return $result;
	};

	$meta_query[] = $get_array_meta_query( 'category' );
	$meta_query[] = $get_array_meta_query( 'region' );

	$wp_query->set( 'meta_query', $meta_query );
});

/**
 * Handle ajax wholesaler message request
 */
add_action( 'wp_ajax_wholesaler_message', 'handle_wholesaler_message' );
add_action( 'wp_ajax_nopriv_wholesaler_message', 'handle_wholesaler_message' );
function handle_wholesaler_message() {

  // Sanitize message post data
  $name = sanitize_text_field( $_POST[ 'name' ] );
  $email = sanitize_email( $_POST[ 'email' ] );
  $message = sanitize_textarea_field( $_POST[ 'message' ] );
  $wholesaler_id = intval( $_POST[ 'wholesaler_id' ] );

  // Insert wholesaler message post
  $postarr = [
    'post_type' => 'wholesaler_message',
    'post_title' => $name,
    'post_status' => 'publish',
    'meta_input' => [
      'email' => $email,
      'message' => $message,
      'wholesaler' => $wholesaler_id,
    ],
  ];
  wp_insert_post( $postarr );

  // Increase wholesaler contact count
  $wholesaler_contact_count = get_post_meta( $wholesaler_id, 'contact_count', true );
  if ( is_numeric( $wholesaler_contact_count ) ) {
    update_post_meta( $wholesaler_id, 'contact_count', $wholesaler_contact_count + 1 );
  } else {
    update_post_meta( $wholesaler_id, 'contact_count', 1 );
  }

  // Get wholesaler post fields
  $wholesaler_title = get_the_title( $wholesaler_id );
  $wholesaler_contact_email = get_post_meta( $wholesaler_id, 'contact_email' );

  // Get ACF e-mail options
  $options = get_fields( 'options' );
  $email_from = $options[ 'email_from' ];
  $wholesaler_email_body = $options[ 'wholesaler_email_body' ];
  $wholesaler_email_subject = $options[ 'wholesaler_email_subject' ];
  $retailer_email_body = $options[ 'retailer_email_body' ];
  $retailer_email_subject = $options[ 'retailer_email_subject' ];

  // Replace e-mail body variables
  $to_replace = [
    '%contact_name%' => $name,
    '%contact_email%' => $email,
    '%contact_message%' => $message,
    '%wholesaler_name%' => $wholesaler_title,
  ];
  $wholesaler_email_body = strtr( $wholesaler_email_body, $to_replace );
  $retailer_email_body = strtr( $retailer_email_body, $to_replace );

  // Send e-mail to wholesaler
  wp_mail(
    $wholesaler_contact_email,
    $wholesaler_email_subject,
    $wholesaler_email_body,
    [
      'From: ' . $email_from,
      'Reply-to: ' . $email,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );

  // Send e-mail to retailer
  wp_mail(
    $email,
    $retailer_email_subject,
    $retailer_email_body,
    [
      'From: ' . $email_from,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );

  wp_die();
}

/**
 * Initialize wholesaler contact count meta
 */
add_action( 'save_post', function( $post_id ) {
	// Not the correct post type, bail out
	if ( 'custom' !== get_post_type( $post_id ) ) return;
	update_post_meta( $post_id, 'contact_count', 0 );
} );
