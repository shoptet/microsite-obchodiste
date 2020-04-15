<?php

namespace Shoptet;

class ImporterXML {

  static function init() {
    add_action( 'acf/init', [ get_called_class(), 'add_field_group' ] );
    add_action( 'acf/init', [ get_called_class(), 'add_options_page' ] );
  }

  static function add_options_page() {
    if( function_exists('acf_add_options_page') ) {
      acf_add_options_sub_page([
        'page_title' 	=> __( 'Import produktů přes XML', 'shp-obchodiste' ),
        'menu_title' 	=> __( 'Import XML', 'shp-obchodiste' ),
        'parent_slug' => 'edit.php?post_type=product',
        'menu_slug'   => 'product-import-xml',
        'capability' => 'product_import',
        'update_button'		=> __( 'Importovat', 'shp-obchodiste' ),
        'updated_message'	=> __( 'Produkty úspešně importovány', 'shp-obchodiste' ),
      ]);
    }
  }

  static function add_field_group() {
    if( function_exists('acf_add_local_field_group') ) {}
  }

}

ImporterXML::init();