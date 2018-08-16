<a
  class="wholesaler-tease"
  href="<?php the_permalink(); ?>"
  title="<?php _e( 'Zobrazit profil', '' ); ?>"
>
  <div class="d-flex align-items-center">

    <?php if ( get_field( "logo" ) ): ?>
    <div class="wholesaler-tease-logo mr-2">
      <img
        src="<?php echo get_field( "logo" )[ "sizes" ][ "medium" ]; ?>"
        alt="<?php echo the_title(); ?>"
      >
    </div>
    <?php endif; ?>

    <div>

      <h3 class="wholesaler-tease-title h5 mb-1">
        <?php the_title(); ?>
      </h3>

      <?php if ( get_field( "region" ) ): ?>
      <p class="fs-90 mb-0">
        <?php echo get_field( "region" )['label']; ?>
      </p>
      <?php endif; ?>

      <?php $terms = get_the_terms( $post->ID, 'customtaxonomy' ); ?>
      <?php if ( ! empty( $terms ) ):  ?>
      <p class="fs-90 mb-0">
        <?php foreach ( $terms as $term ): ?>
        <?php echo $term->name; ?>
        <?php endforeach; ?>
      </p>
      <?php endif; ?>

    </div>

  </div>

  <?php if ( is_post_new() ):  ?>
  <div class="wholesaler-tease-badges">

    <span class="badge badge-new badge-small">
      <?php _e( 'Nové', '' ); ?>
    </span>

  </div>
  <?php endif;  ?>

  <?php if ( get_field( "short_about" ) ): ?>
  <p class="wholesaler-tease-description mt-2 mb-0">
    <?php echo truncate( strip_tags( get_field( "short_about" ) ), 110 ); ?>
  </p>
  <?php endif; ?>
</a>
