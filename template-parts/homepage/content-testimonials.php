<?php if ( isset( get_fields( 'options' )[ 'homepage_testimonial' ] ) && get_fields( 'options' )[ 'homepage_testimonial' ] !== false ): ?>
  <?php $testimonials = get_fields( 'options' )[ 'homepage_testimonial' ]; ?>
  <section class="section section-primary py-5">
    <div class="container">

      <h2 class="text-center h3 mb-5">
        <?php _e( 'Komu už jsme pomohli?', '')?>
      </h2>

      <div class="row">
        <?php for ( $i = 0, $len = count( $testimonials ); $i < $len; $i++ ): ?>
          <div class="
            col-12
            col-md-6
            <?php if ( $i == ( $len - 1 ) && $len != 2 ): ?>
              offset-md-3
            <?php endif; ?>
              col-lg-4
            <?php if ( $i == 0 && $len == 1 ): ?>
              offset-lg-4
            <?php elseif ( $i == 0 && $len == 2 ): ?>
              offset-lg-2
            <?php else: ?>
              offset-lg-0
            <?php endif; ?>
            mb-4
          ">

            <div class="testimonial-thumbnail">
              <img
                class="testimonial-thumbnail-image"
                src="<?php echo $testimonials[ $i ][ 'image' ][ "sizes" ][ "thumbnail" ]; ?>"
                alt="<?php echo $testimonials[ $i ][ 'name' ]; ?>"
              >
              <div class="testimonial-thumbnail-body">
                <p class="font-weight-bold mb-0"><?php echo $testimonials[ $i ][ 'name' ]; ?></p>
                <p class="text-muted"><?php echo $testimonials[ $i ][ 'position' ]; ?></p>
                <div><?php echo $testimonials[ $i ][ 'text' ]; ?></div>
              </div>
            </div>

          </div>
        <?php endfor; ?>
      </div>

    </div>
  </section>
<?php endif; ?>
