<?php
$post_type = $wp_query->get( 'post_type' );
$options = get_fields( 'options' );

switch ( $post_type ) {
  case 'special_offer':
  $archive_title = __( 'Akční nabídka', 'shp-obchodiste' );
  $archive_description = $options[ 'archive_special_offer_description' ];
  $archive_empty_title = __( 'Nemůžeme najít žádné akční nabídky s těmito požadavky', 'shp-obchodiste' );
  $action_button_label = __( 'Přidat nabídku', 'shp-obchodiste' );
  break;
  case 'product':
  $archive_title = __( 'Produkty', 'shp-obchodiste' );
  $archive_description = $options[ 'archive_product_description' ];
  $archive_empty_title = __( 'Nemůžeme najít žádné produkty s těmito požadavky', 'shp-obchodiste' );
  $action_button_label = __( 'Přidat produkt', 'shp-obchodiste' );
  break;
}
?>

<form class="wholesaler-archive mt-2" method="get" id="archiveForm" data-post-type="<?php echo $post_type; ?>">

  <div class="row">
    <div class="col-12 col-md-4 col-lg-3">

      <?php get_template_part( 'src/template-parts/common/content', 'archive-filter' ); ?>

    </div>
    <div class="col-12 col-md-8 col-lg-9">

      <div id="archiveList">

        <?php if ( have_posts() ): ?>

        <div class="d-md-flex justify-content-between mb-3">
          <h1 class="h2 mb-3 mb-md-0"><?php echo $archive_title; ?></h1>
          <div class="ml-md-3">
            <a href="<?php echo admin_url( 'post-new.php?post_type=' . $post_type ); ?>" class="btn btn-orange btn-add">
              <i class="fas fa-plus-circle"></i>
              <?php echo $action_button_label; ?>
            </a>
          </div>
        </div>

        <?php get_template_part( 'template-parts/utils/content', 'breadcrumb' ); ?>

        <?php get_template_part( 'src/template-parts/common/content', 'archive-sort' ); ?>

        <?php if ($post_type === 'special_offer'): ?>
        <div class="row row-bordered no-gutters">
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-12 col-lg-6">

              <?php get_template_part( 'src/template-parts/special_offer/content', 'tease' ); ?>

            </div>

          <?php endwhile; ?>
        </div>
        <?php elseif ($post_type === 'product'): ?>
        <div class="list-bordered">
          <div class="list-bordered-item d-none d-lg-block bg-light font-weight-bold">
            <div class="d-flex">
              <div class="flex-shrink-0 mr-3">
                <div class="product-tease-image-placeholder"></div>
              </div>
              <div class="row w-100">
                <div class="col-12 col-lg-6 py-2">
                  <?php _e( 'Název a popis produktu', 'shp-obchodiste' ); ?>
                </div>
                <div class="col-12 col-lg-3 py-2 text-center">
                  <?php _e( 'Velkoobchod', 'shp-obchodiste' ); ?>
                </div>
                <div class="col-12 col-lg-3 py-2 text-right">
                  <?php _e( 'Cena', 'shp-obchodiste' ); ?>
                </div>
              </div>
            </div>
          </div>
          <?php $GLOBALS[ 'is_product_tease_in_row' ] = true; ?>
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="list-bordered-item">
              <?php get_template_part( 'src/template-parts/product/content', 'tease' ); ?>
            </div>
          <?php endwhile; ?>
          <?php unset( $GLOBALS[ 'is_product_tease_in_row' ] ); ?>
        </div>
        <?php endif; ?>

        <div class="mt-4">
          <?php echo $archive_description; ?>

          <?php get_template_part( 'template-parts/utils/content', 'pagination' ); ?>
        </div>

        <?php else: ?>

        <p class="h3 mb-2">
          <?php echo $archive_empty_title; ?>
        </p>
        <p>
          <?php _e( 'Zkuste prosím snížit vaše požadavky pomocí filtrů.', 'shp-obchodiste' ); ?>
        </p>

        <?php endif; ?>

      </div>

    </div>
  </div>
</form>
