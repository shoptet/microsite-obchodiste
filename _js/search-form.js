$(function() {

  var $form = $('form#searchForm');

  var syncFormControls = function ($elToSync, prop) {
    $elToSync.prop(prop, true);
  };

  var switchFormData = function (postType) {
    var data = window.searchFormData[postType];
    $form.attr('action', data.formAction);
    $form.find('input[name=s]').attr('placeholder', data.searchInputPlaceholder);
    $form.find('button[type=submit]').text(data.submitButtonText);
  };

  $('[name=searchFormPostTypeSelect]').on('change', function () {
    var value = $(this).val();
    var $postTypeRadio = $('input[name=searchFormPostTypeRadio][value=' + value + ']');
    syncFormControls($postTypeRadio, 'checked');
    switchFormData(value);
  });

  $('[name=searchFormPostTypeRadio]').on('change', function () {
    var value = $(this).val();
    var $postTypeSelect = $('select[name=searchFormPostTypeSelect]').find('option[value=' + value + ']');
    syncFormControls($postTypeSelect, 'selected');
    switchFormData(value);
  });

  $form.on('submit', function () {
    var query = $form.find('input[name=s]').val();
    window.location = $form.attr('action') + '?s=' + query;
    return false;
  });

});
