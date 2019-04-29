<?php
$options = get_fields( 'options' );
$post_type = get_post_type_in_archive_or_taxonomy();

$archive_text = [
  'custom' => [
    'title' => __( 'Velkoobchody', 'shp-obchodiste' ),
    'description' => $options[ 'archive_description' ],
    'empty_title' => __( 'Nemůžeme najít žádné velkoobchody s těmito požadavky', 'shp-obchodiste' ),
    'action_button_label' => __( 'Přidat velkoobchod', 'shp-obchodiste' ),
  ],
  'special_offer' => [
    'title' => __( 'Akční nabídka', 'shp-obchodiste' ),
    'description' => $options[ 'archive_special_offer_description' ],
    'empty_title' => __( 'Nemůžeme najít žádné akční nabídky s těmito požadavky', 'shp-obchodiste' ),
    'action_button_label' => __( 'Přidat nabídku', 'shp-obchodiste' ),
  ],
  'product' => [
    'title' => __( 'Produkty', 'shp-obchodiste' ),
    'description' => $options[ 'archive_product_description' ],
    'empty_title' => __( 'Nemůžeme najít žádné produkty s těmito požadavky', 'shp-obchodiste' ),
    'action_button_label' => __( 'Přidat produkt', 'shp-obchodiste' ),
  ],
];

if ( is_tax() ) {
  $taxonomy = get_queried_object();
  $archive_text[$post_type]['title'] = ( $taxonomy ? $taxonomy->name . ' – ' : '' ) . $archive_text[$post_type]['title'];
  $archive_text[$post_type]['description'] = ( $taxonomy ? nl2p( $taxonomy->description ) : $archive_text[$post_type]['description'] );
}

if ( is_paged() ) {
  $archive_text[$post_type]['title'] .= sprintf( __( ', strana %d', 'shp-obchodiste' ), get_query_var('paged') );
}
?>

<form class="wholesaler-archive mt-2" method="get" id="archiveForm" data-post-type="<?php echo $post_type; ?>">

  <?php if ( get_search_query() ): ?>
    <input type="hidden" name="s" value="<?php echo get_search_query(); ?>">
  <?php endif; ?>

  <div class="row">
    <div class="col-12 col-md-4 col-lg-3">

      <?php get_template_part( 'src/template-parts/common/content', 'archive-filter' ); ?>

    </div>
    <div class="col-12 col-md-8 col-lg-9">

      <div id="archiveList">

        <?php if ( have_posts() ): ?>

        <div class="d-md-flex justify-content-between mb-3">
          <h1 class="h2 mb-3 mb-md-0">
            <?php echo $archive_text[$post_type]['title']; ?>
          </h1>
          <div class="ml-md-3">
            <a href="<?php echo admin_url( 'post-new.php?post_type=' . $post_type ); ?>" class="btn btn-orange btn-add">
              <i class="fas fa-plus-circle"></i>
              <?php echo $archive_text[$post_type]['action_button_label']; ?>
            </a>
          </div>
        </div>

        <?php get_template_part( 'template-parts/utils/content', 'breadcrumb' ); ?>

        <?php if ($post_type === 'custom'):?>
        <?php get_template_part( 'src/template-parts/wholesaler/content', 'archive-sort' ); ?>

        <div class="row row-bordered row-bordered-2-columns no-gutters">
          <?php $loop_index = 0; ?>
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-12 col-lg-6">

              <?php get_template_part( 'src/template-parts/wholesaler/content', 'tease' ); ?>

            </div>

            <?php if ( $loop_index === 5 && $options[ 'archive_post' ] ): ?>
              </div>
              <div class="row">
                <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 py-4">
                  <?php get_template_part( 'src/template-parts/post/content', 'banner' ); ?>
                </div>
              </div>
              <div class="row row-bordered row-bordered-2-columns no-gutters">
            <?php endif; ?>

            <?php $loop_index++; ?>

          <?php endwhile; ?>
        </div>
        <?php elseif ($post_type === 'special_offer'): ?>
        <?php get_template_part( 'src/template-parts/common/content', 'archive-sort' ); ?>

        <div class="row row-bordered row-bordered-2-columns no-gutters">
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-12 col-lg-6">

              <?php get_template_part( 'src/template-parts/special_offer/content', 'tease' ); ?>

            </div>

          <?php endwhile; ?>
        </div>
        <?php elseif ($post_type === 'product'): ?>
        <?php get_template_part( 'src/template-parts/common/content', 'archive-sort' ); ?>

        <div class="list-bordered">
          <?php get_template_part( 'src/template-parts/product/content', 'list-header' ); ?>
          
          <?php $GLOBALS[ 'is_product_tease_in_row' ] = true; ?>
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="list-bordered-item">
              <?php get_template_part( 'src/template-parts/product/content', 'tease' ); ?>
            </div>
          <?php endwhile; ?>
          <?php unset( $GLOBALS[ 'is_product_tease_in_row' ] ); ?>
        </div>
        <?php endif; ?>

        <div class="text-muted text-right mt-3">
          <?php
          printf(
            __( 'Celkem %d z %d', 'shp-obchodiste' ),
            $wp_query->post_count,
            $wp_query->found_posts
          );
          ?>
        </div>

        <div class="mt-4">
          <?php echo $archive_text[$post_type]['description']; ?>

          <?php get_template_part( 'template-parts/utils/content', 'pagination' ); ?>
        </div>

        <?php else: ?>

        <p class="h3 mb-2">
          <?php echo $archive_text[$post_type]['archive_empty_title']; ?>
        </p>
        <p>
          <?php _e( 'Zkuste prosím snížit vaše požadavky pomocí filtrů.', 'shp-obchodiste' ); ?>
        </p>

        <?php endif; ?>

      </div>

    </div>
  </div>
</form>
