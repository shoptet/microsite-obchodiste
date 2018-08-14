<div class="d-md-flex justify-content-between">

  <h1 class="mt-1 mb-1" itemprop="name">
    <?php the_title(); ?>
  </h1>

  <div class="ml-md-3 mb-2 mb-md-0">
    <div class="badges">

      <?php if ( is_post_new() ):  ?>
      <span class="badge badge-new">
        <?php _e( 'Nové', '' ); ?>
      </span>
      <?php endif;  ?>

    </div>
  </div>

</div>

<?php $terms = get_the_terms( $post->ID, 'customtaxonomy' ); ?>
<?php if ( ! empty( $terms ) ):  ?>
<p class="text-muted">
  <?php _e( 'Kategorie:', '' ); ?>
  <?php foreach ( $terms as $term ): ?>
  <a href="<?php echo get_term_link( $term ); ?>">
    <?php echo $term->name; ?>
  </a>
  <?php endforeach; ?>
</p>
<?php endif; ?>
