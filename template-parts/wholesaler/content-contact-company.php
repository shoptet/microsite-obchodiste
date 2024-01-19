<h2 class="h-heavy">
  <?php _e( 'Kontaktní údaje', 'shp-obchodiste' ); ?>
</h2>

<div class="mb-4 clearfix">

  <?php if ( $logo_url = get_wholesaler_logo_url() ): ?>
  <div class="wholesaler-logo">
    <img
      src="<?php echo $logo_url; ?>"
      alt="<?php echo esc_html( get_the_title() ); ?>"
      loading="lazy"
      <?php if ( is_singular('custom') ): ?>itemprop="logo"<?php endif; ?>
    >
  </div>
  <?php endif; ?>

  <address <?php if ( is_singular('custom') ): ?>itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"<?php endif; ?>>
    <?php if ( ! is_singular( 'custom' ) ): ?>
      <a href="<?php the_permalink(); ?>">
        <?php echo esc_html( get_the_title() ); ?>
      </a>
    <?php elseif ( get_field( "in" ) ): ?>
      <?php if ( get_field( "country" ) && get_field( "country" )[ 'value' ] == "sk" ): ?>
        <a href="https://finstat.sk/<?php echo esc_html( get_field( "in" ) ); ?>" target="_blank">
          <?php echo esc_html( get_the_title() ); ?>
        </a>
      <?php else: ?>
        <a href="https://or.justice.cz/ias/ui/rejstrik-$firma?ico=<?php echo esc_html( get_field( "in" ) ); ?>" target="_blank">
          <?php echo esc_html( get_the_title() ); ?>
        </a>
      <?php endif; ?>
      <?php if ( $project_title = get_field( 'project_title' ) ):  ?>
        <span class="text-muted">(<?php echo esc_html( $project_title ); ?>)</span>
      <?php endif;  ?>
    <?php else: ?>
      <span><?php echo esc_html( get_the_title() ); ?></span>
    <?php endif; ?>
    <br>
    <?php if ( get_field( "street" ) ): ?>
    <span <?php if ( is_singular('custom') ): ?>itemprop="streetAddress"<?php endif; ?>><?php echo esc_html( get_field( "street" ) ); ?></span>
    <br>
    <?php endif; ?>
    <span <?php if ( is_singular('custom') ): ?>itemprop="streetAddress"<?php endif; ?>><?php echo esc_html( get_field( "zip" ) ); ?></span>
    &nbsp;<span <?php if ( is_singular('custom') ): ?>itemprop="addressLocality"<?php endif; ?>><?php echo esc_html( get_field( "city" ) ); ?></span>
    <?php if ( get_field( "country" ) && get_field( "country" )[ 'label' ] ): ?>
      <br>
      <?php echo get_field( "country" )[ 'label' ]; ?>
    <?php endif; ?>
  </address>

  <dl class="dl-pair-inline">
    <?php if ( get_field( "in" ) ): ?>
    <dt><?php _e( 'IČ', 'shp-obchodiste' ); ?></dt>
    <dd>
      <?php if ( is_singular('custom') ): ?>
        <meta itemprop="identifier" content="<?php echo esc_html( get_field( "in" ) ); ?>">
      <?php endif; ?>
      <?php if ( get_field( "country" ) && get_field( "country" )[ 'value' ] == "sk" ): ?>
        <a href="https://finstat.sk/<?php echo esc_html( get_field( "in" ) ); ?>" target="_blank">
          <?php echo esc_html( get_field( "in" ) ); ?>
        </a>
      <?php else: ?>
        <a href="https://ares.gov.cz/ekonomicke-subjekty?ico=<?php echo esc_html( get_field( "in" ) ); ?>" target="_blank">
          <?php echo esc_html( get_field( "in" ) ); ?>
        </a>
      <?php endif; ?>
    </dd>
    <?php endif; ?>
    <?php if ( get_field( "tin" ) ): ?>
    <dt><?php _e( 'DIČ', 'shp-obchodiste' ); ?></dt>
    <dd>
      <?php if ( is_singular('custom') ): ?>
        <meta itemprop="taxID" content="<?php echo esc_html( get_field( "tin" ) ); ?>">
      <?php endif; ?>
      <?php if ( get_field( "country" ) && get_field( "country" )[ 'value' ] == "sk" ): ?>
        <a href="https://finstat.sk/<?php echo esc_html( get_field( "in" ) ); ?>" target="_blank">
          <?php echo esc_html( get_field( "tin" ) ); ?>
        </a>
      <?php else: ?>
        <?php
        $tin[0] = substr( get_field( "tin" ), 0, 2 ); // Get country code
        $tin[1] = substr( get_field( "tin" ), 2 ); // Get tin number
        ?>
        <form class="d-inline" action="http://ec.europa.eu/taxation_customs/vies/vatResponse.html" method="post" target="_blank">
          <button class="btn btn-link p-0 align-baseline" type="submit">
            <?php echo esc_html( get_field( "tin" ) ); ?>
          </button>
          <input type="hidden" name="memberStateCode" value="<?php echo $tin[ 0 ]; ?>">
          <input type="hidden" name="number" value="<?php echo $tin[ 1 ]; ?>">
        </form>
      <?php endif; ?>

    </dd>
    <?php endif; ?>
  </dl>

  <?php if ( get_field( "website" ) ): ?>
  <p>
    <a href="<?php echo esc_html( ensure_protocol( get_field( "website" ) ) ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="url"<?php endif; ?>>
      <?php echo esc_html( display_url( get_field( "website" ) ) ); ?>
    </a>
  </p>
  <?php endif; ?>

  <?php if ( get_field( "facebook" ) || get_field( "twitter" ) || get_field( "instagram" ) ): ?>
  <ul class="list-inline">
    <?php if ( get_field( "facebook" ) ): ?>
    <li class="list-inline-item">
      <a class="link-facebook" href="<?php echo esc_html( get_field( "facebook" ) ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="sameAs"<?php endif; ?>>
        <i class="fab fa-2x fa-facebook-square"></i>
      </a>
    </li>
    <?php endif; ?>
    <?php if ( get_field( "twitter" ) ): ?>
    <li class="list-inline-item">
      <a class="link-twitter" href="<?php echo esc_html( get_field( "twitter" ) ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="sameAs"<?php endif; ?>>
        <i class="fab fa-2x fa-twitter-square"></i>
      </a>
    </li>
    <?php endif; ?>
    <?php if ( get_field( "instagram" ) ): ?>
    <li class="list-inline-item">
      <a class="link-instagram" href="<?php echo esc_html( get_field( "instagram" ) ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="sameAs"<?php endif; ?>>
        <i class="fab fa-2x fa-instagram"></i>
      </a>
    </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>

  <?php if ( is_premium_wholesaler() ): ?>
    <div class="wholesaler-premium mb-3">
      <i class="fas fa-star mr-2"></i>
      <span><?php _e( 'Doporučený dodavatel', 'shp-obchodiste' ); ?>
    </div>
  <?php endif; ?>

  <?php if ( get_post_meta( $post->ID, 'location' ) ): ?>
    <div class="wholesaler-map" id="wholesalerMap"></div>
  <?php endif; ?>

</div>
