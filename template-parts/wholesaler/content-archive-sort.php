<div class="d-lg-flex align-items-end w-100 mt-3 mb-4">
  <div class="form-group mb-0 mr-lg-3 mr-xl-4">
    <label class="mb-1" for="orderSelect">
      <?php _e( 'Seřadit podle:', 'shp-obchodiste' ); ?>
    </label>
    <select class="form-control custom-select" id="orderSelect" name="orderby">
      <?php
      $order_choices = [
        'date_desc' => __( 'Nejnověji přidáno', 'shp-obchodiste' ),
        //'favorite_desc' => __( 'Nejoblíbenější', 'shp-obchodiste' ), // ElasticPress cannot work with clause
        'title_asc' => __( 'Dle jména A-Z', 'shp-obchodiste' ),
        'title_desc' => __( 'Dle jména Z-A', 'shp-obchodiste' ),
      ];
      $selected_orderby = isset( $_GET[ 'orderby' ] ) ? $_GET[ 'orderby' ] : null;
      ?>
      <?php foreach ( $order_choices as $value => $choice ): ?>
        <option
          value="<?php echo $value ?>"
          <?php if ( $selected_orderby == $value ) echo "selected"; ?>
        >
          <?php echo $choice ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mt-3 mt-lg-0" id="archiveFormServices">
    <?php
    $checked_services = ( isset($_GET[ 'services' ]) && is_array($_GET[ 'services' ]) ) ? $_GET[ 'services' ] : [];
    ?>
    <div class="row no-gutters">
    <?php foreach ( get_all_services() as $service ): ?>
      <?php $inputIdAttr = 'filterService' . preg_replace('/\s/', '', $service); // Remove whitespace ?>
      <div class="col-sm-6">
        <div class="custom-control custom-checkbox mr-1 mr-xl-3">
          <input
            class="custom-control-input"
            type="checkbox"
            value="<?php echo $service; ?>"
            id="<?php echo $inputIdAttr; ?>"
            name="services[]"
            <?php if ( in_array ( $service, $checked_services ) ) echo "checked"; ?>
          >
          <label
            class="custom-control-label"
            for="<?php echo $inputIdAttr; ?>"
          >
            <?php echo $service; ?>
          </label>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>
