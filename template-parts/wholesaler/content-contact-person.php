<?php if ( get_field( "contact_full_name" ) || get_field( "contact_email" )  || get_field( "contact_tel" ) ): ?>

<h2 class="h-heavy">
  <?php _e( 'Kontaktní osoba', 'shp-obchodiste' ); ?>
</h2>

<div class="border rounded mb-4">

  <?php if ( get_field( "contact_photo" ) ): ?>
  <img
    class="float-left rounded-top-left rounded-bottom-left mr-3"
    src="<?php echo get_field( "contact_photo" )[ "sizes" ][ "thumbnail" ]; ?>"
    <?php if ( get_field( "contact_full_name" ) ): ?>
      alt="<?php echo esc_html( get_field( "contact_full_name" ) ); ?>"
    <?php endif; ?>
    height="105"
    width="105"
    loading="lazy"
  >
  <?php endif; ?>

  <div class="p-3">

    <?php if ( get_field( "contact_full_name" ) ): ?>
    <p class="font-weight-bold text-truncate mb-0">
      <?php echo esc_html( get_field( "contact_full_name" ) ); ?> 
    </p>
    <?php endif; ?>

    <ul class="list-unstyled mb-0">
      <?php if ( get_field( "contact_email" ) ): ?>
      <li class="text-truncate">
        <a href="mailto:<?php echo sanitize_email( get_field( "contact_email" ) ); ?>">
          <?php echo sanitize_email( get_field( "contact_email" ) ); ?> 
        </a>
        <?php if ( is_singular('custom') ): ?><meta itemprop="email" content="<?php echo sanitize_email( get_field( "contact_email" ) ); ?>"><?php endif; ?>
      </li>
      <?php endif; ?>
      <?php if ( get_field( "contact_tel" ) ): ?>
      <li class="text-truncate">
        <a href="tel:<?php echo esc_html( get_field( "contact_tel" ) ); ?>">
          <?php echo esc_html( get_field( "contact_tel" ) ); ?>
        </a>
        <?php if ( is_singular('custom') ): ?><meta itemprop="telephone" content="<?php echo esc_html( get_field( "contact_tel" ) ); ?>"><?php endif; ?>
      </li>
      <?php endif; ?>
    </ul>

  </div>
</div>

<?php endif; ?>
