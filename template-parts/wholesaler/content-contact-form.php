<?php
$post_type  = get_post_type();
$form_id = '';
switch ( $post_type ) {
  case 'custom':
  $form_id = 'wholesalerContactForm';
  break;
  case 'product':
  $form_id = 'productContactForm';
  break;
}
?>

<div class="sticky pb-2">

  <h2 class="h-heavy mb-2">
    <?php _e( 'Kontaktovat velkoobchod', 'shp-obchodiste' ); ?>
  </h2>

  <p>
    <?php _e( 'Máte zájem o&nbsp;dlouhodobou spolupráci s&nbsp;tímto velkoobchodem?', 'shp-obchodiste' ); ?>
  </p>

  <form class="wholesaler-contact" id="<?php echo $form_id; ?>">

    <div id="wholesalerContactFormFields">

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
        <textarea class="form-control" name="message" rows="11" id="wholesalerContactFormMessage" required></textarea>
      </div>

      <input type="hidden" name="post_type" value="<?php echo $post_type; ?>">
      <input type="hidden" name="post_id" value="<?php the_ID(); ?>">

      <div class="g-recaptcha mb-3" data-sitekey="<?php echo G_RECAPTCHA_SITE_KEY; ?>"></div>

      <p class="small">
        <?php
        printf(
          __( 'Vložením e-mailu souhlasím s <a href="%s" target="_blank">podmínkami ochrany osobních údajů</a>.', 'shp-obchodiste' ),
          'https://www.shoptet.cz/podminky-ochrany-osobnich-udaju/'
        );
        ?>
      </p>

      <button type="submit" class="btn btn-primary btn-block">
        <?php _e( 'Odeslat', 'shp-obchodiste' ); ?>
      </button>

    </div>
    
    <p class="text-danger d-none mb-0" id="wholesalerContactFormError"></p>

    <p class="text-success text-center form-control-plaintext font-weight-bold d-none" id="wholesalerContactFormSuccess"></p>

  </form>

</div>
