<?php if ( isset( get_fields( 'options' )[ 'homepage_posts_ids' ] ) && is_array( get_fields( 'options' )[ 'homepage_posts_ids' ] ) ): ?>
  <?php
  $the_query = new WP_Query( [
    'post_type' => [ 'post', 'page' ],
    'posts_per_page' => 2,
    'post__in' => get_fields( 'options' )[ 'homepage_posts_ids' ],
  ] );
  ?>
  <section class="section section-tertiary py-5">
    <div class="container">

      <div class="row">
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <div class="col-12 col-md-6">
            <?php get_template_part( 'src/template-parts/post/content', 'tease' ); ?>
          </div>
        <?php endwhile; ?>
      </div>

    </div>
  </section>
<?php endif; ?>
