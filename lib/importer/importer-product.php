<?php

namespace Shoptet;

use Tightenco\Collect\Support\Collection;

class ImporterProduct {

  protected $wholesaler;
  protected $category_custom;
  protected $category_default;
  protected $category_google;
  protected $pending_status;
  protected $name;
  protected $short_description;
  protected $description;
  protected $price;
  protected $vat_included;
  protected $minimal_order;
  protected $ean;
  protected $code;
  protected $images = [];

  function __construct( array $data_array = [] ) {
    foreach( $data_array as $key => $value ) {
      switch( $key ) {
        case 'wholesaler':
          $this->wholesaler = $value;
        break;
        case 'category_custom':
          $this->category_custom = $value;
        break;
        case 'category_default':
          $this->category_default = $value;
        break;
        case 'category_google':
          $this->category_google = $value;
        break;
        case 'pending_status':
          $this->pending_status = $value;
        break;
        case 'name':
          $this->name = $value;
        break;
        case 'short_description':
          $this->short_description = $value;
        break;
        case 'description':
          $this->description = $value;
        break;
        case 'price':
          $this->price = $value;
        break;
        case 'vat_included':
          $this->vat_included = $value;
        break;
        case 'minimal_order':
          $this->minimal_order = $value;
        break;
        case 'ean':
          $this->ean = $value;
        break;
        case 'code':
          $this->code = $value;
        break;
        case 'images':
          $this->images = $value;
        break;
      }
    }
  }

  public function get_wholesaler() {
    return $this->wholesaler;
  }

  public function get_category() {
    if ( $this->category_google ) {
      $term_query = new \WP_Term_Query( [
        'taxonomy' => 'producttaxonomy',
        'fields' => 'ids',
        'hide_empty' => false,
        'meta_key' => 'shoptet_category_id',
        'meta_value' => $this->category_google,
      ] );
      if ( ! empty($term_query->terms) ) {
        $category = $term_query->terms[0];
      }
    } else if ( $this->category_custom ) {
      $category = $this->category_custom;
    } else if ( $this->category_default ) {
      $category = $this->category_default;
    }

    if ( isset($category) && term_exists( $category, 'producttaxonomy' ) ) {
      return $category;
    } else {
      return null;
    }
  }

  public function get_pending_status() {
    return $this->pending_status;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_short_description() {
    return $this->short_description;
  }

  public function get_description() {
    return $this->description;
  }

  public function get_price() {
    return $this->price;
  }

  public function get_vat_included() {
    return $this->vat_included;
  }

  public function get_minimal_order() {
    return $this->minimal_order;
  }

  public function get_ean() {
    return $this->ean;
  }

  public function get_code() {
    return $this->code;
  }

  public function get_images() {
    return $this->images;
  }

  public function import_xml_collection( Collection $product_collection ) {
    if ( $name = $product_collection->get('NAME') )
      $this->name = $name;
    if ( $short_description = $product_collection->get('SHORT_DESCRIPTION') ) 
      $this->short_description = $short_description;
    if ( $description = $product_collection->get('DESCRIPTION') ) 
      $this->description = $description;
    if ( $price = $product_collection->get('PRICE') )
      $this->price = floatval($price);
    elseif ( $price_vat = $product_collection->get('PRICE_VAT') ) {
      $this->price = floatval($price_vat);
      $this->vat_included = 1;
    }
    if ( $ean = $product_collection->get('EAN') )
      $this->ean = $ean;
    if ( $code = $product_collection->get('CODE') )
      $this->code = $code;
    if ( $categories = $product_collection->get('CATEGORIES') ) {
      $categories = $categories->where('DEFAULT_CATEGORY')->where('google-id');
      if ( $category = $categories->first() )
        $this->category_google = intval($category->get('google-id'));
    }
    if ( $stock = $product_collection->get('STOCK') ) {
      $stock = $stock->toArray();
      if ( !empty($stock[2][0]) )
        $this->minimal_order = intval($stock[2][0]);
    }
    if ( $images = $product_collection->get('IMAGES') ) {
      $images = $images->toArray();
      foreach( $images as $img ) {
        if ( empty($img['IMAGE']) ) continue;
        $this->images[] = filter_var($img['IMAGE'], FILTER_SANITIZE_URL);
      }
    }
  }

  public function import_csv_array( array $product_array ) {
    if ( ! empty($product_array['name']) )
      $this->name = $product_array['name'];
    if ( ! empty($product_array['shortDescription']) )
      $this->short_description = $product_array['shortDescription'];
    if ( ! empty($product_array['description']) )
      $this->description = $product_array['description'];
    if ( ! empty($product_array['price']) )
      $this->price = floatval($product_array['price']);
    if ( ! empty($product_array['ean']) )
      $this->ean = $product_array['ean'];
    if ( ! empty($product_array['code']) )
      $this->code = $product_array['code'];
    if ( ! empty($product_array['minimumAmount']) )
      $this->minimal_order = intval($product_array['minimumAmount']);
    if ( ! empty($product_array['googleCategoryId']) )
      $this->category_google = intval($product_array['googleCategoryId']);
    foreach ( [ 'image', 'image2', 'image3', 'image4', 'image5' ] as $img_key ) {
      if ( empty($product_array[$img_key]) ) continue;
      $this->images[] = filter_var($product_array[$img_key], FILTER_SANITIZE_URL);
    }
  }

  public function to_array() {
    $export = [];
    if ( isset($this->wholesaler) )
      $export['wholesaler'] = $this->wholesaler;
    if ( isset($this->category) )
      $export['category_custom'] = $this->category_custom;
    if ( isset($this->category_default) )
      $export['category_default'] = $this->category_default;
    if ( isset($this->category_google) )
      $export['category_google'] = $this->category_google;
    if ( isset($this->pending_status) )
      $export['pending_status'] = $this->pending_status;
    if ( isset($this->name) )
      $export['name'] = $this->name;
    if ( isset($this->short_description) )
      $export['short_description'] = $this->short_description;
    if ( isset($this->description) )
      $export['description'] = $this->description;
    if ( isset($this->price) )
      $export['price'] = $this->price;
    if ( isset($this->vat_included) )
      $export['vat_included'] = $this->vat_included;
    if ( isset($this->minimal_order) )
      $export['minimal_order'] = $this->minimal_order;
    if ( isset($this->ean) )
      $export['ean'] = $this->ean;
    if ( isset($this->code) )
      $export['code'] = $this->code;
    if ( isset($this->images) )
      $export['images'] = $this->images;
    return $export;
  }

}