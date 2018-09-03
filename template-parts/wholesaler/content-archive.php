<form class="wholesaler-archive mt-2" method="get" id="archiveForm">

  <?php if ( get_search_query() ): ?>
    <input type="hidden" name="s" value="<?php echo get_search_query(); ?>">
  <?php endif; ?>

  <div class="row">
    <div class="col-12 col-md-4 col-lg-3">

      <?php get_template_part( 'src/template-parts/wholesaler/content', 'archive-filter' ); ?>

    </div>
    <div class="col-12 col-md-8 col-lg-9">

      <div id="archiveList">

        <?php if ( have_posts() ): ?>

        <h1 class="h2 mb-3">
          <?php
          if ( is_tax() ) {
            $taxonomy = get_queried_object();
            echo $taxonomy->name . ' – ';
          }
          _e( 'Velkoobchody', 'shp-obchodiste' );
          ?>
        </h1>

        <?php get_template_part( 'template-parts/utils/content', 'breadcrumb' ); ?>

        <?php get_template_part( 'src/template-parts/wholesaler/content', 'archive-sort' ); ?>

        <div class="row row-bordered no-gutters">
          <?php $loop_index = 0; ?>
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-12 col-lg-6">

              <?php get_template_part( 'src/template-parts/wholesaler/content', 'tease' ); ?>

            </div>

            <?php if ( $loop_index === 5 && get_fields( 'options' )[ 'archive_post' ] ): ?>
              </div>
              <div class="row">
                <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 py-4">
                  <?php get_template_part( 'src/template-parts/post/content', 'banner' ); ?>
                </div>
              </div>
              <div class="row row-bordered no-gutters">
            <?php endif; ?>

            <?php $loop_index++; ?>

          <?php endwhile; ?>
        </div>

        <div class="mt-4">
          <?php
          if ( is_tax() ) {
            echo nl2p( $taxonomy->description );
          } else {
            $options = get_fields( 'options' );
            echo $options[ 'archive_description' ];
          }
          ?>

          <?php get_template_part( 'template-parts/utils/content', 'pagination' ); ?>
        </div>

        <?php else: ?>

        <p class="h3 mb-2">
          <?php _e( 'Nemůžeme najít žádné velkoobchody s těmito požadavky', 'shp-obchodiste' ); ?>
        </p>
        <p>
          <?php _e( 'Zkuste prosím snížit vaše požadavky pomocí filtrů.', 'shp-obchodiste' ); ?>
        </p>

        <?php endif; ?>

      </div>

    </div>
  </div>
</form>
