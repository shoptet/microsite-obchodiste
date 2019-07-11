<?php
$external_company_id = $GLOBALS[ 'external_company']['id'];

$wholesaler = get_wholesaler_by_external_company_id( $external_company_id );

$title = ( $wholesaler ? $wholesaler->post_title : $GLOBALS[ 'external_company' ][ 'title' ] );

$acf_form_settings_base = [
  'id' => 'acf-form',
  'post_id' => ( $wholesaler ? $wholesaler->ID : 'new_post' ),
  'new_post' => [
    'post_type' => 'custom',
    'post_status' => 'draft',
    'meta_input' => [
      'external_company_id' => $external_company_id,
      'approving_token' => bin2hex( openssl_random_pseudo_bytes(16) ),
    ],
  ],
  'form' => false,
];

$acf_form_settings = [
  'company' => [
    'fields' => [ 'street', 'city', 'zip', 'country', 'region', 'in', 'website' ],
    'label_placement' => 'left',
    'instruction_placement' => 'field',
  ],
  'contact_person' => [
    'fields' => [ 'contact_full_name', 'contact_email', 'contact_tel' ],
    'label_placement' => 'left',
    'instruction_placement' => 'field',
  ],
  'category' => [
    'fields' => [ 'category', 'minor_category_1', 'minor_category_2' ],
    'label_placement' => 'left',
    'instruction_placement' => 'field',
  ],
  'about' => [
    'fields' => [ 'about_products' ],
  ],
];

array_walk( $acf_form_settings, function ( &$value ) use( &$acf_form_settings_base ) {
  $value = array_merge( $acf_form_settings_base, $value );
} );

$query = new WP_Query( [
  'post_type' => 'custom',
  'post_status' => 'any',
  'meta_query' => [
    [
      'key' => 'external_company_id',
      'value' => $GLOBALS[ 'external_company']['id'],
    ],
  ],
] );

acf_form_head();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <title><?php _e( 'Registrace velkoobchodu – Obchodiště.cz', 'shp-obchodiste' ); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
	</head>

  <body class="operator-form">
    <div class="bg-white border-bottom">
      <div class="container py-3">
        <h1 class="h5 mb-0"><?php _e( 'Registrace velkoobchodu', 'shp-obchodiste' ); ?></h1>
      </div>
    </div>
    <div class="container pt-4 pb-5 mb-2">

      <form
        id="<?php echo $acf_form_settings_base['id']; ?>"
        class="acf-form"
        action=""
        method="post"
        autocomplete="off"
      >
      
        <div class="row mb-4">
          <div class="col-lg-6 mb-4 mb-lg-0">

            <div class="card mb-4 h-100">
              <div class="card-body">
                <h2 class="h4 font-weight-bold"><?php _e( 'Firemní údaje', 'shp-obchodiste' ); ?></h2>

                <div class="acf-fields acf-form-fields -left">
                  <div class="acf-field acf-field-text acf-field--post-title is-required" data-name="_post_title" data-type="text" data-key="_post_title" data-required="1">
                    <div class="acf-label">
                      <label for="acf-_post_title">
                        <?php _e( 'Název', 'shp-obchodiste' ); ?>
                        <span class="acf-required">*</span>
                      </label>
                    </div>
                    <div class="acf-input">
                      <div class="acf-input-wrap">
                        <input class="font-weight-bold" type="text" id="acf-_post_title" name="acf[_post_title]" required="required" value="<?php echo $title; ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <?php acf_form( $acf_form_settings['company'] ); ?>
              </div>
            </div>

          </div>
          <div class="col-lg-6">

            <div class="card mb-4">
              <div class="card-body">
                <h2 class="h4 font-weight-bold"><?php _e( 'Kontaktní osoba', 'shp-obchodiste' ); ?></h2>
                <?php acf_form( $acf_form_settings['contact_person'] ); ?>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <h2 class="h4 font-weight-bold"><?php _e( 'Kategorie podnikání', 'shp-obchodiste' ); ?></h2>
                <?php acf_form( $acf_form_settings['category'] ); ?>
              </div>
            </div>

          </div>
        </div>

        <div class="card operator-form-label-heading">
          <div class="card-body">
            <?php acf_form( $acf_form_settings['about'] ); ?>
          </div>
        </div>

        <div class="text-right pt-4 clearfix">
          <input
            type="submit"
            class="btn btn-primary btn-lg float-right"
            name="operator_form_register_wholesaler"
            value="<?php _e( 'Dokončit registraci firmy', 'shp-obchodiste' ); ?>"
          >
          <?php if ( ! $wholesaler ): ?>
            <input
              type="submit"
              class="btn btn-link btn-lg"
              name="operator_form_notify_contact_person"
              value="<?php _e( 'Odeslat e-mail kontaktní osobě', 'shp-obchodiste' ); ?>"
            >
          <?php endif; ?>
        </div>

      </form>

    </div>

    <?php wp_footer(); ?>

    <div class="d-none">
      <?php echo get_shoptet_footer(); ?>
    </div>

  </body>
</html>