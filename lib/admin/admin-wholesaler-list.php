<?php

namespace Shoptet;

class AdminWholesalerList {


  static function init () {
    add_action( 'manage_posts_custom_column', [ get_called_class(), 'manage_column_content' ], 10, 2 );
    add_filter( 'manage_edit-custom_columns', [ get_called_class(), 'manage_columns' ] );
  }

  static function manage_columns ( $columns ) {
    $custom_columns = [];
    $custom_columns['premium'] = __( 'Prémiový zápis', 'shp-obchodiste' );
    $columns_count = count($columns);
    return (
      array_slice( $columns, 0, 2, true ) +
      $custom_columns +
      array_slice( $columns, 2, ( $columns_count - 1 ), true )
    );
  }

  static function manage_column_content ( $column, $post_id ) {

    if ( 'custom' != get_post_type($post_id) ) {
      return;
    }

    switch ( $column ) {
      case 'premium':
        $is_premium = boolval( get_post_meta( $post_id, 'is_premium', true ) );
        $date_from = intval( get_post_meta( $post_id, 'premium_date_from', true ) );
        $date_to = intval( get_post_meta( $post_id, 'premium_date_to', true ) );
        $date_format = get_option('date_format');
        $current_date = current_time( 'Ymd', 1 ); 

        if ( ! $date_from && ! $date_to ) {
          break;
        }

        $color = 'inherit';
        if ( $is_premium ) {
          if ( $current_date < $date_from ) {
            $color = '#ffb900';
            $state = __( 'Naplánováno', 'shp-obchodiste' );
          } elseif ( $current_date > $date_to ) {
            $state = __( 'Expirováno', 'shp-obchodiste' );
          } else {
            $color = '#006505';
            $state = __( 'Aktivní', 'shp-obchodiste' );
          }
        } else {
          $state = __( 'Deaktivováno', 'shp-obchodiste' );
        }

        printf(
          __( '<strong style="color:%s">%s</strong><br><small>%s – %s</small> ', 'shp-obchodiste' ),
          $color,
          $state,
          date_i18n( $date_format, strtotime($date_from), true ),
          date_i18n( $date_format, strtotime($date_to), true ),
        );
      break;
    }
  }

}

AdminWholesalerList::init();