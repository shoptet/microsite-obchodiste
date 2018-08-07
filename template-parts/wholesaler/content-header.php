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

<p class="text-muted">
  <?php _e( 'Kategorie:', '' ); ?>
  <a href="#">
    Kosmetika a parfémy
  </a>
</p>
