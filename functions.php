<?php

require __DIR__ . '/vendor/autoload.php';

$includes = [
  'src/lib/elasticpress.php',
	'src/lib/counter-cache.php',
	'src/lib/sync-cleaner.php',
	'src/lib/setup.php',
	'src/lib/random-data-generator.php',
	'src/lib/cpt.php',
	'src/lib/acf/field-group.php',
	'src/lib/acf/options-page.php',
	'src/lib/filters.php',
  'src/lib/helpers.php',
  'src/lib/operator_form.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);
