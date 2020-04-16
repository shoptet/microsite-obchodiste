<?php

namespace Shoptet;

class Importer {

  public static function init() {
    add_action( 'importer/insert_product', [ get_called_class(), 'insertProduct' ] );
    add_action( 'importer/upload_product_image', [ get_called_class(), 'uploadProductImage' ], 10, 4 );
  }

  public static function enqueueProduct( $product_arr, $related_wholesaler_id, $set_pending_status, $product_category_id ) {
    
    $args = [ $product_arr, $related_wholesaler_id, $set_pending_status, $product_category_id ];
    $args_id = ImporterStore::insert($args);

    $action_id = as_enqueue_async_action(
      'importer/insert_product',
      [ $args_id ],
      'importer_insert_product_' . $related_wholesaler_id
    );

    ImporterStore::update_action_id( $args_id, $action_id );
  }

  public static function enqueueProductImageUpload( $post_product_id, $image_url, $is_thumbnail, $attemps ) {
    as_enqueue_async_action(
      'importer/upload_product_image',
      [ $post_product_id, $image_url, $is_thumbnail, $attemps ],
      'importer_upload_product_image_' . $post_product_id
    );
  }

  public static function getProductsCount( $related_wholesaler_id = NULL, $status = NULL ) {
    $args = [
      'hook' => 'importer/insert_product',
      'per_page' => -1,
    ];
    if ( $related_wholesaler_id ) {
      $args['group'] = 'importer_insert_product_' . $related_wholesaler_id;
    }
    switch ( $status ) {
      case 'pending';
        $args['status'] = \ActionScheduler_Store::STATUS_PENDING;
      break;
      case 'running';
        $args['status'] = \ActionScheduler_Store::STATUS_RUNNING;
      break;
      case 'complete';
        $args['status'] = \ActionScheduler_Store::STATUS_COMPLETE;
      break;
    }
    $actions = as_get_scheduled_actions( $args, 'ids' );
    return count( $actions );
  }

  public static function getProductImagesCount( $post_product_id = NULL, $status = NULL ) {
    $args = [
      'per_page' => -1,
      'hook' => 'importer/upload_product_image',
    ];
    if ( $post_product_id ) {
      $args['group'] = 'importer_upload_product_image_' . $post_product_id;
    }
    switch ( $status ) {
      case 'pending';
        $args['status'] = \ActionScheduler_Store::STATUS_PENDING;
      break;
      case 'running';
        $args['status'] = \ActionScheduler_Store::STATUS_RUNNING;
      break;
      case 'complete';
        $args['status'] = \ActionScheduler_Store::STATUS_COMPLETE;
      break;
    }
    $actions = as_get_scheduled_actions( $args, 'ids' );
    return count( $actions );
  }

  public static function insertProduct( $args_id ) {

    $args = ImporterStore::get($args_id);
    list( $product_arr, $related_wholesaler_id, $set_pending_status, $product_category_id ) = $args;

    $is_related_wholesaler_publish = ( 'publish' === get_post_status( $related_wholesaler_id ) );
    $wholesaler_author_id = get_post_field( 'post_author', $related_wholesaler_id );

    $meta_input = [
      'short_description' => isset( $product_arr['shortDescription'] ) ? $product_arr['shortDescription'] : '',
      'description' => isset( $product_arr['description'] ) ? $product_arr['description'] : '',
      'price' => isset( $product_arr['price'] ) ? floatval( $product_arr['price'] ) : '',
      'minimal_order' => isset( $product_arr['minimumAmount'] ) ? $product_arr['minimumAmount'] : '',
      'ean' => isset( $product_arr['ean'] ) ? $product_arr['ean'] : '',
      'related_wholesaler' => $related_wholesaler_id,
      '_related_wholesaler' => 'field_5c7d1fbf2e01c',
    ];

    $title = $product_arr['name'];
    $title = apply_filters( 'product_title_import', $title );

    $postarr = [
      'post_type' => 'product',
      'post_title' => $title,
      'post_author' => $wholesaler_author_id, // Set correct author id
      'post_status' => 'draft',
      'meta_input' => $meta_input,
    ];
    $post_product_id = wp_insert_post( $postarr );

    $product_category_id = ( $product_category_id ?: self::getProductCategoryID( $product_arr ) );

    if ( $product_category_id ) {
      wp_set_post_terms( $post_product_id, [ $product_category_id ], 'producttaxonomy' );
      update_field( 'field_5cc6fbe565ff6', $product_category_id, $post_product_id ); // update product category field
    }

    $image_items = [ 'image', 'image2', 'image3', 'image4', 'image5' ];
    foreach ( $image_items as $image_key ) {
      if ( empty( $product_arr[$image_key] ) ) continue;
      $image_url = $product_arr[$image_key];
      $is_thumbnail = ( 'image' == $image_key );
      self::enqueueProductImageUpload( $post_product_id, $image_url, $is_thumbnail, 1 );
    }

    // Set to pending status
    if (
      $set_pending_status &&
      $product_category_id &&
      $is_related_wholesaler_publish &&
      ! empty( $product_arr['image'] ) &&
      ! empty( $product_arr['shortDescription'] ) &&
      ! empty( $product_arr['description'] )
    ) {
      wp_update_post( [
        'ID' => $post_product_id,
        'post_status' => 'pending',
      ] );
    }

    error_log(sprintf('Product (%s) inserted', $post_product_id));
  }

  public static function uploadProductImage( $post_product_id, $image_url, $is_thumbnail, $attemps ) {
    
    $image_id = insert_image_from_url( $image_url, $post_product_id );

    if ( $image_id ) {
      if ( $is_thumbnail ) {
        update_field( 'thumbnail', $image_id, $post_product_id );
      } else {
        // Add image to gallery
        $gallery = get_post_meta( $post_product_id, 'gallery', true );
        if ( empty( $gallery ) ) $gallery = [];
        $gallery[] = $image_id;
        update_field( 'gallery', $gallery, $post_product_id );
      }
      $success = intval( get_post_meta( $post_product_id, 'sync_success', true ) );
      update_post_meta( $post_product_id, 'sync_success', $success + 1 );
      error_log(sprintf('Image (%s) uploaded', $post_product_id));
    } else {
      if ( $attemps < 2 ) {
        self::enqueueProductImageUpload( $post_product_id, $image_url, $is_thumbnail, $attemps + 1 );
      } else {
        $errors = intval( get_post_meta( $post_product_id, 'sync_errors', true ) );
        update_post_meta( $post_product_id, 'sync_errors', $errors + 1 );
      }
      error_log(sprintf('Image (%s) failed', $post_product_id));
    }

  }

  public static function getProductCategoryID( $product_arr ) {
    $product_category_id = false;

    if ( ! empty( $product_arr['googleCategoryId'] ) ) {
      $term_query = new \WP_Term_Query( [
        'taxonomy' => 'producttaxonomy',
        'fields' => 'ids',
        'hide_empty' => false,
        'meta_key' => 'shoptet_category_id',
        'meta_value' => $product_arr['googleCategoryId'],
      ] );
      if ( ! empty($term_query->terms) ) {
        $product_category_id = $term_query->terms[0];
      }
    } else if ( ! empty( $product_arr['category'] ) ) {
      $category_id = intval( $product_arr['category'] );
      if ( term_exists( $category_id, 'producttaxonomy' ) ) {
        $product_category_id = $category_id;
      }
    }

    return $product_category_id;
  }

}

Importer::init();