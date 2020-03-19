<?php

add_filter( 'action_scheduler_queue_runner_time_limit', function() {
  return 120;
} );

add_filter( 'action_scheduler_queue_runner_batch_size', function() {
  return 100;
} );