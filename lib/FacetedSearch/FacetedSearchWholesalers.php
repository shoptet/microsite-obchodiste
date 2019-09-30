<?php

class FacetedSearchWholesalers extends FacetedSearch
{

  public function __construct( &$wp_query )
  {
    parent::__construct( $wp_query );
  }

  public function order()
  {
    $meta_query = $this->getMetaQuery();

    if( ! isset( $_GET[ 'orderby' ] ) ) {
      $this->wp_query->set( 'meta_key', 'is_shoptet' );
      $this->wp_query->set( 'orderby', [ 'meta_value_num' => 'DESC', 'post_date' => 'DESC' ] );
    } else {
      $query = explode( '_', $_GET[ 'orderby' ] );
      if ( $query == [ 'date', 'desc' ] ) {
        $this->wp_query->set( 'meta_key', 'is_shoptet' );
        $this->wp_query->set( 'orderby', [ 'meta_value_num' => 'DESC', 'post_date' => $query[1] ] );
      } else {
        if ( $query[0] == 'title' ) {
          // title is not a meta key
          $this->wp_query->set( 'meta_key', 'is_shoptet' );
          $this->wp_query->set( 'orderby', [ 'meta_value_num' => 'DESC', 'title' => $query[1] ] );
        } else if ( $query[0] == 'favorite' ) {
          $meta_query[ 'is_shoptet_clause' ] = [
            'key' => 'is_shoptet',
            'compare' => 'EXISTS',
            'type' => 'numeric',
          ];
          $meta_query[ 'contact_count_clause' ] = [
            'key' => 'contact_count',
            'compare' => 'EXISTS',
            'type' => 'numeric',
          ];
          $this->wp_query->set( 'orderby', [
            'is_shoptet_clause' => 'DESC',
            'contact_count_clause' => $query[1],
          ] );
        }
      }
    }
    
    $this->wp_query->set( 'meta_query', $meta_query );
  }
}