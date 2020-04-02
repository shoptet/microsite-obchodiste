<?php

require __DIR__ . '/vendor/autoload.php';

// CLI scripts
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'wp-cli/class-update-product-wholesaler-cli.php';
}

$includes = [
	'src/lib/action_scheduler.php',
  'src/lib/elasticpress.php',
	'src/lib/counter-cache.php',
	'src/lib/term-syncer.php',
	'src/lib/google_product_categories/google_product_categories.php',
	'src/lib/AdminProductList.php',
	'src/lib/Importer.php',
	'src/lib/AdminProductList.php',
	'src/lib/ShoptetStats.php',
	'src/lib/LoginScreen.php',
	'src/lib/setup.php',
	'src/lib/cpt.php',
	'src/lib/acf/field-group.php',
	'src/lib/acf/options-page.php',
	'src/lib/filters.php',
  'src/lib/helpers.php',
  'src/lib/operator_form.php',
	'src/lib/csv_feed.php',
	'src/lib/shoptet_categories_map/ShoptetCategoriesMap.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);
