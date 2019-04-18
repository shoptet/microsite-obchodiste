<?php

/**
 * Register custom post types and taxonomies
 */
add_action( 'init', function() {
  register_post_type( 'custom', get_cpt_wholesaler_args() );
  register_taxonomy( 'customtaxonomy', 'custom', get_cpt_wholesaler_taxonomy_args() );
  register_post_type( 'special_offer', get_cpt_special_offer_args() );
  register_post_type( 'product', get_cpt_product_args() );
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
 * Remove Yoast page analysis columns from post lists for subscribers
 */
add_action( 'init', function() {
  global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
	if ( user_can( $current_user, 'subscriber' ) ) {
    remove_action( 'admin_init', [ $GLOBALS['wpseo_meta_columns'], 'setup_hooks' ] ); // Remove Yoast page analysis columns from post lists
  }
} );

/**
 * Register image sizes
 */
add_action( 'after_setup_theme', function() {
  add_image_size( 'wholesaler-logo-thumb', 150, 150 );
  add_image_size( 'product-thumb', 1024, 680 );
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
 * Add meta and open graph description to wholesaler and product detail page
 */
add_action( 'wp_head', function() {
  global $post;
  $description = NULL;
  if ( is_singular( 'custom' ) && get_field( "short_about" ) ) {
    $description = strip_tags( get_field( "short_about" ) );
  } else if ( is_singular( 'product' ) && get_field( "short_description" ) ) {
    $description = strip_tags( get_field( "short_description" ) );
  }
  if ( $description ) {
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
 * Add reCAPTCHA script to wholesaler detail page
 */
add_action( 'wp_enqueue_scripts', function() {
  if ( ! is_singular( 'custom' ) && ! is_singular( 'product' )  ) return;
  wp_enqueue_script( 'recaptcha', '//www.google.com/recaptcha/api.js' );
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

  // search form data by selected custom post type
  $search_form_data = [
    'custom' => [
      'formAction' => get_post_type_archive_link( 'custom' ),
      'searchInputPlaceholder' => __( 'Jakého velkoobchodního prodejce hledáte?', 'shp-obchodiste' ),
      'submitButtonText' => __( 'Hledat velkoobchodní prodejce', 'shp-obchodiste' ),
    ],
    'product' => [
      'formAction' => get_post_type_archive_link( 'product' ),
      'searchInputPlaceholder' => __( 'Jaký produkt byste chtěli prodávat?', 'shp-obchodiste' ),
      'submitButtonText' => __( 'Hledat produkt', 'shp-obchodiste' ),
    ],
  ];
  printf( 'window.searchFormData = %s;', json_encode( $search_form_data  ) );

  // post type archive urls
  echo 'window.archiveUrl = [];';
  foreach ( get_post_types() as $post_type ) {
    printf( 'window.archiveUrl[\'%s\'] = \'%s\';', $post_type , get_post_type_archive_link( $post_type ) );
  }

  // wholesaler location
  $location = NULL;
  if ( is_singular( 'custom' ) && get_post_meta( get_queried_object_id(), 'location' ) ) {
    $location = get_post_meta( get_queried_object_id(), 'location' );
  } else if (
    is_singular( 'product' ) && get_field( 'related_wholesaler' ) && get_post_meta( get_field( 'related_wholesaler' )->ID, 'location' ) ) {
    $location = get_post_meta( get_field( 'related_wholesaler' )->ID, 'location' );
  }
  if ( $location ) {
    printf( 'window.wholesalerLocation = %s;', json_encode( $location[ 0 ] ) );
  }
  
  echo '</script>';
} );

/**
 * Make post title required
 */
add_action( 'admin_footer', function() {
  global $post, $pagenow;
  if ( ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) || ( 'custom' !== $post->post_type && 'special_offer' !== $post->post_type )  ) return;
  echo '<script>document.getElementById("title").required = true;</script>';
} );

/**
 * Load addtional fonts
 */
add_action( 'wp_footer', function() {
  echo '<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:600&amp;subset=latin-ext" rel="stylesheet">';
} );

/**
 * Make post title required
 */
add_action( 'admin_footer', function() {
  global $post, $pagenow;
  if ( ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) || 'custom' !== $post->post_type  ) return;
  echo '<script>document.getElementById("title").required = true;</script>';
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
 * Show only own wholesaler, special offer and product post for subscriber
 */
add_action( 'pre_get_posts', function( $wp_query ) {
  global $current_user, $pagenow;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
	// Not the correct screen, bail out
	if( ! is_admin() || 'edit.php' !== $pagenow ) return;
	// Not the correct post type, bail out
  if( ! in_array( $wp_query->query[ 'post_type' ], [ 'custom', 'special_offer', 'product' ] ) ) return;
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

	$meta_query[] = $get_array_meta_query( 'region' );
  
  // Set service meta query
  // checkbox fields are stored as serialized arrays
  if( isset( $_GET[ 'services' ] ) && is_array( $_GET[ 'services' ] ) ) {
    foreach( $_GET[ 'services' ] as $service ) {
      $meta_query[] = [[
        'key' => 'services',
        'value' => $service,
        'compare' =>  'LIKE',
      ]];
    }
  }

  $wp_query->set( 'meta_query', $meta_query );
  
  // Set taxonomy query
  if( !$wp_query->is_tax( 'customtaxonomy' ) && isset( $_GET[ 'category' ] ) && is_array( $_GET[ 'category' ] ) ) {
    $wp_query->set( 'tax_query', [[
      'taxonomy' => 'customtaxonomy',
      'field' => 'term_id',
      'terms' => $_GET[ 'category' ],
      'operator'	=> 'IN',
    ]]);
  }
} );

/**
 * Handle filtering and ordering special offer and product archive
 */
add_action('pre_get_posts', function( $wp_query ) {
	// bail early if is in admin, if not main query (allows custom code / plugins to continue working) or if not wholesaler archive or taxonomy page
	if ( is_admin() || !$wp_query->is_main_query() || !in_array( $wp_query->get( 'post_type' ), [ 'special_offer', 'product' ] ) ) return;

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
	 * Handle ordering queries
	 */

  if( isset($_GET[ 'orderby' ]) ) {
		$query = explode( "_", $_GET[ 'orderby' ] );
		
    if ( $query[0] == 'title' ) {
      $wp_query->set('orderby', 'title');
			$wp_query->set('order', $query[1]);
    } else if ( $query != ['date', 'desc'] ) {
      // skip default ordering by post_date DESC
      // e.g. '?orderby=date_asc'
			$wp_query->set('orderby', 'meta_value_num');
			$wp_query->set('meta_key', $query[0]);
			$wp_query->set('order', $query[1]);
    }
    
	}

	/**
	 * Handle filtering queries - filtered by wholesalers
	 */
  
  // Filtered wholesalers arguments
  $wp_query_wholesaler_args = [
    'post_type' => 'custom',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
  ];

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

  $wp_query_wholesaler_args[ 'meta_query' ] = [];
  $wp_query_wholesaler_args[ 'meta_query' ][] = $get_array_meta_query( 'region' );

  // Set taxonomy query
  if( isset( $_GET[ 'category' ] ) && is_array( $_GET[ 'category' ] ) ) {
    $wp_query_wholesaler_args[ 'tax_query' ] =  [[
      'taxonomy' => 'customtaxonomy',
      'field' => 'term_id',
      'terms' => $_GET[ 'category' ],
      'operator'	=> 'IN',
    ]];
  }

  $wp_query_wholesaler = new WP_Query( $wp_query_wholesaler_args );

  // Query for special offers or products by filtered wholesalers
  $meta_query[] = [[
    'key' => 'related_wholesaler',
    'value' => ( empty( $wp_query_wholesaler->posts ) ? NULL : $wp_query_wholesaler->posts ), // unexpected behavior if empty array – returning all items instead of empty result
    'compare'	=> 'IN',
  ]];

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

  // Verify reCAPTCHA
  $recaptcha_response = sanitize_text_field( $_POST[ 'g-recaptcha-response' ] );
  $recaptcha = new \ReCaptcha\ReCaptcha( G_RECAPTCHA_SECRET_KEY );
  $resp = $recaptcha->verify( $recaptcha_response, $_SERVER['REMOTE_ADDR'] );
  if ( ! $resp->isSuccess() ) {
    status_header( 403 );
    die();
  }

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
 * Update wholesaler categories
 */
add_action( 'save_post', function( $post_id ) {
	// Not the correct post type, bail out
  if ( 'custom' !== get_post_type( $post_id ) ) return;
  $post_categories = [];
  $post_categories[] = get_field( 'category', $post_id )->term_id;
  if ( get_field( 'minor_category_1', $post_id ) ) {
    $post_categories[] = get_field( 'minor_category_1', $post_id )->term_id;
  }
  if ( get_field( 'minor_category_2', $post_id ) ) {
    $post_categories[] = get_field( 'minor_category_2', $post_id )->term_id;
  }
  wp_set_post_terms( $post_id, $post_categories, 'customtaxonomy' );
} );

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
 * Add instructions above custom post title
 */
add_action( 'edit_form_top', function( $post ) {
  if ( 'custom' !== $post->post_type  ) return;
  echo '<p class="description" style="margin: 1rem 0 0 0;">' . __( 'Zadejte oficiální název firmy dle IČ. Např. „Shoptet s.r.o.“', 'shp-obchodiste' ) . '</p>';
});

/**
 * Send e-mail when new wholesaler or special offer is pending for review
 */
add_action( 'transition_post_status',  function( $new_status, $old_status, $post) {

  $post_type = get_post_type( $post );

  // Only new wholesaler, special offer or product post
  if ( ! in_array( $post_type, [ 'custom', 'special_offer', 'product' ] ) ) return;
	if ( $old_status !== 'draft' || $new_status !== 'pending' ) return;

	$options = get_fields( 'options' );

  // Check e-mail recipients
	if ( ! isset( $options[ 'pending_email_recipients' ] ) || ! is_array( $options[ 'pending_email_recipients' ] ) ) return;

  // Get recipients and wholesaler post id
	$email_recipients = $options[ 'pending_email_recipients' ];

  // Collect recipient e-mails
	$email_recipients_emails = [];
	foreach ( $email_recipients as $user ) {
		if ( ! isset($user[ 'user_email' ]) ) continue;
		$email_recipients_emails[] = $user[ 'user_email' ];
	}

  // Get wholesaler title and ACF options
	$title = $post->post_title;
  $email_from = $options[ 'email_from' ];
  
  // Set diferent option variables for diferent post type
  switch ( get_post_type( $post ) ) {

    case 'custom':
    $email_subject = $options[ 'pending_email_subject' ];
    $email_body = $options[ 'pending_email_body' ];
    $to_replace = [ '%wholesaler_name%' => $title ];
    break;

    case 'special_offer':
    $email_subject = $options[ 'pending_special_offer_email_subject' ];
    $email_body = $options[ 'pending_special_offer_email_body' ];
    $to_replace = [ '%offer_name%' => $title ];
    break;

    case 'product':
    $email_subject = $options[ 'pending_product_email_subject' ];
    $email_body = $options[ 'pending_product_email_body' ];
    $to_replace = [ '%product_name%' => $title ];
    break;
  }
  
  // Replace e-mail body variables
  $email_body = strtr( $email_body, $to_replace );

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
 * Set cron for increasing fake message number
 */
if ( ! wp_next_scheduled( 'increase_fake_message_number' ) ) {
  wp_schedule_event( time(), 'hourly', 'increase_fake_message_number' );
}
add_action( 'increase_fake_message_number', function() {
	$options = get_fields('options');
  if ( ! isset( $options['fake_message_number'] ) || ! isset( $options['fake_message_max_increase_constant'] ) ) return;
  $increase_constant = rand( 0, (int) $options['fake_message_max_increase_constant'] ); // Generate random number from 0 to user defined value
  $new_fake_message_number = (int) $options['fake_message_number'] + $increase_constant;
  update_field( 'fake_message_number', $new_fake_message_number, 'options' );
});
//do_action( 'increase_fake_message_number' );

/**
 * Disable special offer single page
 */
add_action( 'template_redirect', function() {
  global $wp_query;
  if ( is_single() && 'special_offer' == $wp_query->query[ 'post_type' ] ) {
    $wp_query->set_404();
    status_header( 404 );
  }
} );

/**
 * Remove links to special offer single page in admin
 */
add_action( 'admin_head', function() {
  global $post, $pagenow;
  if ( ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && 'special_offer' === $post->post_type ) {
    echo '
<style>
  #wp-admin-bar-view,
  #preview-action,
  #message a,
  #titlediv div.inside {
    display: none !important;
  }
</style>
    ';
  }
} );

/**
 * Remove wp admin bar link to create new content
 */
add_action( 'admin_head', function() {
  global $current_user, $pagenow, $wp_query, $post;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;

  echo '
<style>
  #wp-admin-bar-new-content { display: none }
</style>
  ';
} );

/**
 * Add admin notice if special offer or product limit exceeded
 */
add_action( 'admin_notices', function() {
  global $current_user, $pagenow, $wp_query, $post;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) return;

  $post_type = $wp_query->query[ 'post_type' ] ?: $post->post_type;
  if ( ! in_array( $post_type, [ 'special_offer', 'product' ] ) ) return;

  $options = get_fields( 'options' );
  $special_offer_limit = $options[ $post_type . '_limit' ];
  if ( is_number_of_posts_exceeded( $post_type ) ): ?>
    <div class="notice notice-warning">
      <p><?php printf( __( '<strong>Dosáhli jste maximálního počtu položek.</strong> Maximální počet položek je %d.', 'shp-obchodiste' ), $special_offer_limit ); ?></p>
    </div>
  <?php else: ?>
    <div class="notice notice-info">
      <p><?php printf( __( 'Maximální počet položek je %d', 'shp-obchodiste' ), $special_offer_limit ); ?></p>
    </div>
  <?php endif;
} );

/**
 * Disable "Add new item" button and edit page if specil offer or product limit exceeded
 */
add_action( 'admin_head', function() {
  global $current_user, $pagenow, $wp_query, $post;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) return;
  $post_type = $wp_query->query[ 'post_type' ] ?: $post->post_type;
  if ( ! in_array( $post_type, [ 'special_offer', 'product' ] ) ) return;
  if ( ! is_number_of_posts_exceeded( $post_type ) ) return;
  echo '
<style>
  .page-title-action { display: none }
</style>
  ';
} );

/**
 * Disable "Add new special offer" button and special offer edit page if specil offer limit exceeded
 */
add_action( 'admin_head', function() {
  global $current_user, $pagenow, $post;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ( 'post-new.php' !== $pagenow ) return;
  $post_type = $post->post_type;
  if ( ! is_number_of_posts_exceeded( $post_type ) ) return;
  if ( ! in_array( $post_type, [ 'special_offer', 'product' ] ) ) return;
  echo '
<style>
  #poststuff { display: none }
</style>
  ';
} );

/**
 * Remove "Add new item" submenu item if post type limit exceeded
 */
add_action( 'admin_head', function() {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;

  foreach ( [ 'special_offer', 'product' ] as $post_type ) {
    if ( ! is_number_of_posts_exceeded( $post_type ) ) continue;
    echo '
<style>
  #menu-posts-' . $post_type . ' ul.wp-submenu li:nth-of-type(3) { display: none }
</style>
    ';
  }
} );

/**
 * Include subscribers to dropdown menus
 */
add_filter( 'wp_dropdown_users_args', function ( $query_args ) {
  $query_args['who'] = '';
  return $query_args;
} );


/**
 * Enable custom part of header
 */
define( 'CUSTOM_PART_OF_HEADER', TRUE );
define( "CUSTOM_SEARCH_ACTION", TRUE );
define( "CUSTOM_SEARCH_HEADER", TRUE );