$(function() {

  var $formHero = $('form#searchForm');
  var $formHeader = $('form#searchFormHeader');

  var syncFormControls = function ($elToSync, prop) {
    $elToSync.prop(prop, true);
  };

  var switchFormData = function ($form, postType) {
    var data = window.searchFormData[postType];
    $form.attr('action', data.formAction);
    $form.find('input[name=s]').attr('placeholder', data.searchInputPlaceholder);
    $form.find('button[type=submit]').text(data.submitButtonText);
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
    switchFormData($formHero, value);
  });

  $('[name=searchFormPostTypeRadio]').on('change', function () {
    var value = $(this).val();
    var $postTypeSelect = $('select[name=searchFormPostTypeSelect]').find('option[value=' + value + ']');
    syncFormControls($postTypeSelect, 'selected');
    switchFormData($formHero, value);
  });

  $('[name=searchFormHeaderPostTypeSelect]').on('change', function () {
    var $selectedOption = $(this).find(':selected');
    var value = $selectedOption.val();
    var width = $selectedOption.attr('data-width');
    var data = window.searchFormData[value];
    $(this).css('width', width);
    $formHeader.attr('action', data.formAction);
  });

  $formHero.on('submit', submitFormHandler);
  $formHeader.on('submit', submitFormHandler);
});
