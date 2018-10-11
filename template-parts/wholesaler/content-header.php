<div class="d-md-flex justify-content-between">

  <h1 class="mt-1 mb-1" itemprop="name">
    <?php the_title(); ?>
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

<?php $terms = get_the_terms( $post->ID, 'customtaxonomy' ); ?>
<?php if ( ! empty( $terms ) ):  ?>
<p class="text-muted">
  <?php _e( 'Kategorie:', 'shp-obchodiste' ); ?>
  <?php foreach ( $terms as $term ): ?>
  <a href="<?php echo get_term_link( $term ); ?>">
    <?php echo $term->name; ?>
  </a>
  <?php endforeach; ?>
</p>
<?php endif; ?>
