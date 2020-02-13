<?php

class LoginScreen {

  static function init() {
    add_action( 'login_enqueue_scripts', [ get_called_class(), 'enqueueScriptsAndStyles' ] );
    add_filter( 'login_message', [ get_called_class(), 'handleHeader' ] );
    add_filter( 'login_message', [ get_called_class(), 'handleRegisterHeader' ] );
    add_filter( 'login_message', [ get_called_class(), 'handleMessages' ] );
    add_action( 'register_form', [ get_called_class(), 'renderRegisterForm' ] );
    add_filter( 'registration_errors', [ get_called_class(), 'handleRegisterFormErrors' ] );
    add_action( 'login_form_register', [ get_called_class(), 'handleRegisterForm' ] );
    add_action( 'login_footer', [ get_called_class(), 'renderRegisterFooter' ] );
    add_filter( 'registration_redirect', [ get_called_class(), 'handleRegisterRedirectURL' ] );
  }

  static function enqueueScriptsAndStyles () {

    wp_enqueue_script( 'jQuery', 'https://code.jquery.com/jquery-3.4.1.slim.min.js' );

    wp_enqueue_script( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js' );

    $fileName = '/src/dist/js/login-screen.js';
    $fileUrl = get_template_directory_uri() . $fileName;
    $filePath = get_template_directory() . $fileName;
    wp_enqueue_script( 'login-screen', $fileUrl, [], filemtime($filePath), true );
    
    $fileName = '/src/dist/css/login-screen.css';
    $fileUrl = get_template_directory_uri() . $fileName;
    $filePath = get_template_directory() . $fileName;
    wp_enqueue_style( 'login-screen', $fileUrl, [], filemtime($filePath), 'all' );

  }

  static function getLogoHTML () {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    $logo_url = '';
    if ( ! isset( $logo[ 0 ] ) ) return;
    $logo_url = esc_url( $logo[ 0 ] );
    $logo_html = '
      <a href="' . get_home_url() . '">
        <img
          src="' . $logo_url . '"
          style="
            display: block;
            margin: 0 auto;
            max-width: 230px;
            width: 100%;
          "
        >
      </a>
    ';
    return $logo_html;
  }

  static function getMessage ( $text ) {
    return '<p class="message message-bottom">' . $text . '</p>';
  }

  static function getAction () {
    $action = ( isset( $_REQUEST[ 'action' ] ) ? $_REQUEST[ 'action' ] : 'login' );
    $success = isset( $_GET[ 'success' ] );
    if ( 'register' == $action && $success ) {
      $action .= '-success';
    }
    return $action;
  }

  static function handleHeader ( $message ) {
    $action = self::getAction();
    if ( 'register' == $action ) return $message;

    $header_html = '<div style="margin-bottom:15px;">' . self::getLogoHTML() . '</div>';
    
    $header_html .= '<p style="margin-bottom:40px;text-align:center;">';
    $header_html .= __( 'Nabídněte svoje produkty maloobchodním prodejcům. Služba Obchodistě je zcela zdarma, bez jakýchkoliv přímých nebo nepřímých poplatků.', 'shp-obchodiste' );
    $header_html .= '</p>';
    
    $header_html .= '<h1 style="margin-bottom:20px">';
    switch ( $action ) {
      case 'login':
        $header_html .= __( 'Přihlášení', 'shp-obchodiste' );
      break;
      case 'lostpassword':
        $header_html .= __( 'Zapomenuté heslo', 'shp-obchodiste' );
      break;
    }
    $header_html .= '</h1>';

    return $header_html . $message;
  }

  static function handleRegisterHeader ( $message ) {
    $action = self::getAction();
    if ( ! in_array( $action, [ 'register', 'register-success' ] ) ) return $message;

    echo '
      <style>
        #backtoblog:not(.footer-backtoblog){ display:none; }
        #login { width: 420px; }
        #login form p { display: none; }
        #login .clear { display:none; }
      </style>

      <div class="row no-gutters">
        <div class="col-sm-6 mb-3">
          ' . self::getLogoHTML() . '
        </div>
        <div class="col-sm-6 mb-3">
          <h1 class="mb-sm-0 text-sm-right font-weight-normal" style="font-size:2rem">' . __( 'Registrace', 'shp-obchodiste' ) . '</h1>
        </div>
      </div>
      <p class="text-center mb-3">
      ' . __( 'Nabídněte svoje produkty maloobchodním prodejcům.<br><strong>Obchodistě je navíc zcela ZDARMA.</strong>', 'shp-obchodiste' ) . '
      </p>
    ';
  }

  static function handleMessages ( $message ) {
    $action = self::getAction();

    switch ( $action ) {
      case 'register':
        $message .= self::getMessage( sprintf( __( 'Pokud již máte vytvořený účet, <a href="%s">přihlašte se</a>', 'shp-obchodiste' ), wp_login_url() ) );
      break;
      case 'login':
        $message .= self::getMessage( sprintf( __( 'Nemáte-li vytvořený účet, nejprve se <a href="%s">registrujte</a>', 'shp-obchodiste' ), wp_registration_url() ) );
        $message .= self::getMessage( sprintf( __( 'Zapomněli jste heslo? <a href="%s">Vygenerujeme Vám nové</a>', 'shp-obchodiste' ), wp_lostpassword_url() ) );
      break;
    }

    return $message;
  }

  // Remove error for username, only show error for email only.
  static function handleRegisterFormErrors ( $wp_error ) {
    if ( isset($wp_error->errors['empty_username']) ) {
      unset( $wp_error->errors['empty_username']) ;
    }
    if ( isset($wp_error->errors['username_exists']) ) {
      unset( $wp_error->errors['username_exists'] );
    }
    return $wp_error;
  }

  // Set email as username
  static function handleRegisterForm () {
    if ( isset($_POST['user_login']) && ! empty($_POST['user_email']) ) {
      $_POST['user_login'] = $_POST['user_email'];
    }
  }

  static function renderRegisterForm () {
    $action = self::getAction();

    switch ( $action ) {
      case 'register':
        echo '
          <div class="row align-items-end no-gutters">
            <div class="col-sm-9 pr-sm-2 mb-3 mb-sm-0">
              <label for="user_email">' . __( 'Email' ) . '</label>
              <input type="email" name="user_email" id="user_email" class="input mb-0" size="25" />
            </div>
            <div class="col-sm-3">
              <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" style="width:100%;height:40px;margin-bottom:0" value="' . __( 'Registrace', 'shp-obchodiste' ) .'" />
            </div>
          </div>
        ';
      break;
      case 'register-success':
        echo '
          <div class="d-flex align-items-center mt-2 pt-1">
            <div class="pr-3 pr-sm-4">
              <i class="fas fa-check-circle" style="color:#A7C957;font-size:40px;"></i>
            </div>
            <div>
              <h2 class="mt-0 mb-1" style="font-size:21px">' . __( 'Registrace hotova', 'shp-obchodiste' ) . '</h2>
              <div style="font-size:14px;">' . __( 'Zkontrolujte svou e-mailovou schránku.<br>Poslali jsme Vám přihlašovací údaje.', 'shp-obchodiste' ) . '</div>
            </div>
          </div>
        ';
      break;
    }
    
  }

  static function getFooterBlock ( $icon_class, $title, $text ) {
    return '
      <div class="d-flex align-items-center" style="color:#3583B3">
        <div class="pr-3 pr-sm-4">
          <i class="' . $icon_class . '" style="font-size:40px;"></i>
        </div>
        <div>
          <h2 class="mt-0 mb-1" style="font-size:21px">' . $title . '</h2>
          <div style="font-size:14px;">' . $text . '</div>
        </div>
      </div>
    ';
  }

  static function renderRegisterFooter () {
    $action = self::getAction();
    if ( ! in_array( $action, [ 'register', 'register-success' ] ) ) return;

    $options = get_fields( 'options' );
    if ( $login_screen_benefits = $options['login-screen-benefits'] ): ?>
      <div class="login-footer" id="loginfooter">
        <div style="max-width:800px;margin: 0 auto; padding: 0 20px">
          <div class="row pt-5">
            <?php foreach( $login_screen_benefits as $benefit ): ?>
              <div class="col-sm-6 mb-5">
                <?php echo self::getFooterBlock( $benefit['icon-class'], $benefit['title'], $benefit['text'] ); ?>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="text-center pt-4 pb-4">
            <div class="footer-backtoblog pb-4" id="backtoblog">
              <a class="d-inline-block" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php printf( _x( '&larr; Back to %s', 'site' ), get_bloginfo( 'title', 'display' ) ); ?>
              </a>
            </div>
          </p>

        </div>
      </div>
    <?php endif;
  }

  static function handleRegisterRedirectURL () {
    return wp_registration_url() . '&success';
  }

}