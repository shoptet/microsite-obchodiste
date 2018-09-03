<?php
$the_query_wholesaler = new WP_Query( [
  'post_type' => 'post',
  'posts_per_page' => -1,
  'category_name' => 'velkoobchody',
] );
?>

<?php
$the_query_retail = new WP_Query( [
  'post_type' => 'post',
  'posts_per_page' => -1,
  'category_name' => 'maloobchody',
] );
?>

<?php if ( $the_query_wholesaler->post_count && $the_query_retail->post_count ): ?>

  <section class="section section-primary section-faq py-5">
    <div class="container">

      <h2 class="text-center h3 mb-5">
        <?php _e( 'Otázky a odpovědi', 'shp-obchodiste' ); ?>
      </h2>

      <div class="row">
        <div class="col-12 col-md-6 col-lg-4 offset-xl-1">
          <p class="h-heavy">
            <?php _e( 'Pro velkoobchody', 'shp-obchodiste' ); ?>
          </p>

          <ul class="fa-ul">
            <?php while ( $the_query_wholesaler->have_posts() ) : $the_query_wholesaler->the_post(); ?>
              <li>
                <span class="fa-li"><i class="far fa-question-circle"></i></span>
                <a href="<?php the_permalink(); ?>">
                  <?php the_title(); ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>

        </div>
        <div class="col-12 col-md-6 col-lg-4 offset-lg-4 offset-xl-3">

          <p class="h-heavy">
            <?php _e( 'Pro maloobchody', 'shp-obchodiste' ); ?>
          </p>

          <ul class="fa-ul">
            <?php while ( $the_query_retail->have_posts() ) : $the_query_retail->the_post(); ?>
              <li>
                <span class="fa-li"><i class="far fa-question-circle"></i></span>
                <a href="<?php the_permalink(); ?>">
                  <?php the_title(); ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>

        </div>
      </div>

    </div>
  </section>

<?php endif; ?>
