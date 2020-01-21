<?php

require __DIR__ . '/vendor/autoload.php';

$includes = [
	'src/lib/action_scheduler.php',
	'src/lib/setup.php',
	'src/lib/cpt.php',
	'src/lib/acf.php',
	'src/lib/filters.php',
  'src/lib/helpers.php',
  'src/lib/operator_form.php',
  'src/lib/google_product_categories/google_product_categories.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);
