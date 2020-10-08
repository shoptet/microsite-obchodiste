<?php

add_action( 'acf/init', function () {

  acf_add_local_field_group(array(
    'key' => 'group_5f5f838b10e8f',
    'title' => 'Reklama',
    'fields' => array(
      array(
        'key' => 'field_5f5f87b0c732e',
        'label' => 'Deaktivovat reklamu',
        'name' => 'is_disabled',
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'message' => 'Deaktivovat reklamu',
        'default_value' => 0,
        'ui' => 0,
        'ui_on_text' => '',
        'ui_off_text' => '',
      ),
      array(
        'key' => 'field_5f5f839dae489',
        'label' => 'Kategorie velkoobchodu',
        'name' => 'wholesaler_tax_terms',
        'type' => 'taxonomy',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'taxonomy' => 'customtaxonomy',
        'field_type' => 'multi_select',
        'allow_null' => 1,
        'add_term' => 0,
        'save_terms' => 0,
        'load_terms' => 0,
        'return_format' => 'id',
        'multiple' => 0,
      ),
      array(
        'key' => 'field_5f5f83d6ae48a',
        'label' => 'Kategorie produktu',
        'name' => 'product_tax_terms',
        'type' => 'taxonomy',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'taxonomy' => 'producttaxonomy',
        'field_type' => 'multi_select',
        'allow_null' => 1,
        'add_term' => 0,
        'save_terms' => 0,
        'load_terms' => 0,
        'return_format' => 'id',
        'multiple' => 0,
      ),
      array(
        'key' => 'field_5f5f846b8a466',
        'label' => 'Zobrazovat od',
        'name' => 'date_from',
        'type' => 'date_picker',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'display_format' => 'F j, Y',
        'return_format' => 'Ymd',
        'first_day' => 1,
      ),
      array(
        'key' => 'field_5f5f84d18a467',
        'label' => 'Zobrazovat do',
        'name' => 'date_to',
        'type' => 'date_picker',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'display_format' => 'F j, Y',
        'return_format' => 'Ymd',
        'first_day' => 1,
      ),
      array(
        'key' => 'field_5f5f84e48a468',
        'label' => 'Cílová URL',
        'name' => 'target_url',
        'type' => 'url',
        'instructions' => '',
        'required' => 0,
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
        'key' => 'field_5f5f85098a469',
        'label' => 'Banner mobil',
        'name' => 'banner_mobile',
        'type' => 'image',
        'instructions' => '740x450px, JPG, max. 250 KB',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'preview_size' => 'medium',
        'library' => 'uploadedTo',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
      ),
      array(
        'key' => 'field_5f5f85438a46b',
        'label' => 'Banner desktop',
        'name' => 'banner_desktop',
        'type' => 'image',
        'instructions' => '810x100px, JPG, max. 250 KB',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'preview_size' => 'medium',
        'library' => 'uploadedTo',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'ad_banner',
        ),
      ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
  ));
  
} );