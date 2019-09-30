<?php

class FacetedSearch
{

  protected $wp_query;
  protected $options;

  protected const POSTS_PER_PAGE = 12;

  public function __construct( &$wp_query )
  {
    $this->wp_query = $wp_query;
    $this->wp_query->set( 'posts_per_page', self::POSTS_PER_PAGE );
    $this->options = get_fields('options');
  }

  protected function getMetaQuery()
  {
    $meta_query = $this->wp_query->get( 'meta_query' );

    if ( $meta_query == '' ) {
      $meta_query = [];
    }

    return $meta_query;
  }

  public function filterBySearchQuery()
  {
    if( isset( $_GET[ 's' ] ) && ! empty( $_GET[ 's' ] ) ) {
      $this->wp_query->set( 's', $_GET[ 's' ] );
    }
  }

  public function filterByMetaQuery( $key, $relation = 'AND', $compare = '=' )
  {
    $meta_query = $this->getMetaQuery();
    
    if( isset( $_GET[ $key ] ) && is_array( $_GET[ $key ] ) ) {
      $result = [ 'relation' => $relation ];
      foreach( $_GET[ $key ] as $value ) {
        $result[] = [
          'key' => $key,
          'value' => $value,
          'compare' => $compare,
        ];
      }
      $meta_query[] = $result;
    }

    $this->wp_query->set( 'meta_query', $meta_query );
  }

  public function order()
  {
    if( ! isset( $_GET[ 'orderby' ] ) ) return;

    $query = explode( '_', $_GET[ 'orderby' ] );
    
    if ( $query[0] == 'title' ) {
      $this->wp_query->set('orderby', 'title');
      $this->wp_query->set('order', $query[1]);
    } else if ( $query != ['date', 'desc'] ) {
      // skip default ordering by post_date DESC
      // e.g. '?orderby=date_asc'
      $this->wp_query->set('orderby', 'meta_value_num');
      $this->wp_query->set('meta_key', $query[0]);
      $this->wp_query->set('order', $query[1]);
    }
  }

}