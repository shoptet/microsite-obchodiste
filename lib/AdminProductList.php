<?php

namespace Shoptet;

class AdminProductList {

  const PRODUCT_CATEGORY_ACF_KEY = 'field_5cc6fbe565ff6';

  private static $isBulkEditRendered = false;

  static function init () {
    add_action( 'admin_notices', [ get_called_class(), 'renderPendingProductsNotices' ] );
    add_action( 'admin_notices', [ get_called_class(), 'renderPendingProductImagesNotices' ] );
    add_action( 'manage_posts_custom_column', [ get_called_class(), 'manageColumnContent' ], 10, 2 );
    add_action( 'load-edit.php', [ get_called_class(), 'acfFormHead' ] );
    add_action( 'load-edit.php', [ get_called_class(), 'handleBulkEdit' ] );
    add_action( 'wp_ajax_inline-save', [ get_called_class(), 'handleQuickEdit' ], 0 ); // Before WordPress
    add_action( 'bulk_edit_custom_box', [ get_called_class(), 'renderBulkEdit' ], 10, 2 );

    add_filter( 'manage_edit-product_columns', [ get_called_class(), 'manageColumns' ] );
    add_filter( 'acf/load_field/key=' . self::PRODUCT_CATEGORY_ACF_KEY, [ get_called_class(), 'loadBulkEditCategoryField' ] );
  }

  static function renderPendingProductsNotices () {
    if ( !self::isAdminProductList() ) return;

    global $current_user;
    wp_get_current_user();

    if ( user_can( $current_user, 'subscriber' ) ) {
      if ( $related_wholesaler = get_user_wholesaler( $current_user ) ) {
        $related_wholesaler_id = $related_wholesaler->ID;
      } else {
        return;
      }
    } else {
      $related_wholesaler_id = NULL;
    }

    $pending_products_count = Importer::get_products_count( $related_wholesaler_id, 'pending' );
    $pending_products_count += Importer::get_products_count( $related_wholesaler_id, 'running' );

    if ( $pending_products_count > 0 ) : ?>
      <div class="notice notice-warning">
        <p>
          <?php
          printf(
            __( '<strong>%d produktů</strong> ve frontě čeká na vytvoření&hellip;', 'shp-obchodiste' ),
            $pending_products_count
          );
          ?>
        </p>
      </div>
    <?php endif;
  }

  static function renderPendingProductImagesNotices () {
    if ( !self::isAdminProductList() ) return;

    global $current_user;
    wp_get_current_user();

    if ( user_can( $current_user, 'subscriber' ) ) {
      return;
    }

    $pending_product_images_count = Importer::get_product_images_count( NULL, 'pending' );
    $pending_product_images_count += Importer::get_product_images_count( NULL, 'running' );

    if ( $pending_product_images_count > 0 ) : ?>
      <div class="notice notice-warning">
        <p>
          <?php
          printf(
            __( '<strong>%d obrázků</strong> ve frontě čeká na stažení&hellip;', 'shp-obchodiste' ),
            $pending_product_images_count
          );
          ?>
        </p>
      </div>
    <?php endif;
  }

  static function manageColumns ( $columns ) {
    global $current_user;
    wp_get_current_user();

    $custom_columns = [];
    if ( !user_can( $current_user, 'subscriber' ) ) {
      $custom_columns['related_wholesaler'] = __( 'Velkoobchod', 'shp-obchodiste' );
    }
    $custom_columns['sync_state'] = __( 'Stav synchronizace', 'shp-obchodiste' );
    return (
      array_slice( $columns, 0, 3, true ) +
      $custom_columns +
      array_slice( $columns, 3, 4, true )
    );
  }

  static function manageColumnContent ( $column, $post_id ) {
    switch ( $column ) {
      case 'related_wholesaler':
        if ( $related_wholesaler = get_field( 'related_wholesaler', $post_id ) ) {
          echo '<a href="' . get_permalink( $related_wholesaler ) . '">';
          echo esc_html( get_the_title( $related_wholesaler ) );
          echo '</a>';
        } else {
          echo '<em>' . __( 'Bez velkoobchodu', 'shp-obchodiste' ) . '</em>';
        }
      break;
      case 'sync_state':
        $all_product_images_count = Importer::get_product_images_count( $post_id );
        if ( $all_product_images_count == 0 ) {
          echo '–';
          break;
        }
        $pending_product_images_count = Importer::get_product_images_count( $post_id, 'pending' );
        $pending_product_images_count += Importer::get_product_images_count( $post_id, 'running' );
        if ( $pending_product_images_count == 0 ) {
          echo '<strong>' . __( 'Synchronizace obrázků dokončena', 'shp-obchodiste' ) . '</strong>';
        } else {
          echo '<strong style="color:#ffb900"><em>' . __( 'Čeká na stažení obrázků...', 'shp-obchodiste' )  . '</em></strong>';
          echo '<br><small>' . sprintf( __( 'Zbývá: <strong>%d</strong>', 'shp-obchodiste' ), $pending_product_images_count ) . '</small>';
        }
        $success = intval( get_post_meta( $post_id, 'sync_success', true ) );
        echo '<br><small>' . sprintf( __( 'Dokončeno: <strong>%d</strong>', 'shp-obchodiste' ), $success ) . '</small>';
        if ( $errors = intval( get_post_meta( $post_id, 'sync_errors', true ) ) ) {
          echo '<br><small style="color:#ff0000" title="' . __( 'Počet obrázků, které se nepodařilo stáhnout', 'shp-obchodiste' ) . '">' . sprintf( __( 'Počet chyb: <strong>%d</strong>', 'shp-obchodiste' ), $errors ) . '</small>';
        }
      break;
    }
  }

  static function isAdminProductList () {
    global $pagenow;
    $post_type = ( isset( $_GET['post_type'] ) ? $_GET['post_type'] : '' );
    return (
      is_admin() &&
      'edit.php' == $pagenow &&
      'product' == $post_type
    );
  }

  static function acfFormHead () {
    if ( self::isAdminProductList() ) {
      acf_enqueue_scripts();
    }
  }

  static function handleBulkEdit () {
    if ( ! self::isAdminProductList() ) return;
    if ( ! isset($_GET['bulk_edit']) ) return;

    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    if ( user_can( $current_user, 'subscriber' ) ) return;

    if ( is_array($_GET['post']) ) {
      $post_ids = array_map('intval', $_GET['post']);
    }

    if ( empty($post_ids) ) {
      return;
    }

    if ( isset($_GET['acf'][self::PRODUCT_CATEGORY_ACF_KEY]) ) {
      $term_id = intval($_GET['acf'][self::PRODUCT_CATEGORY_ACF_KEY]);
    }
    
    if ( ! isset($term_id) || ! term_exists($term_id, 'producttaxonomy') ) {
      $term_id = false;
    }

    $wholesaler_ids = [];
    foreach ( $post_ids as $post_id ) {
      if ( $term_id ) {
        update_field( self::PRODUCT_CATEGORY_ACF_KEY, $term_id, $post_id );
      }
      if ( $related_wholesaler_id = get_post_meta( $post_id, 'related_wholesaler', true ) ) {
        $wholesaler_ids[] = intval($related_wholesaler_id);
      }
    }

    $wholesaler_ids = array_unique( $wholesaler_ids );
    foreach ( $wholesaler_ids as $wholesaler_id ) {
      TermSyncer::enqueueWholesaler( $wholesaler_id );
    }

  }

  static function handleQuickEdit () {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    if ( ! user_can( $current_user, 'administrator' ) ) return;
    
    if ( ! isset($_POST['post_ID']) ) {
      return;
    }

    $post_id = intval($_POST['post_ID']);

    if ( $related_wholesaler_id = get_post_meta( $post_id, 'related_wholesaler', true ) ) {
      $related_wholesaler_id = intval($related_wholesaler_id);
      TermSyncer::enqueueWholesaler( $related_wholesaler_id );
    }
  }

  static function renderBulkEdit ( $column_name, $post_type ) {
    if (
      self::$isBulkEditRendered ||
      'product' != $post_type
    ) return;

    // Render product category field
    $field = acf_get_field(self::PRODUCT_CATEGORY_ACF_KEY);
    acf_render_field_wrap( $field );

    self::$isBulkEditRendered = true;
  }

  static function loadBulkEditCategoryField ( $field ) {
    if ( self::isAdminProductList() ) {
      $field['required'] = false;
      $field['instructions'] = false;
    }
    return $field;
  }

}

AdminProductList::init();