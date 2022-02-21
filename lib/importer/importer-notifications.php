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
          'key' => 'is_importing',
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
        update_post_meta( $wholesaler_id, 'is_importing', 0 );
        self::notify($wholesaler_id);
      }
    }
  }

  static function notify($wholesaler_id) {
    $options = get_fields( 'options' );
    $email_from = $options[ 'email_from' ];

    $wholesaler_contact_email = get_field( 'contact_email', $wholesaler_id, false );

    wp_mail(
      $wholesaler_contact_email,
      'Váš import na Obchodiště.cz byl dokončen!',
      'Váš import na Obchodiště.cz byl dokončen!',
      [
        'From: ' . $email_from,
        'Content-Type: text/html; charset=UTF-8',
      ]
    );
  }

}

ImporterNotifications::init();