$(function() {

  var $formHero = $('form#searchForm');
  var $formHeader = $('form#searchFormHeader');

  $formHero.find('input[name=s]').focus();

  var syncFormControls = function ($elToSync, prop) {
    $elToSync.prop(prop, true);
  };

  var switchFormData = function ($form, postType, switchSubmitButton) {
    var data = window.searchFormData[postType];
    $form.attr('action', data.formAction);
    $form.find('input[name=s]').attr('placeholder', data.searchInputPlaceholder);
    if (switchSubmitButton) {
      $form.find('button[type=submit]').text(data.submitButtonText);
    }
  };

  var submitFormHandler = function () {
    var $this = $(this);
    var query = $this.find('input[name=s]').val();
    window.location = $this.attr('action') + '?s=' + query;
    return false;
  }

  $('[name=searchFormPostTypeSelect]').on('change', function () {
    var value = $(this).val();
    var $postTypeRadio = $('input[name=searchFormPostTypeRadio][value=' + value + ']');
    syncFormControls($postTypeRadio, 'checked');
    switchFormData($formHero, value, true);
  });

  $('[name=searchFormPostTypeRadio]').on('change', function () {
    var value = $(this).val();
    var $postTypeSelect = $('select[name=searchFormPostTypeSelect]').find('option[value=' + value + ']');
    syncFormControls($postTypeSelect, 'selected');
    switchFormData($formHero, value, true);
  });

  $('[name=searchFormHeaderPostTypeSelect]').on('change', function () {
    var $selectedOption = $(this).find(':selected');
    var value = $selectedOption.val();
    var width = $selectedOption.attr('data-width');
    $(this).css('width', width);
    switchFormData($formHeader, value, false);
  });

  $formHero.on('submit', submitFormHandler);
  $formHeader.on('submit', submitFormHandler);
});
