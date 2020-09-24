<?php

class ElasticPressSettings {

  static function init() {
    add_filter( 'ep_post_mapping', [ get_called_class(), 'add_analysis_default_analyzer' ] );
    add_filter( 'ep_post_mapping', [ get_called_class(), 'add_analysis_filter' ] );
    add_filter( 'ep_formatted_args', [ get_called_class(), 'unlimit_track_total_hits' ] );
    add_filter( 'ep_prepare_meta_excluded_public_keys', [ get_called_class(), 'exclude_meta_public_keys' ], 10, 2 );
    add_filter( 'ep_prepared_post_meta', [ get_called_class(), 'html_trim_meta' ] );
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

  static function exclude_meta_public_keys( $excluded_meta, $post ) {
    switch ( $post->post_type ) {
      case 'product':
        $excluded_meta = [
          'minimal_order',
          'code',
          'thumbnail',
          'sync_success',
          'gallery',
          'category',
        ];
      break;
      case 'custom':
        $excluded_meta = [
          'contact_count',
          'website',
          'facebook',
          'twitter',
          'instagram',
          'logo',
          'contact_photo',
          'category',
          'minor_category_1',
          'minor_category_2',
          'gallery',
          'video',
          'location',
        ];
      break;
    }
    return $excluded_meta;
  }

  static function html_trim_meta( $meta ) {
    foreach ( $meta as $key => $value ) {
      if ( isset($value[0]) ) {
        $trimmed = strip_tags($value[0]);
        $trimmed = preg_replace( '#[\n\r]+#s', ' ', $trimmed );
        $meta[$key][0] = $trimmed;
      }
    }
    return $meta;
  }

}
