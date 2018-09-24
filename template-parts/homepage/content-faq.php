<?php
$options = get_fields( 'options' );
$wholesaler_post = ( isset( $options[ 'homepage_faq_wholesaler_post' ] ) && is_string( $options[ 'homepage_faq_wholesaler_post'] ) ) ? $options[ 'homepage_faq_wholesaler_post'] : '';
$retail_post = ( isset( $options[ 'homepage_faq_retail_post' ] ) && is_string( $options[ 'homepage_faq_retail_post'] ) ) ? $options[ 'homepage_faq_retail_post'] : '';
$wholesaler_anchors = ( isset( $options[ 'homepage_faq_wholesaler_anchors' ] ) && is_array( $options[ 'homepage_faq_wholesaler_anchors'] ) ) ? $options[ 'homepage_faq_wholesaler_anchors'] : [];
$retail_anchors = ( isset( $options[ 'homepage_faq_retail_anchors' ] ) && is_array( $options[ 'homepage_faq_retail_anchors'] ) ) ? $options[ 'homepage_faq_retail_anchors'] : [];
$show_wholesaler_faq = ( ! empty( $wholesaler_post ) && ! empty( $wholesaler_anchors ) );
$show_retail_faq = ( ! empty( $retail_post ) && ! empty( $retail_anchors ) );
?>

<?php if ( $show_wholesaler_faq || $show_retail_faq ): ?>

  <section class="section section-primary section-faq py-5">
    <div class="container">

      <h2 class="text-center h3 mb-5">
        <?php _e( 'Otázky a odpovědi', 'shp-obchodiste' ); ?>
      </h2>

      <div class="row">
        <div class="col-12 col-md-6 col-lg-4 offset-xl-1">
          <?php if ( $show_wholesaler_faq ): ?>
            <p class="h-heavy">
              <?php _e( 'Pro velkoobchody', 'shp-obchodiste' ); ?>
            </p>

            <ul class="fa-ul">
              <?php foreach ( $wholesaler_anchors as $anchor ): ?>
                <li>
                  <span class="fa-li"><i class="far fa-question-circle"></i></span>
                  <a href="<?php echo $wholesaler_post . '#' . $anchor['anchor']; ?>">
                    <?php echo $anchor['title']; ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="col-12 col-md-6 col-lg-4 offset-lg-4 offset-xl-3">
        <?php if ( $show_retail_faq ): ?>
            <p class="h-heavy">
              <?php _e( 'Pro maloobchody', 'shp-obchodiste' ); ?>
            </p>

            <ul class="fa-ul">
              <?php foreach ( $retail_anchors as $anchor ): ?>
                <li>
                  <span class="fa-li"><i class="far fa-question-circle"></i></span>
                  <a href="<?php echo $retail_post . '#' . $anchor['anchor']; ?>">
                    <?php echo $anchor['title']; ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </section>

<?php endif; ?>
