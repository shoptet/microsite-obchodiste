<div class="login header-login">
    <?php
    if ( is_front_page() && ! is_user_logged_in() ):
      $admin_wholesaler_url = admin_url( 'post-new.php?post_type=custom' );
      $add_wholesaler_url = is_user_logged_in() ? $admin_wholesaler_url : wp_login_url( $admin_wholesaler_url );
    ?>
      <a href="<?php echo $add_wholesaler_url; ?>" class="btn btn-orange btn-add">
        <i class="fas fa-plus-circle"></i>
        <?php _e( 'Přidat velkoobchod', 'shp-obchodiste' ); ?>
      </a>
    <?php endif; ?>
    <?php if ( is_user_logged_in() ): ?>
      <span class="dropdown">
        <a
          class="align-middle py-2 d-inline-block"
          role="button"
          href="<?php echo admin_url(); ?>"
          id="dropdownMenuButton"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <?php echo wp_get_current_user()->user_email; ?>
          <i class="fas fa-angle-down" aria-hidden="true"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="<?php echo admin_url( 'edit.php?post_type=custom' ); ?>">
            <?php _e( 'Můj velkoobchod', 'shp-obchodiste' ); ?>
          </a>
          <a class="dropdown-item" href="<?php echo admin_url( 'edit.php?post_type=product' ); ?>">
            <?php _e( 'Moje produkty', 'shp-obchodiste' ); ?>
          </a>
          <a class="dropdown-item" href="<?php echo admin_url( 'profile.php' ); ?>">
            <?php _e( 'Můj profil', 'shp-obchodiste' ); ?>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?php echo wp_logout_url(); ?>">
            <?php _e( 'Odhlásit', 'shp-obchodiste' ); ?>
          </a>
        </div>
      </span>
    <?php else: ?>
      <a class="has-icon" href="<?php echo wp_login_url(); ?>">
        <i class="fas fa-user"></i>
        <span><?php _e( 'Přihlášení', 'shp-obchodiste' ); ?></span>
      </a>
    <?php endif; ?>
</div>
