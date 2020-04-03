<?php

function check_if_external_company_exists( $external_company_token ) {
  global $wpdb;
  $result = $wpdb->get_var(
    $wpdb->prepare(
      'SELECT 1 FROM external_companies WHERE registration_token = "%s"',
      $external_company_token
    )
  );  
  return ( $result === '1' );
}

function get_external_company_value_by_field_name( $external_company_token, $field_name ) {
  global $wpdb;
  $column_names_by_field_name = [
    'id' => 'id',
    'title' => 'title',
    'street' => 'street',
    'city' => 'city',
    'zip' => 'postal_code',
    'in' => 'id_number',
    'website' => 'website',
    'country' => 'country',
    'region' => 'district',
    'contact_full_name' => 'contact_name_surname',
    'contact_email' => 'email',
    'contact_tel' => 'phone',
    'short_about' => 'description',
  ];
  $regions_by_disctrict = [
    'Hlavní město Praha' => 0,
    'Jihočeský kraj' => 1,
    'Jihomoravský kraj' => 2,
    'Karlovarský kraj' => 3,
    'Kraj Vysočina' => 4,
    'Královéhradecký kraj' => 5,
    'Liberecký kraj' => 6,
    'Moravskoslezský kraj' => 7,
    'Olomoucký kraj' => 8,
    'Pardubický kraj' => 9,
    'Plzeňský kraj' => 10,
    'Středočeský kraj' => 11,
    'Ústecký kraj' => 12,
    'Zlínský kraj' => 13,
  ];
  if ( ! array_key_exists( $field_name, $column_names_by_field_name ) ) return false;
  $column_name = $column_names_by_field_name[ $field_name ];
  $result = $wpdb->get_var(
    $wpdb->prepare(
      'SELECT ' . $column_name . ' FROM external_companies WHERE registration_token = "%s"',
      $external_company_token
    )
  );

  switch ( $field_name ) {
    case 'region':
    if ( ! array_key_exists( $result, $regions_by_disctrict ) ) return false;
    $result = $regions_by_disctrict[ $result ];
    break;
    case 'country':
    $result = 'cz';
    case 'contact_tel':
    $result = str_replace( ' ', '', $result ); // Remove spaces from phone number
    break;
  }
  return $result;
}

function get_wholesaler_by_external_company_id ( $external_company_id ) {
  $query = new WP_Query( [
    'post_type' => 'custom',
    'posts_per_page' => 1,
    'post_status' => 'any',
    'meta_query' => [
      [
        'key' => 'external_company_id',
        'value' => $external_company_id,
      ],
    ],
  ] );

  if ( empty( $query->posts ) ) return null;

  return $query->posts[0];
}

function get_wholesaler_by_approving_token ( $approving_token ) {  
  $query = new WP_Query( [
    'post_type' => 'custom',
    'posts_per_page' => 1,
    'post_status' => 'any',
    'meta_query' => [
      [
        'key' => 'approving_token',
        'value' => $approving_token,
      ],
    ],
  ] );

  if ( empty( $query->posts ) ) return null;

  return $query->posts[0];
}

function register_wholesaler ( $post_id ) {
  // Create a user from contact person data

  $user_email = get_post_meta( $post_id, 'contact_email', true );
  $user_name = get_post_meta( $post_id, 'contact_full_name', true );

  // Check for contact person duplication
  if ( username_exists( $user_email ) || email_exists( $user_email ) ) {
    wp_delete_post( $post_id, true );
    wp_die( __(
      '<strong>Kontaktní osoba s tímto e-mailem je již zaregistrována.</strong> Velkoobchod nebyl vytvořen.',
      'shp-obchodiste'
    ) );
    return;
  }

  $random_password = wp_generate_password();
  $user_id = wp_create_user( $user_email, $random_password, $user_email );

  // Set the user as post author
  wp_update_post( [ 'ID' => $post_id, 'post_author' => $user_id ] );

  $approving_token = get_post_meta( $post_id, 'approving_token', true );
  $approving_url = get_site_url( null, '?approve_company=' . $approving_token );

  // Send e-mail with approving link to user
  $options = get_fields( 'options' );
  $email_from = $options[ 'email_from' ];
  $welcome_email_subject = $options[ 'operator_welcome_email_subject' ];
  $welcome_email_body = $options[ 'operator_welcome_email_body' ];
  $to_replace = [
    '%username%' => $user_email,
    '%approving_url%' => $approving_url,
  ];
  $welcome_email_body = strtr( $welcome_email_body, $to_replace );

  wp_mail(
    $user_email,
    $welcome_email_subject,
    $welcome_email_body,
    [
      'From: ' . $email_from,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );
}

function notify_contact_person ( $post_id, $external_company_token ) {
  $options = get_fields( 'options' );
  $operator_registration_email_from = $options[ 'operator_registration_email_from' ];
  $registration_email_subject = $options[ 'operator_registration_email_subject' ];
  $registration_email_body = $options[ 'operator_registration_email_body' ];
  $registration_url = get_site_url( null, '?external_company=' . $external_company_token );
  $to_replace = [
    '%registration_url%' => $registration_url,
  ];
  $registration_email_body = strtr( $registration_email_body, $to_replace );
  $user_email = get_post_meta( $post_id, 'contact_email', true );

  // Remove post author
  wp_update_post( [ 'ID' => $post_id, 'post_author' => 0 ] );

  wp_mail(
    $user_email,
    $registration_email_subject,
    $registration_email_body,
    [
      'From: ' . $operator_registration_email_from,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );

  wp_die(
    __(
      '<strong>E-mail by odeslán.</strong> Kontaktní osobě byl odeslán e-mail s odkazem na registrační formulář.',
      'shp-obchodiste'
    ),
    __( 'E-mail odeslán', 'shp-obchodiste' ),
    [ 'response' => 200 ]
  );
}

function set_wholesaler_logo ( $post_id, $external_company_token ) {
  global $wpdb;
  $logo_url = $wpdb->get_var(
    $wpdb->prepare(
      'SELECT logo_url FROM external_companies WHERE registration_token = "%s"',
      $external_company_token
    )
  );
  if ( empty( $logo_url) ) return;
  $image_id = insert_image_from_url( $logo_url, $post_id );
  if ( ! $image_id ) return;
  update_field( 'logo', $image_id, $post_id );
}

// Check for external company token and authenticate
add_action( 'wp' , function () {
	if ( ! isset( $_GET['external_company'] ) || '' === $_GET['external_company'] ) return;
  $external_company_token = $_GET['external_company'];

  if ( ! check_if_external_company_exists( $external_company_token ) ) {
    wp_die( __( 'Neplatná URL', 'shp-obchodiste' ) );
		return;
  }

  $GLOBALS[ 'external_company' ] = [
    'title' => get_external_company_value_by_field_name( $external_company_token, 'title' ),
    'id' => get_external_company_value_by_field_name( $external_company_token, 'id' ),
  ];
  echo get_template_part( 'src/template-parts/operator/content', 'form' );
  unset( $GLOBALS[ 'external_company' ] );
  die();
} );

// Set default values to operator form
add_filter( 'acf/load_field', function ( $field ) {
  if ( ! isset( $_GET['external_company'] ) || '' === $_GET['external_company'] ) return $field;
  $external_company_token = $_GET['external_company'];

  if ( ! check_if_external_company_exists( $external_company_token ) ) return $field;
  
  if ( $value = get_external_company_value_by_field_name( $external_company_token, $field['name'] ) ) {
    $field[ 'default_value' ] = $value;
  }

  return $field;
} );

add_action( 'acf/save_post', function ( $post_id ) {
  if ( 'custom' !== get_post_type ( $post_id ) ) return;
  if ( ! isset( $_GET['external_company'] ) || '' === $_GET['external_company'] ) return;
  $external_company_token = $_GET['external_company'];
  if ( ! check_if_external_company_exists( $external_company_token ) ) return;

  if ( isset($_POST['operator_form_notify_contact_person']) ) {
    notify_contact_person( $post_id, $external_company_token );
  } else if ( isset($_POST['operator_form_register_wholesaler']) ) {
    register_wholesaler( $post_id );
    set_wholesaler_logo( $post_id, $external_company_token );
    wp_die(
      __(
        '<strong>Velkoobchod byl úspěšně registrován.</strong> Kontaktní osobě byl odeslán e-mail s potvrzovacím odkazem.',
        'shp-obchodiste'
      ),
      __( 'Velkoobchod registrován', 'shp-obchodiste' ),
      [ 'response' => 200 ]
    );
  }

} );

// Check for external company token and authenticate
add_action( 'wp' , function () {
	if (
    ! isset( $_GET['approve_company'] ) ||
    '' === $_GET['approve_company']
  ) return;
  $approving_token = $_GET['approve_company'];

  $wholesaler = get_wholesaler_by_approving_token( $approving_token );

  if ( ! $wholesaler ) {
		wp_die( __( 'Zadali jste neplatný odkaz. Zkuste to prosím znovu.', 'shp-obchodiste' ) );
		return;
  }
  
  // Approve wholesaler and set to pending status
  if ( empty ( get_post_meta( $wholesaler->ID, 'approved' ) ) ) {
    update_post_meta( $wholesaler->ID, 'approved', time() );
    wp_update_post( [
      'ID' => $wholesaler->ID,
      'post_status' => 'pending',
    ] );
  }

  // Get wholesaler author
  $user_id = $wholesaler->post_author;
  $user_data = get_userdata( $user_id );
  $user_login = $user_data->user_login;
  $key = get_password_reset_key( $user_data );

  // Generate reset password URL and redirect
  $reset_password_url = network_site_url(
    'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ),
    'login'
  );
  wp_redirect( $reset_password_url );
  exit;
} );