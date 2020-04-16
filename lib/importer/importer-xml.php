<?php

namespace Shoptet;

use Rodenastyle\StreamParser\StreamParser;
use Tightenco\Collect\Support\Collection;

class ImporterXML {

  const ACF_OPTION_PAGE_NAME = 'product_page_product-import-xml';
  const ACF_XML_FEED_URL_FIELD = 'product_xml_feed_url';

  static function init() {
    add_action( 'acf/init', [ get_called_class(), 'add_field_group' ] );
    add_action( 'acf/init', [ get_called_class(), 'add_options_page' ] );

    add_action( 'acf/save_post', [ get_called_class(), 'form_submit' ], 1 );
  }

  static function form_submit() {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
    $screen = get_current_screen();
  
    if ( ! $screen || self::ACF_OPTION_PAGE_NAME !== $screen->base ) return;
  
    // bail early if no ACF data
    if( empty( $_POST['acf'] ) ) return;
  
    $fields = $_POST['acf'];
    $product_category_id = false;
    $set_pending_status = false;
    foreach( $fields as $key => $value ) {
      $field = acf_get_field( $key );
      switch ( $field['name'] ) {
        case 'related_wholesaler':
        $related_wholesaler_id = $value;
        break;
        case 'product_category':
        $product_category_id = intval( $value );
        break;
        case self::ACF_XML_FEED_URL_FIELD:
        $xml_feed_url = filter_var($value, FILTER_SANITIZE_URL);
        break;
        case 'set_pending_status':
        $set_pending_status = boolval( intval( $value ) );
        break;
      }
    }
  
    if ( user_can( $current_user, 'subscriber' ) ) {
      $related_wholesaler = get_user_wholesaler( $current_user );
      $related_wholesaler_id = $related_wholesaler->ID;
    }

    if ( user_can( $current_user, 'subscriber' ) ) {
      $wholesaler_author_id = get_post_field( 'post_author', $related_wholesaler_id );
      $products_left = products_left_to_exceed( 'product', $wholesaler_author_id );
    }

    $product_base = new ImporterProduct([
      'wholesaler' => $related_wholesaler_id,
      'category_bulk' => $product_category_id,
      'pending_status' => $set_pending_status,
    ]);
  
    $products_imported = 0;
    StreamParser::xml($xml_feed_url)->each(function(Collection $product_collection) use ( &$current_user, &$products_imported, &$products_left, &$product_base ) {

      // break importing for subscriber if number of products exceeded
      if (
        user_can( $current_user, 'subscriber' ) &&
        ( $products_left - $products_imported ) <= 0
      ) return;

      $product = clone $product_base;
      $product->import_xml_collection( $product_collection );

      Importer::enqueueProduct($product);
      
      $products_imported++;
    });
  
    $_POST['acf'] = []; // Do not save any data
  
    TermSyncer::enqueueWholesaler( $related_wholesaler_id );
  
    as_run_queue();
  
    // Add query param to url for admin notice
    wp_redirect( add_query_arg( [
      'products_imported' => $products_imported,
    ] ) );
    exit;
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
    if( function_exists('acf_add_local_field_group') ) {
      acf_add_local_field_group(array(
        'key' => 'group_5e970c7743e04',
        'title' => 'Hromadný import produktů',
        'fields' => array(
          array(
            'key' => 'field_5e970c774b4a2',
            'label' => 'Velkoobchod',
            'name' => 'related_wholesaler',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' => array(
              0 => 'custom',
            ),
            'taxonomy' => array(
            ),
            'allow_null' => 0,
            'multiple' => 0,
            'return_format' => 'object',
            'ui' => 1,
          ),
          array(
            'key' => 'field_5e970c774b4dc',
            'label' => 'XML feed URL',
            'name' => 'product_xml_feed_url',
            'type' => 'url',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
          ),
          array(
            'key' => 'field_5e970c774b510',
            'label' => 'Kategorie',
            'name' => 'product_category',
            'type' => 'taxonomy',
            'instructions' => 'Vyberte výchozí kategorii, do které chcete zařadit produkty bez vyplněné kategorie.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'taxonomy' => 'producttaxonomy',
            'field_type' => 'select',
            'allow_null' => 1,
            'add_term' => 0,
            'save_terms' => 0,
            'load_terms' => 0,
            'return_format' => 'id',
            'multiple' => 0,
          ),
          array(
            'key' => 'field_5e970c774b542',
            'label' => 'Odeslat ke schválení importované produkty',
            'name' => 'set_pending_status',
            'type' => 'true_false',
            'instructions' => 'Ke schválení se odešlou produkty, které mají jméno, popis, krátký popis, kategorii a alespoň jeden obrázek.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => 'Odeslat ke schválení',
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
          ),
        ),
        'location' => array(
          array(
            array(
              'param' => 'options_page',
              'operator' => '==',
              'value' => 'product-import-xml',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
      ));
    }
  }

}

ImporterXML::init();