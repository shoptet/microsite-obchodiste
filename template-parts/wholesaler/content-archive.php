<form class="mt-2" method="get" id="archiveForm">

  <?php if ( get_search_query() ): ?>
    <input type="hidden" name="q" value="<?php echo get_search_query(); ?>">
  <?php endif; ?>

  <div class="row">
    <div class="col-12 col-md-4 col-lg-3">

      <?php get_template_part( 'src/template-parts/wholesaler/content', 'archive-filter' ); ?>

    </div>
    <div class="col-12 col-md-8 col-lg-9">

      <div id="archiveList">

        <h1 class="h2 mb-4">
          <?php
          if ( is_tax() ) {
            $taxonomy = get_queried_object();
            echo $taxonomy->name . ' – ';
          }
          _e( 'Velkoobchody', '' );
          ?>
        </h1>

        <?php get_template_part( 'src/template-parts/wholesaler/content', 'archive-sort' ); ?>

        <div class="row row-bordered no-gutters">
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-12 col-lg-6">

              <?php get_template_part( 'src/template-parts/wholesaler/content', 'tease' ); ?>

            </div>
          <?php endwhile; ?>
        </div>

        <div class="mt-4">
          <?php
          if ( is_tax() ) {
            echo nl2p( $taxonomy->description );
          } else {
            // TODO: Add general description
          }
          ?>

          <?php get_template_part( 'template-parts/utils/content', 'pagination' ); ?>
        </div>

      </div>

    </div>
  </div>
</form>
