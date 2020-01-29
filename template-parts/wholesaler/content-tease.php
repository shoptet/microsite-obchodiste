<?php
$logo_url = get_wholesaler_logo_url();
?>

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

    <div class="wholesaler-tease-logo flex-shrink-0 mr-3 <?php if ( ! $logo_url ) echo "wholesaler-tease-logo-empty" ?>">
      <?php if ( $logo_url ): ?>
      <img
        src="<?php echo $logo_url; ?>"
        alt="<?php the_title(); ?>"
        loading="lazy"
      >
      <?php endif; ?>
    </div>

    <div class="mt-1">

      <h3 class="wholesaler-tease-title h5 mb-0">
        <?php echo esc_html( get_the_title() ); ?>
      </h3>

      <?php if ( $project_title = get_field( 'project_title' ) ):  ?>
        <div class="text-muted">(<?php echo esc_html( $project_title ); ?>)</div>
      <?php endif;  ?>

      <ul class="list-comma fs-90 mt-2">
        <?php if ( get_field( "category" ) ): ?>
        <li><?php echo get_field( "category" )->name; ?></li>
        <?php endif; ?>

        <?php if ( get_field( "minor_category_1" ) ): ?>
        <li><?php echo get_field( "minor_category_1" )->name; ?></li>
        <?php endif; ?>

        <?php if ( get_field( "minor_category_2" ) ): ?>
        <li><?php echo get_field( "minor_category_2" )->name; ?></li>
        <?php endif; ?>

        <?php if ( get_field( "region" ) ): ?>
        <li><?php echo get_field( "region" )['label']; ?></li>
        <?php endif; ?>
      </ul>

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
