<?php

namespace Shoptet;

abstract class AdminDetail {

  function __construct() {
  }

  abstract function get_post_type();

  function is_correct_detail_page() {
    global $pagenow, $post;
    $post_type = null;

    if ( !is_admin() ) return false;

    switch($pagenow) {
      case 'post-new.php':
        $post_type = $_GET['post_type'];
      break;
      case 'post.php':
        $post_type = $post->post_type;
      break;
    }

    return ( $post_type == $this->get_post_type() );
  }

}