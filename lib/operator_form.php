<?php

// TODO: replace id to token
function is_external_company_exist( $external_company_token ) {
  global $wpdb;
  $result = $wpdb->get_var('
    SELECT 1 FROM external_companies WHERE registration_token = "' . $external_company_token . '"
  ');
  return ( $result === '1' );
}

function get_external_company_value_by_field_name( $external_company_token, $field_name ) {
  global $wpdb;
  $column_names_by_field_name = [
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
    'about_company' => 'description',
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
  $result = $wpdb->get_var('
    SELECT ' . $column_name . ' FROM external_companies WHERE registration_token = "' . $external_company_token . '"
  ');

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

// Check for external company token and authenticate
add_action( 'wp' , function () {
	if ( ! isset( $_GET['external_company'] ) || '' === $_GET['external_company'] ) return;
  $external_company_token = $_GET['external_company'];

  if ( ! is_external_company_exist( $external_company_token ) ) {
    wp_die( __( 'Neplatná URL', 'shp-partneri' ) );
		return;
  }

  $updated = ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] );

  if ( $updated ) {
    wp_die(
			__( 'Velkoobchod byl úspěšně registrován!', 'shp-partneri' ),
			__( 'Velkoobchod byl úspěšně registrován!', 'shp-partneri' ),
			[ 'response' => 200 ]
		);
		return;
  }
  $GLOBALS[ 'external_company_title' ] = get_external_company_value_by_field_name( $external_company_token, 'title' );
  echo get_template_part( 'src/template-parts/operator/content', 'form' );
  unset( $GLOBALS[ 'external_company_title' ] );
  die();
} );

// Set default values to operator form
add_filter('acf/load_field', function ( $field ) {
  if ( ! isset( $_GET['external_company'] ) || '' === $_GET['external_company'] ) return $field;
  $external_company_token = $_GET['external_company'];

  if ( ! is_external_company_exist( $external_company_token ) ) return $field;

  if ( $value = get_external_company_value_by_field_name( $external_company_token, $field['name'] ) ) {
    $field[ 'default_value' ] = $value;
  }

  return $field;
} );