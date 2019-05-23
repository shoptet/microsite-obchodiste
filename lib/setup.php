<?php

/**
 * Register custom post types and taxonomies
 */
add_action( 'init', function() {
  register_post_type( 'custom', get_cpt_wholesaler_args() );
  register_taxonomy( 'customtaxonomy', 'custom', get_cpt_wholesaler_taxonomy_args() );
  register_post_type( 'special_offer', get_cpt_special_offer_args() );
  register_post_type( 'product', get_cpt_product_args() );
  register_taxonomy( 'producttaxonomy', 'product', get_cpt_product_taxonomy_args() );
  register_post_type( 'wholesaler_message', get_cpt_wholesaler_message_args() );
  register_post_type( 'sync', get_cpt_sync_args() );
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
 * Add new post status for sync post
 */
add_action( 'init', function() {
  register_post_status( 'waiting', [
    'label' => __( 'Čeká na zpracování', 'shp-obchodiste' ),
    'public' => true,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => false,
		'post_type' => [ 'sync' ],
		'label_count' => _n_noop( 'Čeká na zpracování <span class="count">(%s)</span>', 'Čeká na zpracování <span class="count">(%s)</span>' ),
  ] );
  register_post_status( 'done', [
    'label' => __( 'Zpracováno', 'shp-obchodiste' ),
    'public' => false,
		'show_in_admin_all_list' => false,
		'show_in_admin_status_list' => true,
		'post_type' => [ 'sync' ],
		'label_count' => _n_noop( 'Zpracováno <span class="count">(%s)</span>', 'Zpracováno <span class="count">(%s)</span>' ),
  ] );
  register_post_status( 'error', [
    'label' => __( 'Chyba', 'shp-obchodiste' ),
    'public' => false,
		'show_in_admin_all_list' => false,
		'show_in_admin_status_list' => true,
		'post_type' => [ 'sync' ],
		'label_count' => _n_noop( 'Chyba <span class="count">(%s)</span>', 'Chyba <span class="count">(%s)</span>' ),
	] );
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
  if ( is_singular( 'custom' ) && get_field( "about_company" ) ) {
    $description = strip_tags( get_field( "about_company" ) );
  } else if ( is_singular( 'product' ) && get_field( "short_description" ) ) {
    $description = strip_tags( get_field( "short_description" ) );
  }
  if ( $description ) {
    printf( '<meta name="description" content="%s">', $description );
    printf( '<meta property="og:description" content="%s">', $description );
  }
} );

/**
 * Add meta and open graph description to wholesaler and product detail page
 */
add_action( 'wp_head', function() {
  global $post;
  if( ! is_singular( 'product' ) ) return;
  if ( $price = get_field( "price" ) ) {
    printf( '<meta property="product:price:amount" content="%d">', $price );
    printf( '<meta property="product:price:currency" content="%s">', 'CZK' );
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
  
  // wholesaler and product terms by id
  echo 'window.terms = [];';
  printf( 'window.terms.custom = %s;', json_encode( get_terms_by_id( 'customtaxonomy' ) ) );
  printf( 'window.terms.product = %s;', json_encode( get_terms_by_id( 'producttaxonomy' ) ) );

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
 * Disable product submit button when no own wholesaler is published
 */
add_action( 'admin_footer', function() {
  global $post, $pagenow, $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it  
  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ( ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) || 'product' !== $post->post_type  ) return;
  if ( get_user_wholesaler( $current_user, 'publish' ) ) return;
  echo '<script>document.getElementById("publish").disabled = true;</script>';
} );

/**
 * Disable import products csv when no own wholesaler is created
 */
add_action( 'admin_footer', function() {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  $screen = get_current_screen();
  
  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ('product_page_product-import' !== $screen->base ) return;
  if ( get_user_wholesaler( $current_user ) ) return;
  echo '<script>document.getElementById("publish").disabled = true;</script>';
} );

/**
 * Add alert when no own wholesaler is published
 */
add_action( 'post_submitbox_misc_actions', function() {
  global $post, $pagenow, $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ( ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) || 'product' !== $post->post_type  ) return;
  if ( get_user_wholesaler( $current_user, 'publish' ) ) return;
  echo '<div class="misc-pub-section" style="color:#c00">';
  printf(
    __( 'Produkt bude možné odeslat ke schválení, až bude vytvořen a schválen <a href="%s" style="color:#c00" target="_blank">medailonek vašeho velkoobchodu</a>', 'shp-obchodiste' ),
    admin_url( 'post-new.php?post_type=custom' )
  );
  echo '</div>';
});

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
 * Redirect logic for subscribers
 */
add_action( 'admin_init', function() {
	global $current_user, $pagenow;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  // Redirection login for subscribers only
  if ( ! user_can( $current_user, 'subscriber' ) ) return;

  // Redirect user from dashboard to wholesalers list
  if ( 'index.php' === $pagenow ) {
    wp_redirect( admin_url( 'edit.php?post_type=custom' ), 301 ); exit;
  }

  // Redirect user form wholesaler list to new wholesaler / edit wholesaler page
  if ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'custom' ) {
    if ( $wholesaler = get_user_wholesaler( $current_user ) ) {
      wp_redirect( admin_url( 'post.php?post=' . $wholesaler->ID . '&action=edit' ), 301 ); exit;
    } else {
      wp_redirect( admin_url( 'post-new.php?post_type=custom' ), 301 ); exit;
    }
  }

  // Redirect user with wholesaler from new wholesaler to edit wholesaler page
  if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'custom' ) {
    if ( $wholesaler = get_user_wholesaler( $current_user ) ) {
      wp_redirect( admin_url( 'post.php?post=' . $wholesaler->ID . '&action=edit' ), 301 ); exit;
    }
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
    $result = [ 'relation' => 'OR' ];
    foreach( $_GET[ 'services' ] as $service ) {
      $result[] = [
        'key' => 'services',
        'value' => $service,
        'compare' =>  'LIKE',
      ];
    }
    $meta_query[] = $result;
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
 * Handle filtering and ordering special offer archive
 */
add_action('pre_get_posts', function( $wp_query ) {
	// bail early if is in admin, if not main query (allows custom code / plugins to continue working) or if not special offer archive page
	if ( is_admin() || !$wp_query->is_main_query() || ( $wp_query->get( 'post_type' ) !== 'special_offer' ) ) return;

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

  // Query for special offers by filtered wholesalers
  $meta_query[] = [[
    'key' => 'related_wholesaler',
    'value' => ( empty( $wp_query_wholesaler->posts ) ? NULL : $wp_query_wholesaler->posts ), // unexpected behavior if empty array – returning all items instead of empty result
    'compare'	=> 'IN',
  ]];

	$wp_query->set( 'meta_query', $meta_query );
} );

/**
 * Handle filtering and ordering product archive
 */
add_action('pre_get_posts', function( $wp_query ) {
	// bail early if is in admin, if not main query (allows custom code / plugins to continue working) or if not product archive or taxonomy page
	if ( is_admin() || !$wp_query->is_main_query() || ( $wp_query->get( 'post_type' ) !== 'product' ) && !$wp_query->is_tax( 'producttaxonomy' ) ) return;

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

  $wp_query_wholesaler = new WP_Query( $wp_query_wholesaler_args );

  // Query for products by filtered wholesalers
  $meta_query[] = [[
    'key' => 'related_wholesaler',
    'value' => ( empty( $wp_query_wholesaler->posts ) ? NULL : $wp_query_wholesaler->posts ), // unexpected behavior if empty array – returning all items instead of empty result
    'compare'	=> 'IN',
  ]];

  $wp_query->set( 'meta_query', $meta_query );
  
  // Set taxonomy query
  if( !$wp_query->is_tax( 'producttaxonomy' ) && isset( $_GET[ 'category' ] ) && is_array( $_GET[ 'category' ] ) ) {
    $wp_query->set( 'tax_query', [[
      'taxonomy' => 'producttaxonomy',
      'field' => 'term_id',
      'terms' => $_GET[ 'category' ],
      'operator'	=> 'IN',
    ]]);
  }
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
 * Handle ajax wholesaler and product message request
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
  $post_type = sanitize_text_field( $_POST[ 'post_type' ] );
  $post_id = intval( $_POST[ 'post_id' ] );

  $meta_input = [
    'email' => $email,
    'message' => $message,
  ];

  $options = get_fields( 'options' );

  switch ( $post_type ) {
    case 'custom':
    $meta_input['wholesaler'] = $post_id;
    $wholesaler_id = $post_id;
    $message_email_body = $options[ 'wholesaler_email_body' ];
    $message_email_subject = $options[ 'wholesaler_email_subject' ];
    break;
    case 'product':
    $meta_input['product'] = $post_id;
    $wholesaler_id = get_field( 'related_wholesaler', $post_id, false );
    $message_email_body = $options[ 'product_email_body' ];
    $message_email_subject = $options[ 'product_email_subject' ];
    break;
  }

  // Get wholesaler post fields
  $wholesaler_title = get_the_title( $wholesaler_id );
  $wholesaler_contact_email = get_field( 'contact_email', $wholesaler_id, false );

  // Insert wholesaler message post
  $postarr = [
    'post_type' => 'wholesaler_message',
    'post_title' => $name,
    'post_status' => 'publish',
    'meta_input' => $meta_input,
  ];
  $post_message_id = wp_insert_post( $postarr );

  // Increase wholesaler contact count
  $wholesaler_contact_count = get_post_meta( $wholesaler_id, 'contact_count', true );
  if ( is_numeric( $wholesaler_contact_count ) ) {
    update_post_meta( $wholesaler_id, 'contact_count', $wholesaler_contact_count + 1 );
  } else {
    update_post_meta( $wholesaler_id, 'contact_count', 1 );
  }

  // Get ACF e-mail options
  $email_from = $options[ 'email_from' ];
  $retailer_email_body = $options[ 'retailer_email_body' ];
  $retailer_email_subject = $options[ 'retailer_email_subject' ];

  // Replace e-mail body variables
  $to_replace = [
    '%contact_name%' => $name,
    '%contact_email%' => $email,
    '%contact_message%' => $message,
    '%wholesaler_name%' => $wholesaler_title,
  ];
  if ( 'product' == $post_type ) {
    $to_replace[ '%product_name%' ] = get_the_title( $post_id );
  }
  $message_email_body = strtr( $message_email_body, $to_replace );
  $retailer_email_body = strtr( $retailer_email_body, $to_replace );

  // Send e-mail to wholesaler
  wp_mail(
    $wholesaler_contact_email,
    $message_email_subject,
    $message_email_body,
    [
      'From: ' . $email_from,
      'Reply-to: ' . $email,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );

  update_post_meta(
    $post_message_id,
    'sent_message', 
    $wholesaler_contact_email . '
    ' . $message_email_subject . '
    ' . $message_email_body
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
 * Add instructions above product post title
 */
add_action( 'edit_form_top', function( $post ) {
  if ( 'product' !== $post->post_type  ) return;
  echo '<p class="description" style="margin: 1rem 0 0 0;">' . __( 'Vložte obchodní název vašeho produktu', 'shp-obchodiste' ) . '</p>';
});

/**
 * Send e-mail when new wholesaler, special offer or product is pending for review
 */
add_action( 'transition_post_status',  function ( $new_status, $old_status, $post ) {

  $post_type = get_post_type( $post );
	$options = get_fields( 'options' );
  
	if ( $old_status === 'draft' && $new_status === 'pending' ) {
    // Set recipients for pending notification email

    // Only new wholesaler, special offer or product post
    if ( ! in_array( $post_type, [ 'custom', 'special_offer', 'product' ] ) ) return;

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

  } elseif ( $old_status === 'pending' && $new_status === 'publish' ) {
    // Set recipient for publish notification email
    
    // Only new wholesaler
    if ( ! in_array( $post_type, [ 'custom' ] ) ) return;

    $email_recipients_emails = [];
    $author_id = get_post_field( 'post_author', $post->ID );
    $email_recipients_emails[] = get_the_author_meta( 'user_email', $author_id );
  } else {
    // Bail out if not pending or publish
    return;
  }

  // Get wholesaler title and ACF options
	$title = $post->post_title;
  $email_from = $options[ 'email_from' ];
  
  // Set diferent option variables for diferent post type
  switch ( get_post_type( $post ) ) {

    case 'custom':
    $email_subject = $options[ $new_status . '_email_subject' ];
    $email_body = $options[ $new_status . '_email_body' ];
    $to_replace = [ '%wholesaler_name%' => $title ];
    break;

    case 'special_offer':
    $email_subject = $options[ $new_status . '_special_offer_email_subject' ];
    $email_body = $options[ $new_status . '_special_offer_email_body' ];
    $to_replace = [ '%offer_name%' => $title ];
    break;

    case 'product':
    $email_subject = $options[ $new_status . '_product_email_subject' ];
    $email_body = $options[ $new_status . '_product_email_body' ];
    $to_replace = [ '%product_name%' => $title ];
    break;
  }

  // Set option variables for related wholesaler  
  if ( $related_wholesaler_id = get_post_meta( $post->ID, 'related_wholesaler', true ) ) {
    $to_replace['%wholesaler_name%'] = get_the_title( $related_wholesaler_id );
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
 * Remove ACF gallery sort select
 */
add_action( 'admin_head', function() {
	echo '
<style>
  .acf-gallery-sort {
		display: none;
  }
</style>
  ';
} );

/**
 * Remove wordpress logo fron admin for subscribers
 */
add_action( 'admin_head', function() {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;
	echo '
<style>
  #wp-admin-bar-wp-logo {
		display: none;
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
 * Hide related wholesaler field
 */
add_action( 'admin_head', function() {
  global $post, $pagenow, $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  echo '
<style>
[data-name=related_wholesaler] {
  display: none;
}
</style>
  ';
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
 * Sticky sidebar in admin for large devices
 */
add_action( 'admin_head', function() {
  echo '
<style>
  @media screen and (min-width: 851px) {
    #post-body::after {
      content: "";
      clear: both;
      display: table;
    }
    .postbox-container {
      position: sticky;
      top: 50px;
    }
  }
</style>
  ';
} );

/**
 * Hide redundant profile link in admin bar
 */
add_action( 'admin_head', function() {
  echo '
<style>
  #wpadminbar #wp-admin-bar-user-info .display-name {
    display: none;
  }
</style>
  ';
} );

/**
 * Add admin notices for subscribers
 */
add_action( 'admin_notices', function() {
  global $current_user, $pagenow, $wp_query, $post;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  $screen = get_current_screen();
  
  if ( ! user_can( $current_user, 'subscriber' ) ) return;

  if ( 'product_page_product-import' === $screen->base ) {
    $post_type = 'product';
  } else {
    if ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) return;
    if ( ! $wp_query->query[ 'post_type' ] && ! $post ) return;
  
    $post_type = $wp_query->query[ 'post_type' ] ?: $post->post_type;
    if ( ! in_array( $post_type, [ 'special_offer', 'product' ] ) ) return;
  }

  if ( 'product' === $post_type && ! get_user_wholesaler( $current_user, 'publish' )  ) : ?>
    <div class="notice notice-error">
      <p>
        <?php
        printf(
          __( 'Produkty bude možné odesílat ke schválení, až bude vytvořen a schválen <a href="%s" style="color:#c00" target="_blank">medailonek vašeho velkoobchodu</a>', 'shp-obchodiste' ),
          admin_url( 'post-new.php?post_type=custom' )
        );
        ?>
      </p>
    </div>
  <?php endif;

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
 * Add admin notices for subscribers
 */
add_action( 'admin_notices', function() {
  global $pagenow, $post;
  
  if ( 'post.php' !== $pagenow || 'product' !== $post->post_type ) return;
  $sync_state = get_post_meta( $post->ID, 'sync_state', true );
  if ( 'waiting' === $sync_state ) :
    $query = new WP_Query( [
      'post_type' => 'sync',
      'post_status' => 'waiting',
      'meta_query' => [ [
        'key' => 'product',
        'value' => $post->ID,
      ] ],
    ] );
  ?>
    <div class="notice notice-warning">
      <p><?php printf( __( 'Čeká na stažení obrázků (%d)...', 'shp-obchodiste' ), $query->found_posts ); ?></p>
    </div>
  <?php elseif ( 'error' === $sync_state ) : ?>
    <div class="notice notice-error">
      <p><?php _e( 'Chyba při stahování obrázků', 'shp-obchodiste' ); ?></p>
    </div>
  <?php endif;
} );

/**
 * Add admin notices for subscribers
 */
add_action( 'admin_notices', function() {
  if ( isset( $_GET['products_imported'] ) ) {
    $products_imported = intval( $_GET['products_imported'] );
    
    // Remove query param from url
    ?>
    <script>
      var newUrl = window.location.href.replace('&products_imported=<?php echo $products_imported; ?>','');
      history.pushState({}, null, newUrl);
    </script>
    <?php 
    if ( $products_imported > 0 ): ?>
      <div class="notice notice-success">
        <p><?php printf( __( 'Produkty úspěšně importovány. Celkem přidáno produktů: %d', 'shp-obchodiste' ), $products_imported ); ?></p>
      </div>
    <?php else: ?>
      <div class="notice notice-error">
        <p><?php _e( 'Nebyl importován žádný produkt', 'shp-obchodiste' ); ?></p>
      </div>
    <?php endif;
  }
  if ( isset( $_GET['images_to_sync'] ) ) {
    $images_to_sync = intval( $_GET['images_to_sync'] );
    
    // Remove query param from url
    ?>
    <script>
      var newUrl = window.location.href.replace('&images_to_sync=<?php echo $images_to_sync; ?>','');
      history.pushState({}, null, newUrl);
    </script>
    <?php 
    if ( $images_to_sync > 0 ): ?>
      <div class="notice notice-warning">
        <p><?php printf( __( 'Celkem obrázků přidáno do fronty ke stažení: %d', 'shp-obchodiste' ), $images_to_sync ); ?></p>
      </div>
    <?php endif;
  }
} );

/**
 * Disable "Add new item" button at page title action
 */
add_action( 'admin_head', function() {
  global $current_user, $pagenow, $wp_query, $post;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  if ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) return;
  if ( ! $wp_query->query[ 'post_type' ] && ! $post ) return;
  $post_type = $wp_query->query[ 'post_type' ] ?: $post->post_type;
  if (
    ( in_array( $post_type, [ 'special_offer', 'product' ] ) && is_number_of_posts_exceeded( $post_type ) ) ||
    'custom' === $post_type
  ) {
    echo '
    <style>
      .page-title-action { display: none }
    </style>
    ';
  };

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

  foreach ( [ 'custom', 'special_offer', 'product' ] as $post_type ) {
    if (
      ( in_array( $post_type, [ 'special_offer', 'product' ] ) && is_number_of_posts_exceeded( $post_type ) ) ||
      'custom' === $post_type
    ) {
      echo '
      <style>
        #menu-posts-' . $post_type . ' ul.wp-submenu li:nth-of-type(3) { display: none }
      </style>
      ';
    };
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
 * Remove Medai in menu for subscribers
 */
add_action( 'admin_menu', function () {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;
  remove_menu_page( 'upload.php' );
} );

/**
 * Add filtering by user to admin post list
 */
add_action( 'restrict_manage_posts', function ( $post_type ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) return;

  if ( ! in_array( $post_type, [ 'custom', 'special_offer', 'product' ] ) ) return;
  
  $params = [
    'name' => 'author',
		'show_option_all' => __( '— Autor —', 'shp-obchodiste' ),
  ];
  
  $request_attr = 'user';
	if ( isset( $_REQUEST[ $request_attr ] ) )
		$params['selected'] = $_REQUEST[ $request_attr ];
 
	wp_dropdown_users( $params );

} );

/**
 * Add filtering by wholesaler to admin post list
 */
add_action( 'restrict_manage_posts', function ( $post_type ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) return;

  if ( ! in_array( $post_type, [ 'special_offer', 'product' ] ) ) return;
  
  $selected = '';
  $request_attr = 'wholesaler';
  if ( isset($_REQUEST[$request_attr]) ) {
    $selected = $_REQUEST[$request_attr];
  }

  $wp_query = new WP_Query( [
    'post_type' => 'custom',
    'post_status' => 'any',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
  ] );
  $wholesalers = $wp_query->posts;

  echo '<select id="wholesaler" name="wholesaler">';
  echo '<option value="0">' . __( '— Velkoobchod —', 'shp-obchodiste' ) . ' </option>';
  foreach( $wholesalers as $wholesaler ){
    $select = ( $wholesaler->ID == $selected ) ? ' selected="selected"' : '' ;
    echo '<option value="' . $wholesaler->ID . '"' . $select . '>' . $wholesaler->post_title . ' </option>';
  }
  echo '</select>';

} );

/**
 * Filter special offers and product by wholesalers in admin
 */
add_action( 'pre_get_posts', function( $wp_query ) {
  if( ! is_admin() || ! $wp_query->is_main_query() ) return;

  $post_type = $wp_query->get( 'post_type' );
  if ( ! in_array( $post_type, [ 'special_offer', 'product' ] ) ) return;

  $request_attr = 'wholesaler';
  if ( ! isset($_REQUEST[$request_attr]) || 0 == $_REQUEST[$request_attr] ) return;

  $meta_query = $wp_query->get( 'meta_query' );

	if ( empty( $meta_query ) ) {
		$meta_query = [];
  }
  
  $meta_query = [ [
    'key' => 'related_wholesaler',
    'value' => $_REQUEST[$request_attr],
  ] ];

	$wp_query->set( 'meta_query', $meta_query );
} );

/**
 * Filter messages by wholesaler and product
 */
add_action( 'pre_get_posts', function( $wp_query ) {
  if( ! is_admin() || ! $wp_query->is_main_query() ) return;
  
  $post_type = $wp_query->get( 'post_type' );
  if ( $post_type !== 'wholesaler_message' ) return;
  
  $request_attr = 'related_post_type';
  if ( ! isset($_REQUEST[$request_attr]) ) return;
  
  $meta_query = $wp_query->get( 'meta_query' );

	if ( empty( $meta_query ) ) {
		$meta_query = [];
  }

  $meta_query_key = $_REQUEST[$request_attr];
  if ( $meta_query_key == 'custom' )
    $meta_query_key = 'wholesaler';
  
  $meta_query = [ [
    'key' => $meta_query_key,
  ] ];

	$wp_query->set( 'meta_query', $meta_query );
} );

/**
 * Remove Yoast SEO filters
 */
add_action( 'admin_init', function () {
  global $wpseo_meta_columns;
  if ( ! $wpseo_meta_columns ) return;
  remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown' ] );
  remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown_readability' ] );
} );

/**
 * Add content to admin columns
 */
add_action( 'manage_posts_custom_column', function ( $column, $post_id ) {
	switch ( $column ) {
    // Add related wholesaler to product
    case 'related_wholesaler':
    if ( $related_wholesaler = get_field( 'related_wholesaler', $post_id ) ) {
      echo '<a href="' . get_permalink( $related_wholesaler ) . '">';
      echo get_the_title( $related_wholesaler );
      echo '</a>';
    } else
      echo '<em>' . __( 'Bez velkoobchodu', 'shp-obchodiste' ) . '</em>';
    break;
    // Add message source type to message
    case 'related_post_type':
    if ( get_field( 'wholesaler', $post_id ) )
      echo __( 'Velkoobchod', 'shp-obchodiste' );
    elseif ( $product_id = get_field( 'product', $post_id ) )
      echo __( 'Produkt', 'shp-obchodiste' );
    break;
    // Add related post to message 
    case 'related_post':
    if ( $wholesaler_id = get_field( 'wholesaler', $post_id ) )
      $related_post_id = $wholesaler_id;
    elseif ( $product_id = get_field( 'product', $post_id ) )
      $related_post_id = $product_id;
    else
      break;
    echo '<a href="' . get_permalink( $related_post_id ) . '">';
    echo get_the_title( $related_post_id );
    echo '</a>';
    break;
    case 'sync_state':
    $sync_state = get_field( 'sync_state', $post_id );
    if ( $sync_state === 'waiting' ) {
      $query = new WP_Query( [
        'post_type' => 'sync',
        'post_status' => 'waiting',
        'meta_query' => [ [
          'key' => 'product',
          'value' => $post_id,
        ] ],
      ] );
      echo '<strong style="color:#ffb900"><em>' . sprintf( __( 'Čeká na stažení obrázků (%d)...', 'shp-obchodiste' ), $query->found_posts )  . '</em></strong>';
    }
    elseif ( $sync_state === 'error' )
      echo '<strong style="color:#a00">' . __( 'Chyba při stahování obrázků', 'shp-obchodiste' ) . '</strong>';
    elseif ( $sync_state === 'done' )
      echo '<strong style="color:#006505">✔ ' . __( 'Obrázky staženy', 'shp-obchodiste' ) . '</strong>';
    else
      echo '–';
    break;
	}
}, 10, 2 );

// Via: https://github.com/Hube2/acf-filters-and-functions/blob/master/customized-options-page.php
add_action( 'product_page_product-import', function () {
  ob_start();
}, 1 );
add_action( 'product_page_product-import', function () {
  $content = ob_get_clean();
  $options = get_fields( 'options' );
  
  $product_taxonomy_terms = get_terms( 'producttaxonomy', [
    'hide_empty' => false,
  ] );
    
  $terms_by_id_html = '<h4>' . __( 'Kategorie produktů a jejich ID:', 'shp-obchodiste' ). '</h4>';
  $terms_by_id_html .= '<p>';
  foreach ( $product_taxonomy_terms as $term ) {
    $terms_by_id_html .= '<span style="margin-right:10px;">';
    $terms_by_id_html .= $term->name . ':&nbsp;';
    $terms_by_id_html .= '<code style="font-size:75%">ID: ' . $term->term_id . '</code>';
    $terms_by_id_html .= '</span>';
  }
  $terms_by_id_html .= '</p>';

  $content = str_replace(
    '<div id="normal-sortables"',
    $options[ 'product_import_description' ] .
    $terms_by_id_html .
    '<div id="normal-sortables"',
    $content
  );

  echo $content;
}, 20 );

/**
 * Process CSV file before saving data
 */
add_action( 'acf/save_post', function() {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  $screen = get_current_screen();
  
  if ( 'product_page_product-import' !== $screen->base ) return;

  // bail early if no ACF data
  if( empty( $_POST['acf'] ) ) return;

  $fields = $_POST['acf'];
  foreach( $fields as $key => $value ) {
    $field = acf_get_field( $key );
    switch ( $field['name'] ) {
      case 'related_wholesaler':
      $related_wholesaler_id = $value;
      break;
      case 'product_category':
      $product_category_id = intval( $value );
      break;
      case 'product_import_file':
      $file_path = get_attached_file( $value );
      break;
      case 'set_pending_status':
      $set_pending_status = boolval( intval( $value ) );
      break;
    }
  }

  if ( user_can( $current_user, 'subscriber' ) ) {
    $related_wholesaler = get_user_wholesaler( $current_user );
    $related_wholesaler_id = $related_wholesaler->ID;
  }

  if ( ! isset( $file_path ) ) return;

  $fp = fopen( $file_path, 'r' );

  if ( ! $fp ) return null;

  $header = fgetcsv( $fp, 0, ';' );

  $data = [];
  while ( $row = fgetcsv( $fp, 0, ';' ) ) {
    foreach ( $row as $key => $value ) {
      $row[$key] = iconv( 'CP1250', 'UTF-8', $value );
    }
    $data[] = array_combine( $header, $row );
  }

  fclose( $fp );

  // Proccess data
  $wholesaler_author_id = get_post_field( 'post_author', $related_wholesaler_id );
  $is_related_wholesaler_publish = ( 'publish' === get_post_status( $related_wholesaler_id ) );
  $products_imported = 0;
  $images_to_sync = 0;

  foreach ( $data as $data_item ) {

    // break importing for subscriber if number of products exceeded
    if (
      user_can( $current_user, 'subscriber' ) &&
      is_number_of_posts_exceeded( 'product', $wholesaler_author_id )
    ) break;

    $meta_input = [
      'short_description' => isset( $data_item['shortDescription'] ) ? $data_item['shortDescription'] : '',
      'description' => isset( $data_item['description'] ) ? $data_item['description'] : '',
      'price' => isset( $data_item['price'] ) ? floatval( $data_item['price'] ) : '',
      'minimal_order' => isset( $data_item['minimumAmount'] ) ? $data_item['minimumAmount'] : '',
      'ean' => isset( $data_item['ean'] ) ? $data_item['ean'] : '',
    ];

    $postarr = [
      'post_type' => 'product',
      'post_title' => $data_item['name'],
      'post_author' => $wholesaler_author_id, // Set correct author id
      'post_status' => 'draft',
      'meta_input' => $meta_input,
    ];
    $post_product_id = wp_insert_post( $postarr );

    update_field( 'related_wholesaler', $related_wholesaler_id, $post_product_id ); // Update acf relationship field

    // Set product taxonomy
    if ( isset( $data_item['category'] ) && ! empty( $data_item['category'] ) ) {
      $category_id = intval( $data_item['category'] );
      if ( term_exists( $category_id, 'producttaxonomy' ) ) {
        $product_category_id = $category_id;
      }
    }

    if ( $product_category_id ) {
      wp_set_post_terms( $post_product_id, [ $product_category_id ], 'producttaxonomy' );
    }

    $image_items = [ 'image', 'image2', 'image3', 'image4', 'image5' ];
    foreach ( $image_items as $image_key ) {
      if ( ! isset( $data_item[$image_key] ) || empty( $data_item[$image_key] ) ) continue;
      $postarr = [
        'post_type' => 'sync',
        'post_title' => $data_item['name'] . ' – ' . __( 'Obrázek produktu', 'shp-obchodiste' ),
        'post_status' => 'waiting',
        'meta_input' => [
          'product' => $post_product_id,
          'url' => $data_item[$image_key],
          'is_thumbnail' => ( $image_key === 'image' ),
          'attemps' => 0,
        ],
      ];
      wp_insert_post( $postarr );
      $product_sync_state = 'waiting';
      $images_to_sync++;
    }

    if ( $product_sync_state ) {
      update_post_meta( $post_product_id, 'sync_state', $product_sync_state );
    }

    // Set to pending status
    if (
      $set_pending_status &&
      $product_category_id && $is_related_wholesaler_publish &&
      isset( $data_item['image'] ) && ! empty( $data_item['image'] ) &&
      isset( $data_item['shortDescription'] ) && ! empty( $data_item['shortDescription'] ) &&
      isset( $data_item['description'] ) && ! empty( $data_item['description'] )
    ) {
      wp_update_post( [
        'ID' => $post_product_id,
        'post_status' => 'pending',
      ] );
    }

    $products_imported++;
  }

  $_POST['acf'] = []; // Do not save any data

  // Add query param to url for admin notice
  wp_redirect( add_query_arg( [
    'products_imported' => $products_imported,
    'images_to_sync' => $images_to_sync,
  ] ) );
  exit;
}, 1 );

add_action( 'init', function() {
  if ( ! wp_next_scheduled( 'sync_items' ) ) {
    wp_schedule_event( time(), 'one_second', 'sync_items' );
  }
} );

add_action( 'sync_items', function() {
  $options = get_fields( 'options' );

	$wp_query = new WP_Query( [
    'post_type' => 'sync',
    'post_status' => 'waiting',
    'posts_per_page' => $options[ 'product_image_sync_count' ],
    'orderby' => 'date',
    'order' => 'ASC',
  ] );
  $items = $wp_query->posts;

  foreach ( $items as $item ) {
    $product_id = get_post_meta( $item->ID, 'product', true );
    $url = get_post_meta( $item->ID, 'url', true );
    $is_thumbnail = boolval( intval( get_post_meta( $item->ID, 'is_thumbnail', true ) ) );
    $attemps = intval( get_post_meta( $item->ID, 'attemps', true ) );
    if ( $attemps >= 3 ) {
      // Set error status to sync item and product
      wp_update_post( [
        'ID' => $item->ID,
        'post_status' => 'error',
      ] );
      update_post_meta( $product_id, 'sync_state', 'error' );
      continue;
    } 
    update_post_meta( $item->ID, 'attemps', $attemps + 1 ); // Update attemps
    $image_id = insert_image_from_url( $url, $product_id );
    if ( ! $image_id ) continue;
    if ( $is_thumbnail ) {
      // Set thumbnail
      update_field( 'thumbnail', $image_id, $product_id );
    } else {
      // Add image to gallery
      $gallery = get_post_meta( $product_id, 'gallery', true );
      if ( empty( $gallery ) ) $gallery = [];
      $gallery[] = $image_id;
      update_field( 'gallery', $gallery, $product_id );
    }
    wp_update_post( [
      'ID' => $item->ID,
      'post_status' => 'done',
    ] );

    // Check related product sync is done
    $query = new WP_Query( [
      'post_type' => 'sync',
      'post_status' => 'waiting',
      'meta_query' => [ [
        'key' => 'product',
        'value' => $product_id,
      ] ],
    ] );
    if ( ! $query->found_posts ) {
      update_post_meta( $product_id, 'sync_state', 'done' );
    }
  }
});

/**
 * Enable custom part of header
 */
define( 'CUSTOM_PART_OF_HEADER', TRUE );
define( "CUSTOM_SEARCH_ACTION", TRUE );
define( "CUSTOM_SEARCH_HEADER", TRUE );