<?php

class ShoptetStats {

  static function init() {
    if ( ! wp_next_scheduled( 'shoptet_stats_fetch' ) ) {
      wp_schedule_event( time(), 'hourly', 'shoptet_stats_fetch' );
    }
    add_action( 'shoptet_stats_fetch', [ get_called_class(), 'fetchStats' ] );
    add_shortcode( 'shoptet-stats', [ get_called_class(), 'statsShortcode' ] );
  }

  static function getStats( $name ) {
    $stats = get_option( 'shoptet_stats', [] );

    if ( ! isset( $stats[$name] ) ) {
      return '';
    }

    return $stats[$name];
  }

  static function fetchStats() {

    // Initialize CURL
    $ch = curl_init('https://www.shoptet.cz/action/ShoptetStatisticCounts');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response
    $response = json_decode($json, true);

    // Persist exchange rates
    if ( ! is_array( $response ) ) {
      $response = [];
    }
    update_option( 'shoptet_stats', $shoptet_stats );
  }

  static function statsShortcode( $atts ) {
    $stats = '';
    if ( ! empty( $atts['name'] ) ) {
      $stats = self::getStats( $atts['name'] );
    }
    return $stats;
  }

}