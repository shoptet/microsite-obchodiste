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
    if ( get_user_wholesaler( $current_user ) ) return;
    echo '<script>document.getElementById("publish").disabled = true;</script>';
  }

  function show_notices() {
    if ( ! $this->is_import_page() ) return;
    if ( isset($_GET['products_imported']) ):
      $products_imported = intval($_GET['products_imported']);
      
      // Remove query param from url
      ?>
      <script>
        var newUrl = window.location.href.replace('&products_imported=<?php echo $products_imported; ?>','');
        history.pushState({}, null, newUrl);
      </script>
      <?php if ( $products_imported > 0 ): ?>
        <div class="notice notice-success">
          <p><?php printf( __( 'Produkty přidány do fronty ke zpracování. Celkem přidáno produktů: %d', 'shp-obchodiste' ), $products_imported ); ?></p>
        </div>
      <?php else: ?>
        <div class="notice notice-error">
          <p><?php _e( 'Nebyl importován žádný produkt', 'shp-obchodiste' ); ?></p>
        </div>
      <?php endif;
    endif;
  }
}