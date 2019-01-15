<form class="wholesaler-archive mt-2" method="get" id="archiveForm" data-post-type="special_offer">

  <div class="row">
    <div class="col-12 col-md-4 col-lg-3">

      <?php get_template_part( 'src/template-parts/special_offer/content', 'archive-filter' ); ?>

    </div>
    <div class="col-12 col-md-8 col-lg-9">

      <div id="archiveList">

        <?php if ( have_posts() ): ?>

        <div class="d-md-flex justify-content-between mb-3">
          <h1 class="h2 mb-3 mb-md-0">
            <?php _e( 'Akční nabídka', 'shp-obchodiste' ); ?>
          </h1>
          <div class="ml-md-3">
            <a href="<?php echo admin_url( 'post-new.php?post_type=special_offer' ); ?>" class="btn btn-orange btn-add">
              <i class="fas fa-plus-circle"></i>
              <?php _e( 'Přidat nabídku', 'shp-obchodiste' ); ?>
            </a>
          </div>
        </div>

        <?php get_template_part( 'template-parts/utils/content', 'breadcrumb' ); ?>

        <?php get_template_part( 'src/template-parts/special_offer/content', 'archive-sort' ); ?>

        <div class="row row-bordered no-gutters">
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-12 col-lg-6">

              <?php get_template_part( 'src/template-parts/special_offer/content', 'tease' ); ?>

            </div>

          <?php endwhile; ?>
        </div>

        <div class="mt-4">
          <?php
          $options = get_fields( 'options' );
          echo $options[ 'archive_special_offer_description' ];
          ?>

          <?php get_template_part( 'template-parts/utils/content', 'pagination' ); ?>
        </div>

        <?php else: ?>

        <p class="h3 mb-2">
          <?php _e( 'Nemůžeme najít žádné akční nabídky s těmito požadavky', 'shp-obchodiste' ); ?>
        </p>
        <p>
          <?php _e( 'Zkuste prosím snížit vaše požadavky pomocí filtrů.', 'shp-obchodiste' ); ?>
        </p>

        <?php endif; ?>

      </div>

    </div>
  </div>
</form>
