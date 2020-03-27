<?php

namespace Shoptet;

class AdminProductList {

  const PRODUCT_CATEGORY_ACF_KEY = 'field_5cc6fbe565ff6';

  private static $isBulkEditRendered = false;

  static function init () {
    add_action( 'load-edit.php', [ get_called_class(), 'acfFormHead' ] );
    add_action( 'load-edit.php', [ get_called_class(), 'handleBulkEdit' ] );
    add_action( 'bulk_edit_custom_box', [ get_called_class(), 'renderBulkEdit' ], 10, 2 );
    add_filter( 'acf/load_field/key=' . self::PRODUCT_CATEGORY_ACF_KEY, [ get_called_class(), 'loadBulkEditCategoryField' ] );
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

    
    if ( isset($_GET['acf'][self::PRODUCT_CATEGORY_ACF_KEY]) ) {
      $term_id = intval($_GET['acf'][self::PRODUCT_CATEGORY_ACF_KEY]);
    }
    
    if ( ! isset($term_id) || ! term_exists($term_id, 'producttaxonomy') ) {
      return;
    }

    if ( is_array($_GET['post']) ) {
      $post_ids = array_map('intval', $_GET['post']);
    }

    if ( empty($post_ids) ) {
      return;
    }

    foreach ( $post_ids as $post_id ) {
      update_field( self::PRODUCT_CATEGORY_ACF_KEY, $term_id, $post_id );
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