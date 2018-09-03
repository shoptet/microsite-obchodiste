<a
  class="wholesaler-tease"
  href="<?php the_permalink(); ?>"
  title="<?php _e( 'Zobrazit profil', 'shp-obchodiste' ); ?>"
>
  <?php if ( is_post_type_archive( 'custom' ) || is_tax( 'customtaxonomy' ) ): ?>
    <div itemscope itemtype="http://schema.org/Organization">
      <meta itemprop="url" content="<?php the_permalink(); ?>">
      <meta itemprop="name" content="<?php the_title(); ?>">
    </div>
  <?php endif; ?>
  <div class="d-flex">

    <div class="wholesaler-tease-logo flex-shrink-0 mr-3 <?php if ( ! get_field( "logo" ) ) echo "wholesaler-tease-logo-empty" ?>">
      <?php if ( get_field( "logo" ) ): ?>
      <img
        src="<?php echo get_field( "logo" )[ "sizes" ][ "medium" ]; ?>"
        alt="<?php echo the_title(); ?>"
      >
      <?php endif; ?>
    </div>

    <div class="mt-1">

      <h3 class="wholesaler-tease-title h5 mb-2">
        <?php the_title(); ?>
      </h3>

      <p class="fs-90 mb-0">
        <?php $terms = get_the_terms( $post->ID, 'customtaxonomy' ); ?>
        <?php if ( ! empty( $terms ) ):  ?>
          <?php foreach ( $terms as $term ): ?>
          <?php echo $term->name; ?>,
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if ( get_field( "region" ) ): ?>
          <?php echo get_field( "region" )['label']; ?>
        <?php endif; ?>
      </p>

      <?php if ( get_field( "short_about" ) ): ?>
      <p class="wholesaler-tease-description fs-90 fs-lg-100 mt-2 mb-0">
        <?php echo truncate( strip_tags( get_field( "short_about" ) ), 110 ); ?>
      </p>
      <?php endif; ?>

    </div>

  </div>

  <div class="wholesaler-tease-badges">

    <?php if ( get_field( 'is_shoptet' ) ):  ?>
      <span class="badge badge-shoptet badge-small">
        <?php _e( 'Shoptet', 'shp-obchodiste' ); ?>
      </span>
    <?php endif; ?>

    <?php if ( is_post_new() ):  ?>
      <span class="badge badge-new badge-small">
        <?php _e( 'Nové', 'shp-obchodiste' ); ?>
      </span>
    <?php endif; ?>

  </div>

</a>
