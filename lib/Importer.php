<?php

namespace Shoptet;

class Importer {

  static function init () {
    add_action( 'importer/insert_product', [ get_called_class(), 'insertProduct' ], 10, 4 );
    add_action( 'importer/sync_product_image', [ get_called_class(), 'syncProductImage' ] );
  }

  static function enqueueProduct ( $product_arr, $related_wholesaler_id, $set_pending_status, $product_category_id ) {
    as_enqueue_async_action( 'importer/insert_product', [ $product_arr, $related_wholesaler_id, $set_pending_status, $product_category_id ] );
  }

  static function enqueueProductImageSync ( $post_sync_id ) {
    as_enqueue_async_action( 'importer/sync_product_image', [ $post_sync_id ] );
  }

  static function insertProduct ( $product_arr, $related_wholesaler_id, $set_pending_status, $product_category_id = false ) {

    $is_related_wholesaler_publish = ( 'publish' === get_post_status( $related_wholesaler_id ) );
    $wholesaler_author_id = get_post_field( 'post_author', $related_wholesaler_id );

    $meta_input = [
      'short_description' => isset( $product_arr['shortDescription'] ) ? $product_arr['shortDescription'] : '',
      'description' => isset( $product_arr['description'] ) ? $product_arr['description'] : '',
      'price' => isset( $product_arr['price'] ) ? floatval( $product_arr['price'] ) : '',
      'minimal_order' => isset( $product_arr['minimumAmount'] ) ? $product_arr['minimumAmount'] : '',
      'ean' => isset( $product_arr['ean'] ) ? $product_arr['ean'] : '',
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

    update_field( 'field_5c7d1fbf2e01c', $related_wholesaler_id, $post_product_id ); // update product related wholesaler field

    $product_category_id = ( $product_category_id ?: self::getProductCategoryID( $product_arr ) );

    if ( $product_category_id ) {
      wp_set_post_terms( $post_product_id, [ $product_category_id ], 'producttaxonomy' );
      update_field( 'field_5cc6fbe565ff6', $product_category_id, $post_product_id ); // update product category field
    }

    $image_items = [ 'image', 'image2', 'image3', 'image4', 'image5' ];
    $product_sync_state = false;
    foreach ( $image_items as $image_key ) {
      if ( empty( $product_arr[$image_key] ) ) continue;
      $postarr = [
        'post_type' => 'sync',
        'post_title' => $title . ' – ' . __( 'Obrázek produktu', 'shp-obchodiste' ),
        'post_status' => 'waiting',
        'meta_input' => [
          'product' => $post_product_id,
          'url' => $product_arr[$image_key],
          'is_thumbnail' => ( $image_key === 'image' ),
          'attemps' => 0,
        ],
      ];
      $product_sync_state = 'waiting';
      $post_sync_id = wp_insert_post( $postarr );
      self::enqueueProductImageSync( $post_sync_id );
    }

    if ( $product_sync_state ) {
      update_post_meta( $post_product_id, 'sync_state', $product_sync_state );
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

  static function syncProductImage ( $post_sync_id ) {
    $post_product_id = get_post_meta( $post_sync_id, 'product', true );
    
    // $attemps = intval( get_post_meta( $post_sync_id, 'attemps', true ) );
    // if ( $attemps >= 3 ) {
    //   // Set error status to sync item and product
    //   wp_update_post( [
    //     'ID' => $post_sync_id,
    //     'post_status' => 'error',
    //   ] );
    //   update_post_meta( $post_product_id, 'sync_state', 'error' );
    //   return;
    // } else {
    //   update_post_meta( $post_sync_id, 'attemps', $attemps + 1 );
    //   self::enqueueProductImageSync( $post_sync_id );
    // }

    $url = get_post_meta( $post_sync_id, 'url', true );
    $is_thumbnail = boolval( intval( get_post_meta( $post_sync_id, 'is_thumbnail', true ) ) );
    $image_id = insert_image_from_url( $url, $post_product_id );
    if ( ! $image_id ) return;
    if ( $is_thumbnail ) {
      // Set thumbnail
      update_field( 'thumbnail', $image_id, $post_product_id );
    } else {
      // Add image to gallery
      $gallery = get_post_meta( $post_product_id, 'gallery', true );
      if ( empty( $gallery ) ) $gallery = [];
      $gallery[] = $image_id;
      update_field( 'gallery', $gallery, $post_product_id );
    }
    wp_update_post( [
      'ID' => $post_sync_id,
      'post_status' => 'done',
    ] );

    // Check related product sync is done
    $query = new \WP_Query( [
      'post_type' => 'sync',
      'post_status' => 'waiting',
      'meta_query' => [ [
        'key' => 'product',
        'value' => $post_product_id,
      ] ],
    ] );
    if ( ! $query->found_posts ) {
      update_post_meta( $post_product_id, 'sync_state', 'done' );
    }

    error_log(sprintf('Image (%s) synced', $post_product_id));
  }

  static function getProductCategoryID ( $product_arr ) {
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