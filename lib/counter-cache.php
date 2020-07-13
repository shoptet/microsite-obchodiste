<?php

class CounterCache {

  static function init() {
    self::scheduleEvents();
    add_action( 'update_all_taxonomies_count', [ get_called_class(), 'updateAllTaxonomiesCount' ] );
    add_action( 'update_all_post_types_count', [ get_called_class(), 'updateAllPostTypesCount' ] );
  }

  static function scheduleEvents() {
    if ( ! wp_next_scheduled( 'update_all_taxonomies_count' ) ) {
      wp_schedule_event( time(), 'hourly', 'update_all_taxonomies_count' );
    }
    if ( ! wp_next_scheduled( 'update_all_post_types_count' ) ) {
      wp_schedule_event( time(), 'hourly', 'update_all_post_types_count' );
    }
  }

  static function getTermCount( $term_id ) {
    return get_term_meta( $term_id, 'count_include_children', true );
  }

  static function getPostTypeCount( $post_type ) {
    return intval( get_option( 'post_type_count_' . $post_type , 0 ) );
  }

  static function updateTermCount( $post_type, $term_id, $taxonomy ) {
    $query = new WP_Query( [
      'post_type' => $post_type,
      'post_status' => 'publish',
      'fields' => 'ids',
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'tax_query' => [
        [
          'taxonomy' => $taxonomy,
          'terms' => $term_id,
          'include_children' => true,
        ],
      ],
    ] );
    $count = $query->found_posts;
    update_term_meta( $term_id, 'count_include_children', $count );
  }
  
  static function updateTaxonomyCount( $post_type, $taxonomy ) {
    $terms = get_terms( [
      'taxonomy' => $taxonomy,
      'hide_empty' => true,
      'hierarchical_force' => true,
      'fields' => 'ids',
    ] );
    foreach( $terms as $term_id ) {
      self::updateTermCount( $post_type, $term_id, $taxonomy );
    }
  }

  static function updateAllTaxonomiesCount() {
    self::updateTaxonomyCount( 'custom', 'customtaxonomy' );
    self::updateTaxonomyCount( 'product', 'producttaxonomy' );
  }
  
  static function updatePostTypeCount( $post_type ) {
    $post_count = wp_count_posts( $post_type )->publish;
    update_option( 'post_type_count_' . $post_type , $post_count );
  }
  
  static function updateAllPostTypesCount() {
    self::updatePostTypeCount( 'product' );
    self::updatePostTypeCount( 'custom' );
    self::updatePostTypeCount( 'wholesaler_message' );
  }

}