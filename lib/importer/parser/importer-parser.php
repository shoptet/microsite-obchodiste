<?php

namespace Shoptet;

abstract class ImporterParser {

  protected $source;
  protected $wholesaler;
  protected $default_category;
  protected $set_pending_status;
  protected $products_left;
  protected $products_imported = 0;

  function __construct( $source, $wholesaler, $default_category = null, $set_pending_status = false ) {
    $this->source = $source;
    $this->wholesaler = $wholesaler;
    $this->default_category = $default_category;
    $this->set_pending_status = $set_pending_status;
  }

  abstract function parse();

  function import() {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    if ( user_can( $current_user, 'subscriber' ) ) {
      $wholesaler = get_user_wholesaler( $current_user );
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
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it

    $is_exceed = (
      user_can( $current_user, 'subscriber' ) &&
      ( $this->products_left - $this->products_imported ) <= 0
    );

    return $is_exceed;
  }

}