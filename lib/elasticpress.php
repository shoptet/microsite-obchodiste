<?php

class ElasticPressSettings {

  static function init() {
    add_filter( 'ep_index_name', [ get_called_class(), 'set_index_name' ], 10, 3 );
    add_filter( 'ep_post_mapping', [ get_called_class(), 'add_analysis_default_analyzer' ] );
    add_filter( 'ep_post_mapping', [ get_called_class(), 'add_analysis_filter' ] );
    add_filter( 'ep_formatted_args', [ get_called_class(), 'unlimit_track_total_hits' ] );
  }

  /**
	 * Change index name
	 */
  static function set_index_name( $index_name, $blog_id, $indexable ) {
    return 'obchodiste-cz-' . $indexable->slug;
  }

  /**
	 * Change default analyzer
	 */
  static function add_analysis_default_analyzer( $mapping ) {
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
  static function add_analysis_filter( $mapping ) {
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
	 * Unlimit total hits
   * via: https://www.elastic.co/guide/en/elasticsearch/reference/current/breaking-changes-7.0.html#hits-total-now-object-search-response
	 */
  static function unlimit_track_total_hits( $formatted_args ) {
    $formatted_args['track_total_hits'] = true;
    return $formatted_args;
  }

}
