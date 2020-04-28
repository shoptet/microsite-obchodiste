<?php

namespace Shoptet;

class AdminDetailWholesaler extends AdminDetail {

  const POST_TYPE = 'custom';

  function __construct() {
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    add_action( 'edit_form_top', [ $this, 'render_title_instructions' ] );
    add_action( 'admin_head', [ $this, 'lock_published_post' ] );

    parent::__construct();
  }

  function get_post_type() {
    return self::POST_TYPE;
  }

  function render_title_instructions() {
    if ( ! $this->is_correct_detail_page() ) return;

    $instructions = __( 'Zadejte oficiální název firmy dle IČO. Např. „Shoptet s.r.o.“', 'shp-obchodiste' );
    echo "<p class='description' style='margin: 1rem 0 0 0;'>{$instructions}</p>";
  }

  /**
   * Disable title, slug and status editing after post is published
   */
  function lock_published_post() {
    if ( ! $this->is_correct_detail_page() ) return;

    global $post, $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    if ( ! user_can( $current_user, 'subscriber' ) ) return;

    if ( 'publish' == $post->post_status ) {
      echo '
        <style>
          #edit-slug-buttons,
          .edit-post-status {
            display: none;
          }
          #titlediv #title {
            pointer-events: none;
            background-color: transparent;
          }
        </style>
      ';
    }
  }

  function enqueue_scripts() {
    if ( ! $this->is_correct_detail_page() ) return;

    $file_name = '/src/dist/js/wholesaler-autofill.js';
    $file_url = get_template_directory_uri() . $file_name;
    $file_path = get_template_directory() . $file_name;
    wp_enqueue_script( 'wholesaler-autofill', $file_url, ['jquery'], filemtime($file_path), true );

    wp_localize_script( 'wholesaler-autofill', 'settings', [
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'nonce' => wp_create_nonce( IdentificationNumberApiAjax::NONCE_ACTION ),
    ]);

    wp_localize_script( 'wholesaler-autofill', 'local', [
      'button_label' => __( 'Předvyplnit údaje podle IČO', 'shp-obchodiste' ),
      'error' => __( 'Něco se pokazilo. Zkuste prosím vyplnit údaje ručně.', 'shp-obchodiste' ),
      'success' => __( 'Údaje úspěšně předvyplněny', 'shp-obchodiste' ),
    ]);
  }

}

new AdminDetailWholesaler();