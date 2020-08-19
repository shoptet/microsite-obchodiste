<?php

namespace Shoptet;

class TermSyncer {

  static function init() {
    add_action( 'acf/save_post', [ get_called_class(), 'handlePostSave' ] );
    add_action( 'term_syncer/sync_wholesaler', [ get_called_class(), 'syncWholesalerTerms' ] );
  }

  public static function enqueueWholesaler ( $post_id ) {
    as_enqueue_async_action( 'term_syncer/sync_wholesaler', [ $post_id ], 'term_syncer_sync_wholesaler_' . $post_id );
  }

  static function handlePostSave ( $post_id ) {
    $post_type = get_post_type($post_id);
    switch ( $post_type ) {
      case 'custom':
        self::enqueueWholesaler($post_id);
      break;
      case 'product':
        if ( $related_wholesaler_id = get_post_meta( $post_id, 'related_wholesaler', true ) ) {
          self::enqueueWholesaler($related_wholesaler_id);
        }
      break;
    }
  }

  static function getAllRelatedProducts ( $wholesaler_id ) {
    $query = new \WP_Query( [
      'post_type' => 'product',
      'post_status' => 'publish',
      'fields' => 'ids',
      'posts_per_page' => -1,
      'no_found_rows' => true,
      'ep_integrate' => true,
      'meta_query' => [ 
        [
        'key' => 'related_wholesaler',
        'value' => $wholesaler_id,
        ],
      ],
    ] );
    $products = [];
    if ( is_array( $query->posts ) ) {
      $products = $query->posts;
    }
    return $products;
  }

  static function getAllProductsTerms ( $products, $wholesaler_id ) {
    $product_term_slugs = [];
    $i = 0;
    foreach( $products as $product_id ) {
      $query = new \WP_Term_Query( [
        'taxonomy' => 'producttaxonomy',
        'fields' => 'slugs',
        'object_ids' => intval($product_id),
        'update_term_meta_cache' => false,
      ] );
      if ( ! is_array( $query->terms ) || empty( $query->terms ) ) {
        error_log( sprintf( '%d: Product (%d) has no terms', $wholesaler_id, $product_id ) );
      } else {
        $product_term_slugs = array_unique( array_merge( $product_term_slugs, $query->terms ) );
      }
      if ( ($i % 100) == 0 ) {
        stop_the_insanity();
      }
      $i++;
    }
    return $product_term_slugs;
  }

  static function getWholesalerRelatedTerms ( $product_term_slugs, $wholesaler_id ) {
    $related_term_ids = [];
    foreach( $product_term_slugs as $product_term_slug ) {
      $query = new \WP_Term_Query( [
        'taxonomy' => 'customtaxonomy',
        'fields' => 'ids',
        'slug' => $product_term_slug,
        'hide_empty' => false,
        'update_term_meta_cache' => false,
      ] );
      if ( ! is_array( $query->terms ) || empty( $query->terms )  ) {
        error_log( sprintf( '%d: No related term (%s) found ', $wholesaler_id, $product_term_slug ) );
      } else {
        $related_term_ids = array_unique( array_merge( $related_term_ids, $query->terms ) );
      }
    }
    return $related_term_ids;
  }

  static function getWholesalerProductTerms ( $wholesaler_id ) {
    $products = self::getAllRelatedProducts( $wholesaler_id );
    $product_term_slugs = self::getAllProductsTerms( $products, $wholesaler_id );
    $related_term_ids = self::getWholesalerRelatedTerms( $product_term_slugs, $wholesaler_id );
    return $related_term_ids;
  }

  static function syncWholesalerTerms ( $post_id ) {
    if ( ! get_post( $post_id ) || 'custom' != get_post_type( $post_id )  ) {
      return;
    }

    $term_ids = self::getWholesalerProductTerms( $post_id );

    // Add origin wholesaler terms
    $origin_term_meta_names = [ 'category', 'minor_category_1', 'minor_category_2' ];
    foreach ( $origin_term_meta_names as $meta ) {
      if ( $term_id = get_post_meta( $post_id, $meta, true ) ) {
        $term_ids[] = intval( $term_id );
      }
    }
    $term_ids = array_unique( $term_ids );

    // Remove old term relationships and set new ones
    wp_set_object_terms( $post_id, $term_ids, 'customtaxonomy' );
  }

  static function syncAllWholesalersTerms () {
    $query = new \WP_Query( [
      'post_type' => 'custom',
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ] );
    for ( $i = 0, $len = count( $query->posts ); $i < $len; $i++ ) {
      self::syncWholesalerTerms( $query->posts[$i] );
    }
  }

}

TermSyncer::init();