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
        var $response = $(response);

        $('#archiveList').html($response.find('#archiveList'));
        $archiveForm.removeClass('is-loading');
        initOrderSelect(); // initialize order select change event
        if (moveToTop) $('html, body').animate({scrollTop: $archiveForm.offset().top});

        var $ageTest = $response.find('#ageTest');
        if ($ageTest.length && $('#ageTest').length == 0) {
          $('body').append($ageTest);
          initAgeTest();
        }
      },
    });
  };

  var createUrl = function (data, postType) {
    var categoryCount = 0;
    var lastCategoryId = null;
    var queryString = '';
    var skipSingleCategory = false;
    var skipDefaultOrderBy = false;
    // Count categories
    data.forEach(function (item) {
      if (item.name !== 'category[]' || !item.value) return;
      lastCategoryId = item.value;
      categoryCount++;
    });
    // Remove single category and default ordering
    data = data.filter(function (item) {
      skipSingleCategory = (['custom', 'product'].includes(postType) && categoryCount === 1 && item.name === 'category[]');
      skipDefaultOrderBy = (item.name === 'orderby' && item.value === 'date_desc');
      skipEmptyValue = ( item.value ? false : true );
      return !skipSingleCategory && !skipDefaultOrderBy && !skipEmptyValue;
    });
    // Create query string
    data.forEach(function (item, i) {
      queryString += (i !== 0 ? '&' : '' ) + item.name + '=' + item.value;
    });
    var url = window.archiveUrl[ postType ];
    url += ( (['custom', 'product'].includes(postType) && categoryCount === 1) ? window.terms[ postType ][ lastCategoryId ] + '/' : '' ); // Add category slug
    url += ( queryString.length ? '?' + queryString : '' ); // Add query string
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
    url = createUrl($archiveForm.serializeArray(), $(this).attr('data-post-type') );
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
