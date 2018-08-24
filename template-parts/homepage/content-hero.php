<div class="hero">
  <div class="container">
    <h1 class="hero-title mb-5">
      <?php _e( 'Nabídka velkoobchodů pro maloobchodní prodejce', '' ); ?>
    </h1>

    <form action="<?php echo get_post_type_archive_link( 'custom' ); ?>" id="heroForm">
      <div class="hero-form">
        <div class="row">
          <div class="col-md-6 mb-4 mb-md-0">

            <div class="d-lg-flex align-items-center">
              <label class="mr-3 mb-lg-0" for="filterCategory">
                <?php _e( 'Kategorie:', '' ); ?>
              </label>
              <div class="w-100">
                <select class="custom-select" name="category[]">
                  <option selected disabled>
                    <?php _e( 'Všechny', '' ); ?>
                  </option>
                  <?php foreach ( get_terms( 'customtaxonomy' ) as $term ): ?>
                  <option value="<?php echo $term->term_id; ?>">
                    <?php echo $term->name; ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

          </div>
          <div class="col-md-6">

            <div class="d-lg-flex align-items-center">
              <label class="mr-3 mb-lg-0" for="filterCategory">
                <?php _e( 'Lokalita:', '' ); ?>
              </label>
              <div class="w-100">
                <select class="custom-select" name="region[]">
                  <option selected disabled>
                    <?php _e( 'Všechny', '' ); ?>
                  </option>
                  <?php foreach ( get_used_regions() as $region ): ?>
                    <option value="<?php echo $region[ 'id' ]; ?>">
                      <?php echo $region[ 'name' ]; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="hero-form-action">
        <button type="submit" class="btn btn-secondary btn-lg">
          <?php _e( 'Vyhledat dodavatele', '' ); ?>
        </button>
      </div>

    </form>

  </div>
</div>
