$(function() {

  $('.gallery .colorbox').colorbox({
    rel: 'gallery',
    maxWidth: '98%',
  });
  $('.special-offer-detail .colorbox').colorbox({
    maxWidth: '98%',
  });
  $('.product-gallery .colorbox').colorbox({
    rel: 'product-gallery',
    maxWidth: '98%',
  });

  // Kick off age test modal window
  if ( ! localStorage.getItem('ageTest') ) {
    $('#ageTest').modal({
      backdrop: 'static',
      keyboard: false,
    });
    $('#ageTestButton').on('click', function () {
      localStorage.setItem('ageTest', 'true');
      $('#ageTest').modal('hide');
    });
  }

});
