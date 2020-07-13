<?php

namespace Shoptet;

class IdentificationNumberApiAjax {

  const NONCE_ACTION = 'identification_number_api';

  static function init() {
    add_action( 'wp_ajax_identification_number_api', [ get_called_class(), 'handle_identification_number_api' ] );
  }

  static function handle_identification_number_api() {
    $data = $_POST;

    // check the nonce
    if ( check_ajax_referer( self::NONCE_ACTION, false, false ) == false ) {
      wp_send_json_error();
    }

    // check the country
    if ( empty($data['country']) || 'cz' != $data['country'] ) {
      wp_send_json_error();
    }

    // check the in
    if ( empty($data['in']) ) {
      wp_send_json_error();
    }
    
    $in_api = new IdentificationNumberApiCz($data['in']);
    if ( ! $in_api->is_valid() ) {
      wp_send_json_error( __( 'Zadejte IČO ve správném formátu', 'shp-obchodiste' ) );
    }
    $company = $in_api->get_company();

    if ( $company != false ) {
      wp_send_json_success( $company );
    } else {
      wp_send_json_error( __( 'Překontrolujte prosím IČO', 'shp-obchodiste' ) );
    }
  }

}

IdentificationNumberApiAjax::init();