<?php

require_once 'db-extension-manager.php';
require_once 'db-extension-post-type.php';
require_once 'db-extension-store.php';
require_once 'db-extension-utility.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'db-extension-cli.php';
}

add_action( 'init', function() {

  global $dbx;
  $dbx = new Shoptet\DBXManager();
  
  do_action( 'dbx/init' );
} );
