<?php

class ElasticPressSettings {

  static function init() {
    self::initActions();
    self::initFilters();
  }

  static function initActions() {

  }
  
  static function initFilters() {
    add_filter( 'ep_post_mapping', [ get_called_class(), 'useStemmerFilter' ] );
    add_filter( 'ep_formatted_args', [ get_called_class(), 'fixCustumTaxQuery' ], 10, 2 );
  }

  /**
	 * Use stemmer analysis filter instead of snowball where Czech is not included
	 */
  static function useStemmerFilter( $mapping ) {
    if ( isset( $mapping['settings']['analysis']['filter']['ewp_snowball']['type'] ) ) {
      $mapping['settings']['analysis']['filter']['ewp_snowball']['type'] = 'stemmer';
    }
    return $mapping;
  }

  /**
	 * Fix Custom Taxonomies not included in ElasticPress query
   * via: https://github.com/10up/ElasticPress/issues/1576
	 */
  static function fixCustumTaxQuery( $formatted_args, $args ) {

    $taxes = get_taxonomies(
      array(
        'public' => true,
        '_builtin' => false
      ),
    'names' );

    // Set related post type if taxonomy is being queried
    if ( isset( $args['taxonomy'] ) && $args['taxonomy'] == 'producttaxonomy' ) {
      $post_filter = $formatted_args['post_filter']['bool']['must'];
      for( $i = 0; $i < count( $post_filter ); $i++ ) {
        if ( isset( $post_filter[$i]['terms']['post_type.raw'] ) ) {
          $formatted_args['post_filter']['bool']['must'][$i]['terms']['post_type.raw'] = ['product'];
        }
      }
    }

    // Inlcude taxonomy to query arguments
    foreach ( $taxes as $taxonomy ) {
      if ( empty( $args[$taxonomy] ) ) continue;
      $new_formated_args = array(
        'bool' => array(
          'must' => array(
            'terms' => array(
              'terms.' . $taxonomy . '.slug' => array(
                $args[$taxonomy]
              )
            )
          )
        )
      );
      array_unshift( $formatted_args['post_filter']['bool']['must'], $new_formated_args );
    }
    
    return $formatted_args;
  }

}
