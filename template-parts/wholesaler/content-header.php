<div class="d-md-flex justify-content-between">

  <h1 class="mt-1 mb-1" itemprop="name">
    <?php echo esc_html( get_the_title() ); ?>
    <?php if ( $project_title = get_field( 'project_title' ) ):  ?>
      <span class="text-muted">(<?php echo esc_html(  $project_title ); ?>)</span>
    <?php endif;  ?>
  </h1>

  <div class="ml-md-3 mb-2 mb-md-0">
    <div class="wholesaler-badges">

      <?php if ( get_field( 'is_shoptet' ) ):  ?>
      <span class="badge badge-shoptet">
        <?php _e( 'Shoptet', 'shp-obchodiste' ); ?>
      </span>
      <?php endif;  ?>

      <?php if ( is_post_new() ):  ?>
      <span class="badge badge-new">
        <?php _e( 'Nové', 'shp-obchodiste' ); ?>
      </span>
      <?php endif;  ?>

    </div>
  </div>

</div>

<?php if ( get_field( "website" ) ): ?>
<p class="mb-2">
  <a href="<?php the_field( "website" ); ?>" target="_blank" itemprop="url">
    <?php echo display_url( get_field( "website" ) ); ?>
  </a>
</p>
<?php endif; ?>

<dl class="dl-inline">
  <dt class="text-muted mr-1">
    <?php _e( 'Kategorie:', 'shp-obchodiste' ); ?>
  </dt>
  <dd>
    <ul class="list-comma">
      <?php if ( get_field( "category" ) ):  ?>
      <li><strong><a href="<?php echo get_term_link( get_field( "category" ) ); ?>" title="<?php _e( 'Hlavní kategorie', 'shp-obchodiste' ); ?>"><?php echo get_field( "category" )->name; ?></a></strong></li>
      <?php endif; ?>

      <?php if ( get_field( "minor_category_1" ) ):  ?>
      <li><a href="<?php echo get_term_link( get_field( "minor_category_1" ) ); ?>"><?php echo get_field( "minor_category_1" )->name; ?></a></li>
      <?php endif; ?>

      <?php if ( get_field( "minor_category_2" ) ):  ?>
      <li><a href="<?php echo get_term_link( get_field( "minor_category_2" ) ); ?>"><?php echo get_field( "minor_category_2" )->name; ?></a></li>
      <?php endif; ?>
    </ul>
  </dd>
</dl>
