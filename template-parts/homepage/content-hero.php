<div class="hero">
  <div class="container">
    <h1 class="hero-title mb-5">
      <?php _e( 'Produkty od velkoobchodů pro <span class="ws-nowrap">e-shopové</span> prodejce', 'shp-obchodiste' ); ?>
    </h1>

    <form action="<?php echo get_post_type_archive_link( 'product' ); ?>" id="searchForm" role="search">

      <div class="hero-form">

        <div class="input-group">
          <div class="input-group-prepend">
            <select class="custom-select" name="searchFormPostTypeSelect" id="searchFormPostTypeSelect">
              <option id="searchFormPostTypeSelectProduct" value="product" selected>
                <?php _e( 'Produkty', 'shp-obchodiste' ); ?>
              </option>
              <option id="searchFormPostTypeSelectCustom" value="custom">
                <?php _e( 'Velkoobchody', 'shp-obchodiste' ); ?>
              </option>
            </select>
          </div>
          <input type="text" class="form-control" name="s" placeholder="<?php _e( 'Jaký produkt byste chtěli prodávat?', 'shp-obchodiste' ); ?>">
        </div>

        <div class="mt-2 d-sm-none">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="searchFormPostTypeRadio" id="searchFormPostTypeRadioProduct" value="product" checked>
            <label class="form-check-label" for="searchFormPostTypeRadioProduct">
              <?php _e( 'Produkty', 'shp-obchodiste' ); ?>
            </label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="searchFormPostTypeRadio" id="searchFormPostTypeRadioCustom" value="custom">
            <label class="form-check-label" for="searchFormPostTypeRadioCustom">
              <?php _e( 'Velkoobchody', 'shp-obchodiste' ); ?>
            </label>
          </div>
        </div>

      </div>

      <div class="hero-form-action">
        <button type="submit" class="btn btn-primary btn-lg ws-normal">
          <?php _e( 'Hledat produkt', 'shp-obchodiste' ); ?>
        </button>
      </div>

    </form>

  </div>
</div>
