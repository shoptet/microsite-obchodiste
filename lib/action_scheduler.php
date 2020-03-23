<?php

add_filter( 'action_scheduler_queue_runner_time_limit', function() {
  return 120;
} );

add_filter( 'action_scheduler_queue_runner_batch_size', function() {
  return 100;
} );

add_filter( 'action_scheduler_retention_period', function() {
  $day_in_seconds = 86400;
  return ( 14 * $day_in_seconds );
} );

/**
 * Handle requests initiated by as_run_queue() and start a queue runner if the request is valid.
 */
add_action( 'wp_ajax_nopriv_as_run_queue', function() {
  ActionScheduler_QueueRunner::instance()->run();
	wp_die();
} );

function as_run_queue() {
  // TODO: use external library for async http request
  // Request blocked for max 1s
  wp_remote_post( admin_url( 'admin-ajax.php' ), [
    'method'      => 'POST',
    'timeout'     => 1,
    'redirection' => 5,
    'httpversion' => '1.0',
    'blocking'    => false,
    'headers'     => [],
    'sslverify'   => false,
    'body'        => [
      'action'     => 'as_run_queue',
    ],
    'cookies'     => [],
  ] );
 }