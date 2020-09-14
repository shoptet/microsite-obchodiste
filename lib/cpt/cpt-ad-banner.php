<?php

/**
 * Returns banner message custom post type arguments
 */
function get_cpt_ad_banner_args(): array
{
  $labels = [
    'name' => __( 'Reklamy', 'shp-obchodiste' ),
    'singular_name' => __( 'Reklama', 'shp-obchodiste' ),
    'menu_name' => __( 'Reklamy', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny reklamy', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat novou', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat novou reklamu', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit reklamu', 'shp-obchodiste' ),
    'new_item' => __( 'Nová reklama', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit reklamu', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit reklamy', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat reklamy', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyla nalezena žádná reklama', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyla nalezena žádná reklama', 'shp-obchodiste' ),
    'archives' => __( 'Archiv reklam', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis reklam', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Reklamy', 'shp-obchodiste' ),
    'labels' => $labels,
    'description' => '',
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => false,
    'show_in_menu' => true,
    'exclude_from_search' => true,
    'capability_type' => 'ad-banner',
    'map_meta_cap' => true,
    'hierarchical' => false,
    'rewrite' => false,
    'query_var' => true,
    'menu_icon' => 'dashicons-megaphone',
    'supports' => [ 'title' ],
  ];
  return $args;
}
