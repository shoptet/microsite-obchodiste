<?php

add_filter('acf/settings/show_admin', '__return_false');

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page([
		'menu_title' 	=> __( 'Šablona', 'shp-obchodiste' ),
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'position'    => 61,
		'icon_url'    => 'dashicons-welcome-widgets-menus',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> __( 'Obecná správa', 'shp-obchodiste' ),
		'menu_title' 	=> __( 'Obecné', 'shp-obchodiste' ),
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> __( 'Nastavení domovské stránky', 'shp-obchodiste' ),
		'menu_title' 	=> __( 'Homepage', 'shp-obchodiste' ),
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> __( 'Nastavení mailingu', 'shp-obchodiste' ),
		'menu_title' 	=> __( 'Mailing', 'shp-obchodiste' ),
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> __( 'Nastavení stránky přihlášení', 'shp-obchodiste' ),
		'menu_title' 	=> __( 'Přihlášení', 'shp-obchodiste' ),
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> __( 'Import produktů přes CSV', 'shp-obchodiste' ),
		'menu_title' 	=> __( 'Import CSV', 'shp-obchodiste' ),
		'parent_slug' => 'edit.php?post_type=product',
		'menu_slug'   => 'product-import',
		'capability' => 'product_import',
		'update_button'		=> __( 'Importovat', 'shp-obchodiste' ),
		'updated_message'	=> __( 'Produkty úspešně importovány', 'shp-obchodiste' ),
	]);

	// acf_add_options_sub_page([
	// 	'page_title' 	=> __( 'Import produktů přes XML', 'shp-obchodiste' ),
	// 	'menu_title' 	=> __( 'Import XML', 'shp-obchodiste' ),
	// 	'parent_slug' => 'edit.php?post_type=product',
	// 	'menu_slug'   => 'product-import-xml',
	// 	'capability' => 'product_import',
	// 	'update_button'		=> __( 'Importovat', 'shp-obchodiste' ),
	// 	'updated_message'	=> __( 'Produkty úspešně importovány', 'shp-obchodiste' ),
	// ]);

}
