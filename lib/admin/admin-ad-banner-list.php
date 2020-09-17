<?php

namespace Shoptet;

class AdminAdBannerList {


  static function init () {
    add_action( 'manage_posts_custom_column', [ get_called_class(), 'manage_column_content' ], 10, 2 );
    add_filter( 'display_post_states', [ get_called_class(), 'display_post_states' ], 10, 2 );
    add_filter( 'manage_edit-ad_banner_columns', [ get_called_class(), 'manage_columns' ] );
  }

  static function manage_columns ( $columns ) {
    $custom_columns = [];
    $custom_columns['date_from'] = __( 'Od', 'shp-obchodiste' );
    $custom_columns['date_to'] = __( 'Do', 'shp-obchodiste' );
    $custom_columns['target_url'] = __( 'Cílová URL', 'shp-obchodiste' );
    $columns_count = count($columns);
    return (
      array_slice( $columns, 0, 2, true ) +
      $custom_columns +
      array_slice( $columns, 2, ( $columns_count - 1 ), true )
    );
  }

  static function display_post_states ( $states, $post ) {

    if ( 'ad_banner' != $post->post_type ) {
      return $states;
    }

    $is_disabled = get_post_meta( $post->ID, 'is_disabled', true );

    if ( boolval($is_disabled) ) {
      $states[] = __( 'Deaktivováno', 'wavee-theme' );
    }

    return $states;
  }

  static function manage_column_content ( $column, $post_id ) {

    if ( 'ad_banner' != get_post_type($post_id) ) {
      return;
    }

    switch ( $column ) {
      case 'date_from':
      case 'date_to':
        $date_from = get_post_meta( $post_id, $column, true );
        if ( !$date_from ) {
          echo '–';
          break;
        }
        $date_format = get_option('date_format');
        echo date_i18n( $date_format, strtotime($date_from), true );
      break;
      case 'target_url':
        $target_url = get_post_meta( $post_id, $column, true );
        if ( !$target_url ) {
          echo '–';
          break;
        }
        printf(
          '%s <a href="%s" target="_blank">%s</a>',
          truncate( $target_url, 50 ),
          $target_url,
          __( 'link', 'shp-obchodiste' )
        );
      break;
    }
  }

}

AdminAdBannerList::init();