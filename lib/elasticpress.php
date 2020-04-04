<?php

class ElasticPressSettings {

  static function init() {
    self::initActions();
    self::initFilters();
  }

  static function initActions() {
  }
  
  static function initFilters() {
    add_filter( 'ep_index_name', [ get_called_class(), 'setIndexName' ], 10, 3 );
    add_filter( 'ep_post_mapping', [ get_called_class(), 'addAnalysisDefaultAnalyzer' ] );
    add_filter( 'ep_post_mapping', [ get_called_class(), 'addAnalysisFilter' ] );
    add_filter( 'ep_formatted_args', [ get_called_class(), 'unlimitTrackTotalHits' ] );
    add_filter( 'ep_formatted_args', [ get_called_class(), 'fixCustumTaxQuery' ], 10, 2 );
  }

  /**
	 * Change index name
	 */
  static function setIndexName( $index_name, $blog_id, $indexable ) {
    return 'obchodiste-cz-' . $indexable->slug;
  }

  /**
	 * Change default analyzer
	 */
  static function addAnalysisDefaultAnalyzer( $mapping ) {
    if ( isset( $mapping['settings']['analysis']['analyzer'] ) ) {
      $mapping['settings']['analysis']['analyzer'] = [];
      $mapping['settings']['analysis']['analyzer']['default'] = [
        'tokenizer' => 'standard',
        'char_filter' => [
          'dot_replace',
        ],
        'filter' => [
          'czech_stop',
          'czech_stemmer',
          'lowercase',
          'czech_stop',
          'asciifolding',
          'unique_on_same_position',
        ],
      ];
    }
    return $mapping;
  }

  /**
	 * Use stemmer analysis filter instead of snowball where Czech is not included
	 */
  static function addAnalysisFilter( $mapping ) {
    if ( isset( $mapping['settings']['analysis'] ) ) {
      $mapping['settings']['analysis']['filter'] = [];
      $mapping['settings']['analysis']['filter']['czech_stemmer'] = [
        'type' => 'stemmer',
        'language' => 'czech',
      ];
      $mapping['settings']['analysis']['filter']['czech_stop'] = [
        'type' => 'stop',
        'stopwords' => [ 'že', '_czech_' ],
      ];
      $mapping['settings']['analysis']['filter']['unique_on_same_position'] = [
        'type' => 'unique',
        'only_on_same_position' => true,
      ];
      // Enable search in project title domain name without tld
      // Replace a dot followed by non-space character ("example.com" replace to "example com")
      $mapping['settings']['analysis']['char_filter']['dot_replace'] = [
        'type' => 'pattern_replace',
        'pattern' => '\\.(?=\S)',
        'replacement' => ' ',
      ];
    }
    return $mapping;
  }

  /**
	 * Fix Custom Taxonomies not included in ElasticPress query
   * via: https://github.com/10up/ElasticPress/issues/1576
	 */
  static function fixCustumTaxQuery( $formatted_args, $args ) {

    $custom_taxonomies = [
      'producttaxonomy',
      'customtaxonomy',
    ];

    // Set related post type if taxonomy is being queried
    if ( isset( $args['taxonomy'] ) && in_array( $args['taxonomy'], $custom_taxonomies ) ) {
      $related_post_type = str_replace( 'taxonomy', '', $args['taxonomy'] );
      $post_filter = $formatted_args['post_filter']['bool']['must'];
      for( $i = 0; $i < count( $post_filter ); $i++ ) {
        if ( isset( $post_filter[$i]['terms']['post_type.raw'] ) ) {
          $formatted_args['post_filter']['bool']['must'][$i]['terms']['post_type.raw'] = [ $related_post_type ];
        }
      }
    }

    // Inlcude taxonomy to query arguments
    foreach ( $custom_taxonomies as $taxonomy ) {
      if ( empty( $args[$taxonomy] ) ) continue;
      $term_slug = $args[$taxonomy];
      $term = get_term_by( 'slug', $term_slug, $taxonomy );
      $all_terms = [];
      $child_term_ids = [];

      // include child terms
      if ( isset( $term->term_id ) ) {
        $child_term_ids = get_term_children( $term->term_id, $taxonomy );
        $all_terms = array_map( function ( $term_id ) {
          return get_term( $term_id )->slug;
        }, $child_term_ids );
      }

      array_push( $all_terms, $term_slug );
      $new_formated_args = array(
        'bool' => array(
          'must' => array(
            'terms' => array(
              'terms.' . $taxonomy . '.slug' => $all_terms
            )
          )
        )
      );
      array_unshift( $formatted_args['post_filter']['bool']['must'], $new_formated_args );
    }
    
    return $formatted_args;
  }

  /**
	 * Unlimit total hits
   * via: https://www.elastic.co/guide/en/elasticsearch/reference/current/breaking-changes-7.0.html#hits-total-now-object-search-response
	 */
  static function unlimitTrackTotalHits( $formatted_args ) {
    $formatted_args['track_total_hits'] = true;
    return $formatted_args;
  }

}
