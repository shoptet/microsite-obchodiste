<div class="sticky pb-2" id="<?php _e( 'kontakt', 'shp-obchodiste' ); ?>">

  <h2 class="h-heavy">
    <?php _e( 'Kontaktovat velkoobchod', 'shp-obchodiste' ); ?>
  </h2>

  <form class="wholesaler-contact" id="wholesalerContactForm">

    <div class="form-group">
      <label class="required-asterisk" for="wholesalerContactFormName">
        <?php _e( 'Vaše jméno', 'shp-obchodiste' ); ?>
      </label>
      <input type="text" name="name" class="form-control" id="wholesalerContactFormName" required>
    </div>

    <div class="form-group">
      <label class="required-asterisk" for="wholesalerContactFormEmail">
        <?php _e( 'Váš e-mail', 'shp-obchodiste' ); ?>
      </label>
      <input type="email" name="email" class="form-control" id="wholesalerContactFormEmail" required>
    </div>

    <div class="form-group">
      <label class="required-asterisk" for="wholesalerContactFormMessage">
        <?php _e( 'Zpráva velkoobchodu', 'shp-obchodiste' ); ?>
      </label>
      <textarea class="form-control" name="message" rows="9" id="wholesalerContactFormMessage" required></textarea>
    </div>

    <input type="hidden" name="wholesaler_id" value="<?php the_ID(); ?>">

    <div class="g-recaptcha mb-3" data-sitekey="<?php echo G_RECAPTCHA_SITE_KEY; ?>"></div>

    <p class="text-danger d-none" id="wholesalerContactFormError"></p>

    <p class="small">
      <?php
      printf(
        __( 'Vložením e-mailu souhlasím s <a href="%s" target="_blank">podmínkami ochrany osobních údajů</a>.', 'shp-obchodiste' ),
        'https://www.shoptet.cz/podminky-ochrany-osobnich-udaju/'
      );
      ?>
    </p>

    <p class="text-success text-center form-control-plaintext font-weight-bold d-none" id="wholesalerContactFormSuccess"></p>

    <button type="submit" class="btn btn-primary btn-block">
      <?php _e( 'Odeslat', 'shp-obchodiste' ); ?>
    </button>

  </form>

</div>
