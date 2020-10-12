<?php

namespace Shoptet;

abstract class ImporterParser {

  protected $source;
  protected $wholesaler;
  protected $default_category;
  protected $set_pending_status;
  protected $user_id;
  protected $products_left;
  protected $products_imported = 0;

  function __construct( $source, $wholesaler, $default_category = null, $set_pending_status = false, $user_id = 0 ) {
    $this->source = $source;
    $this->wholesaler = $wholesaler;
    $this->default_category = $default_category;
    $this->set_pending_status = $set_pending_status;
    $this->user_id = $user_id;
  }

  abstract function parse();

  function import() {
    if ( user_can( $this->user_id, 'subscriber' ) ) {
      $wholesaler = get_user_wholesaler( $this->user_id );
      $this->wholesaler = $wholesaler->ID;
      $wholesaler_author_id = get_post_field( 'post_author', $this->wholesaler );
      $this->products_left = products_left_to_exceed( 'product', $wholesaler_author_id );
    }
    
    $this->parse();
      
    TermSyncer::enqueueWholesaler( $this->wholesaler );
  }

  function get_product_base() {
    return new ImporterProduct([
      'wholesaler' => $this->wholesaler,
      'category_default' => $this->default_category,
      'pending_status' => $this->set_pending_status,
    ]);
  }

  function is_exceed() {
    return (
      user_can( $this->user_id, 'subscriber' ) &&
      ( $this->products_left - $this->products_imported ) <= 0
    );
  }

  function maybe_enqueue( $product ) {
    if( $product->is_valid() ) {
      Importer::enqueue_product($product);
      $this->products_imported++;
    } else {
      // Skip invalid product
    }
  }

}