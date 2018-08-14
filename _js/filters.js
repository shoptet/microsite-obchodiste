$(function() {

  var $archiveForm = $('#archiveForm');

  var initOrderSelect = function () {
    $('#archiveForm select').on('change', function (e) {
      $archiveForm.submit();
    });
  };

  var sendData = function (url, moveToTop) {
    $archiveForm.addClass('is-loading');
    window.history.pushState(null, document.title, url); // rewrite url adress
    $.ajax({
      url: url,
      success: function (response) {
        $('#archiveList').html($(response).find('#archiveList'));
        $archiveForm.removeClass('is-loading');
        initOrderSelect(); // initialize order select change event
        if (moveToTop) $('html, body').animate({scrollTop: $archiveForm.offset().top});
      },
    });
  };

  var createUrl = function () {
    var formData = $archiveForm.serializeArray();
    var categoryCount = 0;
    var lastCategoryId = null;
    var queryString = '';
    formData.forEach(function (item) {
      if (item.name !== 'category[]') return;
      lastCategoryId = item.value;
      categoryCount++;
    });
    if (categoryCount === 1) {
      formData = formData.filter(function (item) { return item.name !== 'category[]' });
    }
    formData.forEach(function (item, i) {
      queryString += (i !== 0 ? '&' : '' ) + item.name + '=' + item.value;
    });
    var url = window.wholesalerArchiveUrl;
    url += ( categoryCount === 1 ? window.wholesalerTerms[ lastCategoryId ] + '/' : '' );
    url += ( queryString.length ? '?' + queryString : '' );
    return url;
  };

  // Hide filter submit button if javascript is loaded
  $('#filterSubmit').addClass('d-none');

  $archiveForm.on('click', '#archivePagination a', function (e) {
    e.preventDefault();
    var url = $(e.currentTarget).attr('href');
    sendData(url, true);
  });

  $archiveForm.on('submit', function (e) {
    e.preventDefault();
    url = createUrl();
    sendData(url, false);
  });


  $('#archiveForm input[type=checkbox]').on('change', function () {
    $archiveForm.submit();
  });

  // Refresh browser after state popped
  window.onpopstate = function () {
    window.location.href = document.location;
  };

  initOrderSelect();

});
