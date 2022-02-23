<?php

namespace Shoptet;

class ImporterNotifications {

  static function init() {

    add_action( 'check_finished_imports', [ get_called_class(), 'check_finished_imports' ] );
    
    add_action('init', function() {
      //do_action('check_finished_imports');
    });

    if ( ! wp_next_scheduled( 'check_finished_imports' ) ) {
      wp_schedule_event( time(), 'five_minutes', 'check_finished_imports' );
    }
  }

  static function is_completed($wholeseler_id) {
    $todo = 0;

    $todo += Importer::get_import_count( 'xml', $wholeseler_id, 'pending' );
    $todo += Importer::get_import_count( 'xml', $wholeseler_id, 'running' );
    $todo += Importer::get_import_count( 'csv', $wholeseler_id, 'pending' );
    $todo += Importer::get_import_count( 'csv', $wholeseler_id, 'running' );

    $todo += Importer::get_products_count( $wholeseler_id, 'pending' );
    $todo += Importer::get_products_count( $wholeseler_id, 'running' );

    return $todo == 0;
  }

  static function get_unfinished_wholesalers() {
    $wp_query = new \WP_Query( [
      'post_type' => 'custom',
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
      'meta_query' => [
        [
          'key' => 'importer_importing',
          'value' => 1,
        ],
      ],
    ] );
    return $wp_query->posts;
  }

  static function check_finished_imports() {
    $unfinished_wholesalers = self::get_unfinished_wholesalers();
    foreach( $unfinished_wholesalers as $wholesaler_id ) {
      if (self::is_completed($wholesaler_id)) {
        self::notify($wholesaler_id);
        self::clean($wholesaler_id);
      }
    }
  }

  static function get_mail($wholesaler_id) {
    $products_imported = intval( get_post_meta( $wholesaler_id, 'importer_products_imported', true ) );
    $products_invalid = intval( get_post_meta( $wholesaler_id, 'importer_products_invalid', true ) );

    $mail = [];

    if (!$products_invalid) {
      $mail['subject'] = 'Import úspěšně dokončen!';
      $mail['message'] = 'Děkujeme,<br><br>všechny vaše produkty byly úspěšně importovány na Obchodiště.';
    } else {
      $mail['subject'] = 'Import dokončen s chybami';
      $mail['message'] = 'Některé vaše produkty obsahují chybu a nebyly tak importovány na Obchodiště.';
    }

    if ($products_imported) {
      $mail['message'] .= '<br><br>';
      $mail['message'] .= 'Úspěšně importovanýn produktům se právě stahují obrázky a produkty čekají na schválení.';
    }

    $mail['message'] .= '<br><br>';

    $mail['message'] .= "Úspěšně importováno produktů: $products_imported<br>";
    $mail['message'] .= "Neúspěšně importováno produktů: $products_invalid";

    $mail['message'] .= '<br><br>';

    $mail['message'] .= 'S pozdravem,<br>Tým Obchodiiště';

    return $mail;
  }

  static function notify($wholesaler_id) {
    $mail = self::get_mail($wholesaler_id);
    $email_from = get_fields( 'options' )[ 'email_from' ];
    $wholesaler_contact_email = get_field( 'contact_email', $wholesaler_id, false );

    wp_mail(
      $wholesaler_contact_email,
      $mail['subject'],
      $mail['message'],
      [
        'From: ' . $email_from,
        'Content-Type: text/html; charset=UTF-8',
      ]
    );
  }

  static function clean($wholesaler_id) {
    delete_post_meta( $wholesaler_id, 'importer_products_imported' );
    delete_post_meta( $wholesaler_id, 'importer_products_invalid' );
    delete_post_meta( $wholesaler_id, 'importer_importing' );
  }

}

ImporterNotifications::init();