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
    var skipSingleCategory = false;
    var skipDefaultOrderBy = false;
    formData.forEach(function (item) {
      if (item.name !== 'category[]') return;
      lastCategoryId = item.value;
      categoryCount++;
    });
    formData = formData.filter(function (item) {
      skipSingleCategory = (categoryCount === 1 && item.name === 'category[]');
      skipDefaultOrderBy = (item.name === 'orderby' && item.value === 'date_desc');
      return !skipSingleCategory && !skipDefaultOrderBy;
    });
    formData.forEach(function (item, i) {
      queryString += (i !== 0 ? '&' : '' ) + item.name + '=' + item.value;
    });
    var url = window.wholesalerArchiveUrl;
    url += ( categoryCount === 1 ? window.wholesalerTerms[ lastCategoryId ] + '/' : '' ); // Add category slug
    url += ( queryString.length ? '?' + queryString : '' );
    return url;
  };

  // Hide filter submit button if javascript is loaded
  $('#filterSubmit').addClass('d-none');

  $archiveForm.on('click', '.pagination a', function (e) {
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
