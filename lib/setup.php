<?php

/**
 * Register custom post types and taxonomies
 */
add_action( 'init', function() {
  register_post_type( 'custom', get_cpt_wholesaler_args() );
  register_taxonomy( 'customtaxonomy', 'custom', get_cpt_wholesaler_taxonomy_args() );
  register_post_type( 'wholesaler_message', get_cpt_wholesaler_message_args() );
} );

/**
 * Disable unwanted admin notification e-mails
 */
add_action( 'init', function() {
  // Disable notifying the admin of a new user registartion
  remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
  add_action( 'register_new_user', function( $user_id, $notify = 'user' ) {
    wp_send_new_user_notifications( $user_id, $notify ); 
  } );
  // Disable notifying admin of a user changing password
  remove_action( 'after_password_reset', 'wp_password_change_notification' );
} );

/**
 * Register image sizes
 */
add_action( 'after_setup_theme', function() {
  add_image_size( 'wholesaler-logo-thumb', 150, 150 );
} );

/**
 * Pass data to javascript
 */
add_action( 'wp_head', function() {
  // Add noindex, follow to paged (../page/2/, .../page/3/)
  // via https://blog.bloxxter.cz/jak-spravne-na-strankovani-z-pohledu-seo/ Solution #3
  if ( is_paged() ) {
    echo '<meta name="robots“ content="noindex,follow">';
  }
} );

/**
 * Add meta and open graph description to wholesaler detail page
 */
add_action( 'wp_head', function() {
  global $post;
  if ( is_singular( 'custom' ) && get_field( "short_about" ) ) {
    $description = strip_tags( get_field( "short_about" ) );
    printf( '<meta name="description" content="%s">', $description );
    printf( '<meta property="og:description" content="%s">', $description );
  }
} );

/**
 * Add Mapy.cz API
 */
add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_script( 'mapy.cz', '//api.mapy.cz/loader.js' );
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

  // wholesaler location
  if ( is_singular( 'custom' ) && get_post_meta( get_queried_object_id(), 'location' ) ) {
    $location = get_post_meta( get_queried_object_id(), 'location' );
    printf( 'window.wholesalerLocation = %s;', json_encode( $location[ 0 ] ) );
  }

  echo '</script>';
} );

/**
 * Load addtional fonts
 */
add_action( 'wp_footer', function() {
  echo '<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:600&amp;subset=latin-ext" rel="stylesheet">';
} );

/**
 * Hide WP logo on login page
 */
add_action( 'login_enqueue_scripts', function() {
  echo '
  <style type="text/css">
		#login h1:first-child { display: none; }
  </style>
  ';
} );

/**
 * Remove update nag in admin
 */
add_action( 'admin_head', function() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}, 1 );

/**
 * Redirect subscriber from admin dashboard to wholesaler list
 */
add_action( 'admin_init', function() {
	global $current_user, $pagenow;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( 'index.php' === $pagenow && user_can( $current_user, 'subscriber' ) ) {
    wp_redirect( admin_url( 'edit.php?post_type=custom' ), 301 );
    exit;
  }
} );

/**
 * Show only own wholesaler post for subscriber
 */
add_action( 'pre_get_posts', function( $wp_query ) {
  global $current_user, $pagenow;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
	// Not the correct screen, bail out
	if( ! is_admin() || 'edit.php' !== $pagenow ) return;
	// Not the correct post type, bail out
  if( 'custom' !== $wp_query->query[ 'post_type' ] ) return;
  if ( user_can( $current_user, 'subscriber' ) )
    $wp_query->set( 'author', $current_user->ID );
} );

/**
 * Disable admin bar for subscriber
 */
add_action( 'after_setup_theme', function() {
	global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
	if ( user_can( $current_user, 'subscriber' ) )
		show_admin_bar( false );
} );

/**
 * Remove admin dashboard for subscriber
 */
add_action( 'admin_menu', function() {
	global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) )
		remove_menu_page( 'index.php' );
} );

/**
 * Handle filtering and ordering wholesaler archive and category
 */
add_action('pre_get_posts', function( $wp_query ) {
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

	if( isset( $_GET[ 's' ] ) ) {
		$wp_query->set( 's', $_GET[ 's' ] );
	}

	/**
	 * Handle ordering queries – first order by is_shoptet value, then by order query
	 */

	if( ! isset( $_GET[ 'orderby' ] ) ) {
    $wp_query->set( 'meta_key', 'is_shoptet' );
    $wp_query->set( 'orderby', [ 'meta_value_num' => 'DESC', 'post_date' => 'DESC' ] );
  } else {
    $query = explode( '_', $_GET[ 'orderby' ] );
		if ( $query == [ 'date', 'desc' ] ) {
      $wp_query->set( 'meta_key', 'is_shoptet' );
      $wp_query->set( 'orderby', [ 'meta_value_num' => 'DESC', 'post_date' => $query[1] ] );
    } else {
      if ( $query[0] == 'title' ) {
        // title is not a meta key
        $wp_query->set( 'meta_key', 'is_shoptet' );
        $wp_query->set( 'orderby', [ 'meta_value_num' => 'DESC', 'title' => $query[1] ] );
      } else if ( $query[0] == 'favorite' ) {
        $meta_query[ 'is_shoptet_clause' ] = [
          'key' => 'is_shoptet',
          'compare' => 'EXISTS',
          'type' => 'numeric',
        ];
        $meta_query[ 'contact_count_clause' ] = [
          'key' => 'contact_count',
          'compare' => 'EXISTS',
          'type' => 'numeric',
        ];
        $wp_query->set( 'orderby', [
          'is_shoptet_clause' => 'DESC',
          'contact_count_clause' => $query[1],
        ] );
      }
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
} );

/**
 * Disable default searching
 */
add_action( 'parse_query', function( $query ) {
  if ( is_search() && ! is_admin() ) {
    $query->is_search = false;
    $query->query_vars[ 's' ] = false;
    $query->query[ 's' ] = false;
  }
} );

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
add_action( 'save_post', function( $post_id, $post, $update ) {
	// Not the correct post type, bail out
	if ( 'custom' !== get_post_type( $post_id ) || $update ) return;
	update_post_meta( $post_id, 'contact_count', 0 );
}, 10, 3 );

/**
 * Wholesaler address geocoding
 */
add_action( 'save_post', function( $post_id ) {
	// Not the correct post type, bail out
	if ( 'custom' !== get_post_type( $post_id ) ) return;
  // Clean location data
  delete_post_meta( $post_id, 'location' );
  if ( ! get_field( 'street' ) || ! get_field( 'city' ) || ! get_field( 'zip' ) ) return;
  $address = get_field( 'street' ) . ',' . get_field( 'city' ) . ',' . get_field( 'zip' );
  $address = urlencode( $address );
  $geocode = file_get_contents( 'http://api.mapy.cz/geocode?query=' . $address );
  if ( ! xml_parse_into_struct( xml_parser_create(), $geocode, $output ) ) return;
  if ( $output[ 1 ][ 'attributes' ][ 'MESSAGE' ] !== 'OK' ) return;
  $location = [
    'lat' => $output[ 2 ][ 'attributes' ][ 'Y' ],
    'lng' => $output[ 2 ][ 'attributes' ][ 'X' ],
  ];
  update_post_meta( $post_id, 'location', $location );
} );

/**
 * Send e-mail when new wholesaler is pending for review
 */
add_action( 'transition_post_status',  function( $new_status, $old_status, $post) {

  // Only new wholesaler post
	if ( get_post_type( $post ) !== 'custom' || $old_status !== 'draft' || $new_status !== 'pending' ) return;

	$options = get_fields( 'options' );

  // Check e-mail recipients
	if ( ! isset($options[ 'pending_email_recipients' ] ) || ! is_array( $options[ 'pending_email_recipients' ] ) ) return;

  // Get recipients and wholesaler post id
	$email_recipients = $options[ 'pending_email_recipients' ];
	$wholesaler_id = $post->ID;

  // Collect recipient e-mails
	$email_recipients_emails = [];
	foreach ( $email_recipients as $user ) {
		if ( ! isset($user[ 'user_email' ]) ) continue;
		$email_recipients_emails[] = $user[ 'user_email' ];
	}

  // Get wholesaler title and ACF options
	$wholesaler_title = $post->post_title;
	$email_from = $options[ 'email_from' ];
	$email_subject = $options[ 'pending_email_subject' ];
	$email_body = $options[ 'pending_email_body' ];

  // Replace e-mail body variables
	$to_replace = [
		'%wholesaler_name%' => $wholesaler_title,
  ];
  $email_body = strtr($email_body, $to_replace);

  // Send e-mail
	wp_mail(
		$email_recipients_emails,
		$email_subject,
		$email_body,
		[
			'From: ' . $email_from,
			'Content-Type: text/html; charset=UTF-8',
		]
	);
}, 10, 3 );

/**
 * Add class for small and medium acf input field
 */
add_action( 'admin_head', function() {
	echo '
<style>
  .acf-field-small .acf-input {
		max-width: 250px !important;
  }
	.acf-field-medium .acf-input {
		max-width: 450px !important;
  }
</style>
  ';
} );

/**
 * Disable wholesaler title, slug and status editing for publish wholesaler post
 */
add_action( 'admin_head', function() {
  global $post, $pagenow, $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( 'post.php' === $pagenow && 'custom' === $post->post_type && 'publish' === $post->post_status && user_can( $current_user, 'subscriber' ) ) {
    echo '
<style>
  #edit-slug-buttons,
  .edit-post-status {
    display: none;
  }
  #titlediv #title {
    pointer-events: none;
    background-color: transparent;
  }
</style>
    ';
  }
} );

/**
 * Enable custom part of header
 */
define( 'CUSTOM_PART_OF_HEADER', TRUE );
define( "CUSTOM_SEARCH_ACTION", TRUE );
