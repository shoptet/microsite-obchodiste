<div class="row mt-3 mb-3">
  <div class="col-md-6 col-lg-12 col-xl-6">

    <div class="product-gallery mb-3">
      <a class="d-block colorbox" href="<?php echo get_field( "thumbnail" )[ "sizes" ][ "large" ]; ?>">
        <img
          class="w-100"
          src="<?php echo get_field( "thumbnail" )[ "sizes" ][ "medium" ]; ?>"
          alt="<?php echo the_title(); ?>"
        >
      </a>

      <?php if ( get_field( "gallery" ) ): ?>
      <ul class="gallery gallery-small mt-2" itemscope itemtype="http://schema.org/ImageGallery">
        <?php foreach ( get_field( "gallery" ) as $image ): ?>
        <li itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
          <a class="colorbox" href="<?php echo $image[ "sizes" ][ "large" ]; ?>" itemprop="contentUrl">
            <img
              src="<?php echo $image[ "sizes" ][ "medium" ]; ?>"
              alt="<?php echo $image[ "alt" ]; ?>"
              itemprop="thumbnail"
            >
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>

  </div>
  <div class="col-md-6 col-lg-12 col-xl-6">

    <h1 class="h2 mt-1 mb-1" itemprop="name">
      <?php the_title(); ?>
    </h1>

    <?php
      if ( $related_wholesaler = get_field( "related_wholesaler" ) ):
      // Set global post variable to related wholesaler post
      global $post; 
      $post = get_post( $related_wholesaler->ID );
      setup_postdata( $post );
    ?>
    <dl class="dl-inline">
      <dt class="text-muted mr-1">
        <?php _e( 'Kategorie:', 'shp-obchodiste' ); ?>
      </dt>
      <dd>
        <ul class="list-comma">

          <?php if ( get_field( "category" ) ):  ?>
          <li><strong><a href="<?php echo get_archive_category_link( 'product', get_field( "category" ) ); ?>" title="<?php _e( 'Hlavní kategorie', 'shp-obchodiste' ); ?>"><?php echo get_field( "category" )->name; ?></a></strong></li>
          <?php endif; ?>

          <?php if ( get_field( "minor_category_1" ) ):  ?>
          <li><a href="<?php echo get_archive_category_link( 'product', get_field( "minor_category_1" ) ); ?>"><?php echo get_field( "minor_category_1" )->name; ?></a></li>
          <?php endif; ?>

          <?php if ( get_field( "minor_category_2" ) ):  ?>
          <li><a href="<?php echo get_archive_category_link( 'product', get_field( "minor_category_2" ) ); ?>"><?php echo get_field( "minor_category_2" )->name; ?></a></li>
          <?php endif; ?>

        </ul>
      </dd>
    </dl>
    <?php
      wp_reset_postdata();
      endif;
    ?>

    <?php if ( get_field( "short_description" ) ): ?>
    <p><?php echo get_field( "short_description" ); ?></p>
    <?php endif; ?>

    <?php if ( get_field( "price" ) || get_field( "minimal_order" ) ): ?>
    <dl class="dl-pair-inline">

      <?php if ( $price = get_field( "price" ) ): ?>
      <dt class="text-muted"><?php _e( 'Cena', 'shp-obchodiste' ); ?></dt>
      <dd class="font-weight-bold">
        <span class="fs-150"><?php echo separate_thousands( $price ); ?></span>
        <?php _e( 'Kč', 'shp-obchodiste' ); ?>
      </dd>
      <?php endif; ?>

      <?php if ( $minimal_order = get_field( "minimal_order" ) ): ?>
      <dt class="text-muted"><?php _e( 'Minimální objednávka', 'shp-obchodiste' ); ?></dt>
      <dd class="font-weight-bold">
        <?php echo separate_thousands( $minimal_order ); ?>
        <?php _e( 'ks', 'shp-obchodiste' ); ?>
      </dd>
      <?php endif; ?>

    </dl>
    <?php endif; ?>

  </div>
</div>