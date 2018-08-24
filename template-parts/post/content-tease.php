<article class="post-thumbnail mb-4 mb-md-0" itemscope itemtype="https://schema.org/Article">

  <meta itemprop="url" content="<?php echo get_permalink(); ?>">
  <meta itemprop="headline" content="<?php the_title(); ?>">
  <div itemprop="author publisher" itemscope itemtype="http://schema.org/Organization">
    <meta itemprop="name" content="<?php echo get_bloginfo( 'name' ); ?>">
  </div>
  <meta itemprop="datePublished" content="<?php the_date('Y-m-d'); ?>">
  <?php if ( has_post_thumbnail() ): ?>
    <meta itemprop="image" content="<?php the_post_thumbnail_url( 'large' ); ?>">
  <?php endif; ?>

  <h3 class="post-thumbnail-title h4 mt-0 mb-3">
    <a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
      <?php the_title(); ?>
    </a>
  </h3>

  <?php if ( has_post_thumbnail() ): ?>
    <a
      class="post-thumbnail-image"
      href="<?php echo get_permalink(); ?>"
      title="<?php the_title(); ?>"
    >
      <div
        style="background-image:url(<?php the_post_thumbnail_url( 'large' ); ?>)"
      >
      </div>
    </a>
  <?php endif; ?>

  <p class="mb-3">
    <?php echo truncate( strip_tags( get_the_content() ), 220 ); ?>
  </p>

  <p class="mb-0">
    <a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
      <?php _e('Celý článek', ''); ?>
    </a>
  </p>

</article>
