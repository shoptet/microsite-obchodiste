<?php

namespace Shoptet;

class AdminDetailWholesaler extends AdminDetail {

  const POST_TYPE = 'custom';

  function __construct() {
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

    parent::__construct();
  }

  function get_post_type() {
    return self::POST_TYPE;
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
    ]);
  }

}

new AdminDetailWholesaler();