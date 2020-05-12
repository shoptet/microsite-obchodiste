<?php

add_filter( 'action_scheduler_queue_runner_time_limit', function() {
  return 3600;
} );

add_filter( 'action_scheduler_queue_runner_batch_size', function() {
  return 100;
} );

add_filter( 'action_scheduler_retention_period', function() {
  $day_in_seconds = 86400;
  return ( 60 * $day_in_seconds );
} );


// Disable default runner
// ActionScheduler_QueueRunner::init() is attached to 'init' with priority 1, so we need to run after that
add_action( 'init', function () {
	if ( class_exists( 'ActionScheduler' ) ) {
		remove_action( 'action_scheduler_run_queue', array( ActionScheduler::runner(), 'run' ) );
	}
}, 10 );
