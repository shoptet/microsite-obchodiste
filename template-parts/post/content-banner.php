<?php
$options = get_fields( 'options' );
$post_banner = $options[ 'archive_post' ];
?>

<a class="post-banner" href="<?php the_permalink( $post_banner ); ?>" title="<?php echo $post_banner->post_title; ?>">

  <div class="d-flex align-items-center">

    <?php if ( has_post_thumbnail( $post_banner ) ): ?>
      <div class="post-banner-image mr-3 mr-lg-5" href="<?php the_permalink( $post_banner ); ?>" title="<?php echo $post_banner->post_title; ?>">
        <div style="background-image:url(<?php echo get_the_post_thumbnail_url( $post_banner, 'large' ); ?>)"></div>
      </div>
    <?php endif; ?>

    <p class="post-banner-title h4 mt-0 mb-0">
      <?php echo $post_banner->post_title; ?>
    </p>

  </div>

</a>
