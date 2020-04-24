<?php

namespace Shoptet;

abstract class ImporterForm {

  function __construct() {
    add_action( 'acf/init', [ $this, 'add_field_group' ] );
    add_action( 'acf/init', [ $this, 'add_options_page' ] );
    add_action( 'admin_footer', [ $this, 'disable_import_button' ] );
    add_action( 'admin_notices', [ $this, 'show_notices' ] );
  }

  abstract function add_field_group();

  abstract function add_options_page();

  abstract function get_option_page_name();

  function is_import_page() {
    $screen = get_current_screen();
    return ( $screen && $this->get_option_page_name() == $screen->base );
  } 

  function disable_import_button() {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    if ( ! user_can( $current_user, 'subscriber' ) ) return;
    if ( ! $this->is_import_page() ) return;

    if (
      ! get_user_wholesaler( $current_user ) ||
      0 >= products_left_to_exceed( 'product', $current_user->ID )
    ) {
      echo '<script>document.getElementById("publish").disabled = true;</script>';
    }
  }

  function show_notices() {
    if ( ! $this->is_import_page() ) return;
    if ( isset($_GET['import_enqueued']) ):
      
      // Remove query param from url
      ?>
      <script>
        var newUrl = window.location.href.replace('&import_enqueued=1','');
        history.pushState({}, null, newUrl);
      </script>
      <div class="notice notice-success">
        <p><?php _e( 'Import zařazen do fronty ke zpracování', 'shp-obchodiste' ); ?></p>
      </div>
    <?php endif;
  }
}