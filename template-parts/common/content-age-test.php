<div><?php // jQuery cannot find #ageTest element directly in the root of body ?>
  <div class="modal fade" tabindex="-1" role="dialog" id="ageTest">
    <div class="modal-dialog" role="document">
      <div class="modal-content py-3 px-2">
        <div class="modal-body text-center">
          <p class="fs-150 font-weight-bold text-uppercase">
            <?php _e( 'Pokračováním potvrzuji, že jsem starší 18&nbsp;let', 'shp-obchodiste' ); ?>
          </p>
          <button type="button" class="btn btn-primary btn-lg px-5" id="ageTestButton">
            <?php _e( 'Pokračovat', 'shp-obchodiste' ); ?>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>