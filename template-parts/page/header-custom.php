<div class="login header-login">
    <a href="<?php echo admin_url( 'post-new.php?post_type=custom' ); ?>" class="btn btn-orange btn-add">
      <i class="fas fa-plus-circle" aria-hidden="true"></i>
      <?php _e( 'Přidat velkoobchod', '' ); ?>
    </a>
    <?php if ( is_user_logged_in() ): ?>
      <span class="dropdown">
        <a
          class="align-middle py-2"
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
            <?php _e( 'Moje velkoobchody', '' ); ?>
          </a>
          <a class="dropdown-item" href="<?php echo admin_url( 'profile.php' ); ?>">
            <?php _e( 'Můj profil', '' ); ?>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?php echo wp_logout_url(); ?>">
            <?php _e( 'Odhlásit', '' ); ?>
          </a>
        </div>
      </span>
    <?php else: ?>
      <a class="has-icon" href="<?php echo wp_login_url(); ?>">
        <i class="fas fa-user"></i>
        <span><?php _e( 'Přihlášení', '' ); ?></span>
      </a>
    <?php endif; ?>
</div>
