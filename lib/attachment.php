<?php

namespace Shoptet;

class Attachment {

  const BIG_IMAGE_SIZE_TRASHOLD = 1920;
  const JPEG_QUALITY = 80;

  public static function init() {
    add_action( 'init', [ get_called_class(), 'clean_image_sizes' ] );
    add_action( 'init', [ get_called_class(), 'add_image_sizes' ] );
    add_action( 'add_attachment', [ get_called_class(), 'handle_author' ] );
    add_action( 'edit_attachment', [ get_called_class(), 'handle_author' ] );
    add_filter( 'big_image_size_threshold', [ get_called_class(), 'get_big_image_size_threshold' ] );
    add_filter( 'jpeg_quality', [ get_called_class(), 'get_jpeg_quality' ] );
  }

  public static function clean_image_sizes() {
    $built_in_sizes = [
      'thumbnail',
      'medium',
      'medium_large',
      'large',
    ];

    // Get all registered image sizes
    $image_sizes = get_intermediate_image_sizes();
    
    // Keep only built-in image sizes
    foreach( $image_sizes as $image_size ) {
      if( !in_array( $image_size, $built_in_sizes ) ) {
        remove_image_size( $image_size );
      } 
    }
  }

  public static function add_image_sizes() {
    add_image_size( 'wholesaler-logo-thumb', 150, 150 );
  }

  public static function handle_author( $attach_id ) {
    $attach = get_post( $attach_id );
    
    if ( $attach ) {
      $parent = get_post( $attach->post_parent );
    }

    if ( empty($parent) ) {
      return;
    }

    if ( $attach->post_author != $parent->post_author ) {
      // Prevent infinite loop because wp_update_post trigger edit_attachment again
      remove_action( 'edit_attachment', [ get_called_class(), 'handle_author' ] );

      wp_update_post([
        'ID' => $attach_id,
        'post_author' => $parent->post_author,
      ]);

      add_action( 'edit_attachment', [ get_called_class(), 'handle_author' ] );
    }
  }

  public static function get_big_image_size_threshold() {
    return self::BIG_IMAGE_SIZE_TRASHOLD;
  }

  public static function get_jpeg_quality() {
    return self::JPEG_QUALITY;
  }

}

Attachment::init();