<?php

namespace Shoptet;

class Importer {

  public static function init() {
    add_action( 'importer/import_xml', [ get_called_class(), 'import_xml' ], 10, 5 );
    add_action( 'importer/import_csv', [ get_called_class(), 'import_csv' ], 10, 5 );
    add_action( 'importer/insert_product', [ get_called_class(), 'insert_product' ] );
    add_action( 'importer/upload_product_image', [ get_called_class(), 'upload_product_image' ], 10, 4 );
  }

  public static function enqueue_import( $source_type, $source, $wholesaler, $default_category, $set_pending_status, $user_id ) {
    $args = [
      $source,
      $wholesaler,
      $default_category,
      $set_pending_status,
      $user_id
    ];
    as_enqueue_async_action(
      'importer/import_' . $source_type,
      $args,
      'importer_import_' . $source_type . '_' . $wholesaler
    );
    update_post_meta( $wholesaler, 'importer_importing', 1 );
  }

  public static function enqueue_product( ImporterProduct $product ) {
    
    $product_array = $product->to_array();
    $args_id = ImporterStore::insert($product_array);

    $action_id = as_enqueue_async_action(
      'importer/insert_product',
      [ $args_id ],
      'importer_insert_product_' . $product->get_wholesaler()
    );

    ImporterStore::update_action_id( $args_id, $action_id );
  }

  public static function enqueue_product_image_upload( $post_product_id, $image_url, $is_thumbnail, $attemps ) {
    as_enqueue_async_action(
      'importer/upload_product_image',
      [ $post_product_id, $image_url, $is_thumbnail, $attemps ],
      'importer_upload_product_image_' . $post_product_id
    );
  }

  public static function get_import_count( $source_type, $related_wholesaler_id = NULL, $status = NULL ) {
    $args = [
      'hook' => 'importer/import_' . $source_type, 
      'per_page' => -1,
    ];
    if ( $related_wholesaler_id ) {
      $args['group'] = 'importer_import_'. $source_type . '_' . $related_wholesaler_id;
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

  public static function get_products_count( $related_wholesaler_id = NULL, $status = NULL ) {
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

  public static function get_product_images_count( $post_product_id = NULL, $status = NULL ) {
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

  public static function import_xml( $xml_feed_url, $wholesaler, $default_category, $set_pending_status, $user_id ) {
    (new ImporterParserXML(
      $xml_feed_url,
      $wholesaler,
      $default_category,
      $set_pending_status,
      $user_id
    ))->import();
  }

  public static function import_csv( $file_path, $wholesaler, $default_category, $set_pending_status, $user_id ) {
    (new ImporterParserCSV(
      $file_path,
      $wholesaler,
      $default_category,
      $set_pending_status,
      $user_id
    ))->import();
  }

  public static function insert_product( $args_id ) {

    $args = ImporterStore::get($args_id);
    $product = new ImporterProduct($args);

    $is_related_wholesaler_publish = ( 'publish' === get_post_status( $product->get_wholesaler() ) );
    $wholesaler_author_id = get_post_field( 'post_author', $product->get_wholesaler() );

    $meta_input = [
      'related_wholesaler' => $product->get_wholesaler(),
      '_related_wholesaler' => 'field_5c7d1fbf2e01c',
    ];

    if ( ! empty($product->get_short_description()) )
      $meta_input['short_description'] = $product->get_short_description();
    if ( ! empty($product->get_description()) )
      $meta_input['description'] = $product->get_description();
    if ( ! empty($product->get_ean()) )
      $meta_input['ean'] = $product->get_ean();
    if ( ! empty($product->get_code()) )
      $meta_input['code'] = $product->get_code();
    if ( ! empty( $product->get_price() ) )
      $meta_input['price'] = $product->get_price();
    if ( ! empty( $product->get_vat_included() ) )
      $meta_input['vat_included'] = $product->get_vat_included();
    if ( ! empty( $product->get_minimal_order() ) )
      $meta_input['minimal_order'] = $product->get_minimal_order();
    
    $title = $product->get_name();
    $title = apply_filters( 'product_title_import', $title );

    $postarr = [
      'post_type' => 'product',
      'post_title' => $title,
      'post_author' => $wholesaler_author_id, // Set correct author id
      'post_status' => 'draft',
      'meta_input' => $meta_input,
    ];
    $post_product_id = wp_insert_post( $postarr );

    $category = $product->get_category();

    if ( $category ) {
      wp_set_post_terms( $post_product_id, [ $category ], 'producttaxonomy' );
      update_field( 'field_5cc6fbe565ff6', $category, $post_product_id ); // update product category field
    }

    $is_thumbnail = true;
    foreach ( $product->get_images() as $image_url ) {
      self::enqueue_product_image_upload( $post_product_id, $image_url, $is_thumbnail, 1 );
      $is_thumbnail = false;
    }

    // Set to pending status
    if (
      $product->get_pending_status() &&
      $category &&
      $is_related_wholesaler_publish &&
      ! empty( $product->get_images() ) &&
      ! empty( $product->get_short_description() ) &&
      ! empty( $product->get_description() )
    ) {
      wp_update_post( [
        'ID' => $post_product_id,
        'post_status' => 'pending',
      ] );
    }

    $products_imported = intval( get_post_meta( $product->get_wholesaler(), 'importer_products_imported', true ) );
    update_post_meta( $product->get_wholesaler(), 'importer_products_imported', $products_imported+1 );

    error_log(sprintf('Product (%s) inserted', $post_product_id));
  }

  public static function upload_product_image( $post_product_id, $image_url, $is_thumbnail, $attemps ) {
    
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
        self::enqueue_product_image_upload( $post_product_id, $image_url, $is_thumbnail, $attemps + 1 );
      } else {
        $errors = intval( get_post_meta( $post_product_id, 'sync_errors', true ) );
        update_post_meta( $post_product_id, 'sync_errors', $errors + 1 );
      }
      error_log(sprintf('Image (%s) failed', $post_product_id));
    }

  }

}

Importer::init();