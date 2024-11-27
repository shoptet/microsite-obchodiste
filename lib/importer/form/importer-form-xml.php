<?php

namespace Shoptet;

use Rodenastyle\StreamParser\StreamParser;
use Tightenco\Collect\Support\Collection;

class ImporterFormXML extends ImporterForm {

  const ACF_OPTION_PAGE_NAME = 'product_page_product-import-xml';
  const ACF_XML_FEED_URL_FIELD = 'product_xml_feed_url';

  function __construct() {
    add_action( 'acf/save_post', [ $this, 'form_submit' ], 1 );

    // Via: https://github.com/Hube2/acf-filters-and-functions/blob/master/customized-options-page.php
    add_action( self::ACF_OPTION_PAGE_NAME, [ $this, 'form_description_start' ], 1 );
    add_action( self::ACF_OPTION_PAGE_NAME, [ $this, 'form_description_end' ], 20 );

    parent::__construct();
  }

  function form_submit() {
    global $current_user;
    wp_get_current_user(); // Make sure global $current_user is set, if not set it
  
    if ( ! $this->is_import_page() ) return;
  
    if( empty( $_POST['acf'] ) ) return;
  
    $fields = $_POST['acf'];
    foreach( $fields as $key => $value ) {
      $field = acf_get_field($key);
      switch ( $field['name'] ) {
        case 'related_wholesaler':
          $wholesaler = intval($value);
        break;
        case 'product_category':
          $default_category = intval($value);
        break;
        case self::ACF_XML_FEED_URL_FIELD:
          $value = htmlspecialchars_decode($value);
          $xml_feed_url = filter_var($value, FILTER_SANITIZE_URL);
        break;
        case 'set_pending_status':
          $set_pending_status = boolval(intval($value));
        break;
      }
    }

    $user_id = get_current_user_id();
    Importer::enqueue_import( 'xml', $xml_feed_url, $wholesaler, $default_category, $set_pending_status, $user_id );

    $_POST['acf'] = []; // Do not save any data

    wp_redirect( add_query_arg( [
      'import_enqueued' => 1,
    ] ) );
    exit;
  }

  function get_option_page_name() {
    return self::ACF_OPTION_PAGE_NAME;
  }

  function form_description () {
    $options = get_fields( 'options' );
    $description = '';
    if( ! empty( $options[ 'product_import_xml_description' ] ) ) {
      $description = $options[ 'product_import_xml_description' ];
    }
    return $description;
  }

  function add_options_page() {
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

  function add_field_group() {
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
      acf_add_local_field_group(array(
        'key' => 'group_5ecfdd82dce61',
        'title' => 'Nastavení stránky XML importu',
        'fields' => array(
          array(
            'key' => 'field_5ecfd8cce9a7a',
            'label' => 'Popis stránky pro XML import produktů',
            'name' => 'product_import_xml_description',
            'type' => 'wysiwyg',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
          ),
        ),
        'location' => array(
          array(
            array(
              'param' => 'options_page',
              'operator' => '==',
              'value' => 'acf-options-obecne',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
      ));
    }
  }

}

new ImporterFormXML();