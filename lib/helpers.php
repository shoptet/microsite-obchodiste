<?php

/**
 * Check whether a post is new
 */
function is_post_new(): bool
{
  $is_new_interval = 15; // in days
  $today = new DateTime();
  $post_date = new DateTime( get_the_date( 'Y-m-d' ) );
  $interval = $today->diff( $post_date );
  return $interval->days <= $is_new_interval;
}

/**
 * Remove protocol and last slash from url
 */
function display_url( $url ): string
{
	// Romove protocol
  if ( substr( $url, 0, 7 ) === 'http://' ) {
		$url = substr( $url, 7 );
  } else if ( substr( $url, 0, 8 ) === 'https://' ) {
		$url = substr( $url, 8 );
	} else if ( substr( $url, 0, 2 ) === '//' ) {
	 	$url = substr( $url, 2 );
 	}
	// Remove last slash
	if ( substr( $url, -1 ) === '/' ) {
		$url = substr( $url, 0, -1 );
	}
  return $url;
}
