<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page([
		'menu_title' 	=> __( 'Šablona', '' ),
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'position'    => 61,
		'icon_url'    => 'dashicons-welcome-widgets-menus',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> __( 'Obecná správa', '' ),
		'menu_title' 	=> __( 'Obecné', '' ),
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> 'Nastavení mailingu',
		'menu_title' 	=> 'Mailing',
		'parent_slug' => 'theme-settings',
	]);

}
