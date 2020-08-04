<?php

require __DIR__ . '/vendor/autoload.php';

// CLI scripts
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'wp-cli/class-update-product-wholesaler-cli.php';
	require_once 'wp-cli/class-update-attachment-author-cli.php';
	require_once 'wp-cli/class-update-product-thumbnails-cli.php';
	require_once 'wp-cli/class-clean-postmeta-cli.php';
	require_once 'wp-cli/class-cache-csv-feed-cli.php';
	require_once 'wp-cli/class-clean-as-logs-cli.php';
}

$includes = [
	'src/lib/db-extension.php',
	'src/lib/action_scheduler.php',
  'src/lib/elasticpress.php',
	'src/lib/counter-cache.php',
	'src/lib/term-syncer.php',
	'src/lib/identification-number-api/identification-number-api.php',
	'src/lib/identification-number-api/identification-number-api-ajax.php',
	'src/lib/identification-number-api/countries/identification-number-api-cz.php',
	'src/lib/admin/admin-login.php',
	'src/lib/admin/admin-product-list.php',
	'src/lib/admin/admin-detail.php',
	'src/lib/admin/admin-detail-wholesaler.php',
	'src/lib/attachment.php',
	'src/lib/importer/importer.php',
	'src/lib/importer/importer-product.php',
	'src/lib/importer/importer-store.php',
	'src/lib/importer/form/importer-form.php',
	'src/lib/importer/form/importer-form-csv.php',
	'src/lib/importer/form/importer-form-xml.php',
	'src/lib/importer/parser/importer-parser.php',
	'src/lib/importer/parser/importer-parser-csv.php',
	'src/lib/importer/parser/importer-parser-xml.php',
	'src/lib/setup.php',
	'src/lib/cpt.php',
	'src/lib/acf/acf.php',
	'src/lib/acf/acf-wholesaler.php',
	'src/lib/acf/acf-product.php',
	'src/lib/acf/acf-message.php',
	'src/lib/acf/acf-options.php',
	'src/lib/filters.php',
  'src/lib/helpers.php',
  'src/lib/operator_form.php',
	'src/lib/exporter/exporter-google.php',
	'src/lib/exporter/exporter-admin.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);
