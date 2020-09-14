<?php

/**
 * Returns product custom post type arguments
 */
function get_cpt_product_args(): array
{
  $labels = [
    'name' => __( 'Produkty', 'shp-obchodiste' ),
    'singular_name' => __( 'Produkt', 'shp-obchodiste' ),
    'menu_name' => __( 'Produkty', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny produkty', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat nový', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat nový produkt', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit produkt', 'shp-obchodiste' ),
    'new_item' => __( 'Nový produkt', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit produkt', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit produkty', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat produkty', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyl nalezen žádný produkt', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyl nalezen žádný produkt', 'shp-obchodiste' ),
    'archives' => __( 'Archiv produktů', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis produktů', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Produkty', 'shp-obchodiste' ),
    'labels' => $labels,
    'description' => '',
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => 'produkty',
    'show_in_menu' => true,
    'exclude_from_search' => false,
    'capability_type' => 'product',
    'map_meta_cap' => true,
    'hierarchical' => false,
    'query_var' => true,
    'rewrite' => [ 'slug' => 'produkt', 'with_front' => true ],
    'menu_icon' => 'dashicons-cart',
    'supports' => [ 'title', 'thumbnail', 'author' ],
  ];
  return $args;
}

/**
 * Returns product custom post type taxonomy arguments
 */
function get_cpt_product_taxonomy_args(): array
{
  $labels = [
    'name' => __( 'Kategorie', 'shp-obchodiste' ),
    'singular_name' => __( 'Kategorie', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Kategorie', 'shp-obchodiste' ),
    'labels' => $labels,
    'public' => true,
    'hierarchical' => true,
    'label' => 'Kategorie',
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => true,
    'query_var' => true,
    'rewrite' => [ 'slug' => 'produkty', 'with_front' => true ],
    'show_admin_column' => false,
    'show_in_rest' => false,
    'rest_base' => '',
    'show_in_quick_edit' => false,
  ];
  return $args;
}
