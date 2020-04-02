<?php

namespace Shoptet;

class Attachment {

  public static function init() {
    add_action( 'add_attachment', [ get_called_class(), 'handle_author' ] );
    add_action( 'edit_attachment', [ get_called_class(), 'handle_author' ] );
  }

  public static function handle_author( $attach_id ) {
    $attach = get_post( $attach_id );
    
    if ( $attach ) {
      $parent = get_post( $attach->post_parent );
    }

    if ( empty($parent) ) {
      return;
    }

    if ( $attach->post_author != $parent->post_author ) {
      // Prevent infinite loop because wp_update_post trigger edit_attachment again
      remove_action( 'edit_attachment', [ get_called_class(), 'handle_author' ] );

      wp_update_post([
        'ID' => $attach_id,
        'post_author' => $parent->post_author,
      ]);

      add_action( 'edit_attachment', [ get_called_class(), 'handle_author' ] );
    }
  }

}

Attachment::init();