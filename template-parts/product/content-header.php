<div class="row mt-3">
  <div class="col-md-7 col-lg-12 col-xl-7 order-md-1 order-lg-0 order-xl-1 mb-4">

    <meta itemprop="url" content="<?php the_permalink(); ?>">

    <h1 class="h2 mb-1" itemprop="name">
      <?php the_title(); ?>
    </h1>

    <?php if ( get_field( "category" ) ): ?>
    <dl class="dl-inline">
      <dt class="text-muted mr-1">
        <?php _e( 'Kategorie:', 'shp-obchodiste' ); ?>
      </dt>
      <dd>
        <a href="<?php echo get_term_link( get_field( "category" ) ); ?>"><?php echo get_field( "category" )->name; ?></a>
      </dd>
    </dl>
    <?php endif; ?>

    <?php if ( $short_description = get_field( "short_description" ) ): ?>
    <meta itemprop="description" content="<?php echo strip_tags( $short_description ); ?>">
    <p><?php echo strip_tags( $short_description ); ?></p>
    <?php endif; ?>

    <div class="product-price-block d-flex justify-content-between align-items-center">
      <div>

        <?php if ( $price = get_field( "price" ) ): ?>
        <dl class="dl-pair-inline mb-0" itemprop="offers" itemscope itemtype="https://schema.org/Offer">

          <dt class="text-muted"><?php _e( 'Cena', 'shp-obchodiste' ); ?></dt>
          <dd>
            <meta itemprop="price" content="<?php echo $price; ?>">
            <meta itemprop="priceCurrency" content="CZK">
            <meta itemprop="url" content="<?php the_permalink(); ?>">
            <span class="fs-150 font-weight-bold"><?php echo separate_thousands( $price, true ); ?></span>
            <?php _e( '<span class="font-weight-bold">Kč</span>&nbsp;/&nbsp;ks', 'shp-obchodiste' ); ?>
          </dd>

          <?php if ( $minimal_order = get_field( "minimal_order" ) ): ?>
          <dt class="text-muted"><?php _e( 'Minimální objednávka', 'shp-obchodiste' ); ?></dt>
          <dd class="font-weight-bold">
            <?php echo separate_thousands( $minimal_order ); ?>
            <?php _e( 'ks', 'shp-obchodiste' ); ?>
          </dd>
          <?php endif; ?>

        </dl>
        <?php else: ?>
        <p class="mb-0 fs-110 font-weight-bold">
          <?php _e( 'Cena na vyžádání', 'shp-obchodiste' ); ?>
        </p>
        <?php endif; ?>

      </div>
      <div class="ml-2">
        <a
          href="#productContactForm"
          class="btn btn-primary"
          data-wholesaler-contact="product"
          data-wholesaler-contact-item="<?php the_title(); ?>"
          role="button"
        >
          <?php _e( 'Mám zájem', 'shp-obchodiste' ); ?>
        </a>
      </div>
    </div>

    <div class="mt-2 small text-right">
      <a
        class="text-muted"
        href="mailto:info@obchodiste.cz?subject=<?php _e( 'Hlašení nelegálního obsahu', 'shp-obchodiste' ); ?>&body=<?php echo get_permalink(); ?>"
      >
        <?php _e( 'Hlašení nelegálního obsahu', 'shp-obchodiste' ); ?>
      </a>
    </div>

  </div>
  <div class="col-md-5 col-lg-12 col-xl-5 mb-4">

    <div class="product-gallery">
      <?php if ( $thumbnail = get_field( "thumbnail" ) ): ?>
      <meta itemprop="image" content="<?php echo $thumbnail[ "sizes" ][ "large" ]; ?>">
      <a class="d-block colorbox" href="<?php echo $thumbnail[ "sizes" ][ "large" ]; ?>">
        <img
          class="product-image"
          src="<?php echo $thumbnail[ "sizes" ][ "product-thumb" ]; ?>"
          alt="<?php echo the_title(); ?>"
        >
      </a>
      <?php endif; ?>

      <?php if ( $gallery = get_field( "gallery" ) ): ?>
      <ul class="gallery gallery-small mt-3">
        <?php foreach ( $gallery as $image ): ?>
        <li>
          <a class="colorbox" href="<?php echo $image[ "sizes" ][ "large" ]; ?>">
            <img
              src="<?php echo $image[ "sizes" ][ "medium" ]; ?>"
              alt="<?php echo $image[ "alt" ]; ?>"
            >
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>

  </div>
</div>