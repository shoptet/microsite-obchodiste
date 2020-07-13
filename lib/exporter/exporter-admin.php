<?php

namespace Shoptet;

class ExporterAdmin {

  static function init() {
    add_action( 'admin_menu', [ get_called_class(), 'admin_menu' ] );
    add_action( 'admin_post_export_wholesalers', [ get_called_class(), 'handle_wholesaler_export' ] );
  }

  static function admin_menu () {
    add_submenu_page(
      'edit.php?post_type=custom',
      __( 'Export velkoobchodů', 'shp-obchodiste' ),
      __( 'Export', 'shp-obchodiste' ),
      'manage_options',
      'export',
      [ get_called_class(), 'wholesaler_export_page' ]
    );
  }

  static function wholesaler_export_page () {
    ?>
    <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
      <input type="hidden" name="action" value="export_wholesalers">
      <?php submit_button(  __( 'Exportovat velkoobchody', 'shp-obchodiste' ) ); ?>
    </form>
    <?php
  }

  static function handle_wholesaler_export () {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    if ( user_can( $current_user, 'administrator' ) ) {
      self::export_wholesalers();
    }
  }

  static function get_wholesaler_row ($post_id) {
    $row = [];
    $contact_person_name = get_post_meta( $post_id, 'contact_full_name', true );
    $contact_person_email = get_post_meta( $post_id, 'contact_email', true );
    $contact_person_tel = get_post_meta( $post_id, 'contact_tel', true );
    $is_shoptet = boolval( get_post_meta( $post_id, 'is_shoptet', true ) );

    $wp_query_all_products = new \WP_Query( [
      'post_type' => 'product',
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'ep_integrate' => true,
      'meta_query' => [
        [
          'key' => 'related_wholesaler',
          'value' => $post_id,
        ],
      ],
    ] );

    $row[] = get_the_title( $post_id );
    $row[] = get_post_status( $post_id );
    $row[] = $is_shoptet ? 1 : 0;
    $row[] = count( $wp_query_all_products->posts );
    $row[] = $contact_person_name;
    $row[] = $contact_person_email;
    $row[] = $contact_person_tel;
    return $row;
  }

  static function export_wholesalers () {
    $file_path = get_temp_dir() . 'export_wholesalers.csv';
    $fp = fopen( $file_path, 'w' );
    $header = [
      'company name',
      'status',
      'shoptet',
      'products',
      'contact person name',
      'contact person e-mail',
      'contact person tel',
    ];
    fputcsv( $fp, $header );

    $posts_per_page = 350;
    $paged = 1;

    $args = [
      'post_type' => 'custom',
      'posts_per_page' => $posts_per_page,
      'post_status' => 'any',
      'fields' => 'ids',
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ];

    do {
      $args['paged'] = $paged;
      $wp_query = new \WP_Query( $args );
      foreach( $wp_query->posts as $post_id ) {
        $row = self::get_wholesaler_row($post_id);
        fputcsv( $fp, $row );
      }
      stop_the_insanity();
      $paged++;
    } while ( count($wp_query->posts) );

    fclose( $fp );

    // Http headers for downloads
    header( 'Pragma: public' );
    header( 'Expires: 0' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ); 
    header( 'Content-Type: application/octet-stream' );
    header( 'Content-Disposition: attachment; filename=export.csv' );
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Content-Length: ' . filesize( $file_path ) );
    while ( ob_get_level() ) {
      ob_end_clean();
      @readfile( $file_path );
    }

    unlink( $file_path );
  }
  
}

ExporterAdmin::init();