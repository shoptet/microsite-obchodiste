<h2 class="h-heavy">
  <?php _e( 'Kontaktní údaje', 'shp-obchodiste' ); ?>
</h2>

<div class="mb-4 clearfix">

  <?php if ( get_field( "logo" ) ): ?>
  <div class="wholesaler-logo">
    <img
      src="<?php echo get_field( "logo" )[ "sizes" ][ "medium" ]; ?>"
      alt="<?php the_title(); ?>"
      <?php if ( is_singular('custom') ): ?>itemprop="logo"<?php endif; ?>
    >
  </div>
  <?php endif; ?>

<address <?php if ( is_singular('custom') ): ?>itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"<?php endif; ?>>
    <?php if ( ! is_singular( 'custom' ) ): ?>
      <a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
      </a>
    <?php elseif ( get_field( "in" ) ): ?>
      <?php if ( get_field( "country" ) && get_field( "country" )[ 'value' ] == "sk" ): ?>
        <a href="https://finstat.sk/<?php the_field( "in" ); ?>" target="_blank">
          <?php the_title(); ?>
        </a>
      <?php else: ?>
        <a href="https://or.justice.cz/ias/ui/rejstrik-$firma?ico=<?php the_field( "in" ); ?>" target="_blank">
          <?php the_title(); ?>
        </a>
      <?php endif; ?>
    <?php else: ?>
      <span><?php the_title(); ?></span>
    <?php endif; ?>
    <br>
    <?php if ( get_field( "street" ) ): ?>
    <span <?php if ( is_singular('custom') ): ?>itemprop="streetAddress"<?php endif; ?>><?php the_field( "street" ); ?></span>
    <br>
    <?php endif; ?>
    <span <?php if ( is_singular('custom') ): ?>itemprop="streetAddress"<?php endif; ?>><?php the_field( "zip" ); ?></span>
    &nbsp;<span <?php if ( is_singular('custom') ): ?>itemprop="addressLocality"<?php endif; ?>><?php the_field( "city" ); ?></span>
    <?php if ( get_field( "country" ) && get_field( "country" )[ 'label' ] ): ?>
      <br>
      <?php echo get_field( "country" )[ 'label' ]; ?>
    <?php endif; ?>
  </address>

  <dl class="dl-pair-inline">
    <?php if ( get_field( "in" ) ): ?>
    <dt><?php _e( 'IČ', 'shp-obchodiste' ); ?></dt>
    <dd>
      <?php if ( get_field( "country" ) && get_field( "country" )[ 'value' ] == "sk" ): ?>
        <a href="https://finstat.sk/<?php the_field( "in" ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="identifier"<?php endif; ?>>
          <?php the_field( "in" ); ?>
        </a>
      <?php else: ?>
        <a href="http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_res.cgi?odp=html&ICO=<?php the_field( "in" ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="identifier"<?php endif; ?>>
          <?php the_field( "in" ); ?>
        </a>
      <?php endif; ?>
    </dd>
    <?php endif; ?>
    <?php if ( get_field( "tin" ) ): ?>
    <dt><?php _e( 'DIČ', 'shp-obchodiste' ); ?></dt>
    <dd <?php if ( is_singular('custom') ): ?>itemprop="taxID"<?php endif; ?>>

      <?php if ( get_field( "country" ) && get_field( "country" )[ 'value' ] == "sk" ): ?>
        <a href="https://finstat.sk/<?php the_field( "in" ); ?>" target="_blank">
          <?php the_field( "tin" ); ?>
        </a>
      <?php else: ?>
        <?php
        $tin[0] = substr( get_field( "tin" ), 0, 2 ); // Get country code
        $tin[1] = substr( get_field( "tin" ), 2 ); // Get tin number
        ?>
        <form class="d-inline" action="http://ec.europa.eu/taxation_customs/vies/vatResponse.html" method="post" target="_blank">
          <button class="btn btn-link p-0 align-baseline" type="submit">
            <?php the_field( "tin" ); ?>
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
    <a href="<?php the_field( "website" ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="url"<?php endif; ?>>
      <?php echo display_url( get_field( "website" ) ); ?>
    </a>
  </p>
  <?php endif; ?>

  <?php if ( get_field( "facebook" ) || get_field( "twitter" ) || get_field( "instagram" ) ): ?>
  <ul class="list-inline">
    <?php if ( get_field( "facebook" ) ): ?>
    <li class="list-inline-item">
      <a class="link-facebook" href="<?php the_field( "facebook" ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="sameAs"<?php endif; ?>>
        <i class="fab fa-2x fa-facebook-square"></i>
      </a>
    </li>
    <?php endif; ?>
    <?php if ( get_field( "twitter" ) ): ?>
    <li class="list-inline-item">
      <a class="link-twitter" href="<?php the_field( "twitter" ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="sameAs"<?php endif; ?>>
        <i class="fab fa-2x fa-twitter-square"></i>
      </a>
    </li>
    <?php endif; ?>
    <?php if ( get_field( "instagram" ) ): ?>
    <li class="list-inline-item">
      <a class="link-instagram" href="<?php the_field( "instagram" ); ?>" target="_blank" <?php if ( is_singular('custom') ): ?>itemprop="sameAs"<?php endif; ?>>
        <i class="fab fa-2x fa-instagram"></i>
      </a>
    </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>

  <?php if ( get_post_meta( $post->ID, 'location' ) ): ?>
    <div class="wholesaler-map" id="wholesalerMap"></div>
  <?php endif; ?>

</div>
