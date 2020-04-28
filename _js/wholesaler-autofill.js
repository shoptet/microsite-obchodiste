(function($) {

  var enabled = false;
  var $autofill;
  var $button;
  var $error;
  var $autofillContainer;

  function init() {
    $button = $('<input class="button button-primary button-large" type="button">')
    .attr('value', local.button_label)
    .css({
      'margin-top': '1rem',
    });
    $error = $('<div></div>')
      .css({
        'color': '#f00',
        'margin-top': '0.5rem',
      });
    $autofill = $('<div></div>')
      .append($button)
      .append($error);
    $autofillContainer = $('[data-key=field_5b5ecaf4052fb] .acf-input');

    updateAutofillAbility();
    updateButtonAbility();

    $('#acf-field_5bbdc26030686').on('change', updateAutofillAbility);
    $('#acf-field_5b5ecaf4052fb').on('input', updateButtonAbility);
  }

  function fillForm(data) {
    $('#acf-field_5b5ecc9d052fc').val(data.tin);
    $('#acf-field_5b5ec9b4052f8').val(data.street);
    $('#acf-field_5b5eca63052f9').val(data.city);
    $('#acf-field_5b5eca9d052fa').val(data.zip);
    $('#acf-field_5b5ed2ca0a22d').val(data.region);
  };

  function showError(message) {
    if (typeof message === 'string' && message.length > 0) {
      $error.html(message);
    } else {
      $error.html(local.error);
    }
  }

  function clearError() {
    $error.html('');
  }
  
  function handleAutofill() {
    clearError();
    $button.attr('disabled', true);

    var data = {
      'action': 'identification_number_api',
      '_ajax_nonce': settings.nonce,
      'in': $('#acf-field_5b5ecaf4052fb').val(),
      'country': $('#acf-field_5bbdc26030686').val(),
    };
    
    $.post( settings.ajax_url, data )
      .done(function(res) {
        if ( res.success == true ) {
          fillForm(res.data);
        } else {
          showError(res.data);
        }
      })
      .always(function() {
        $button.attr('disabled', false);
      })
      .fail(function() {
        showError();
      });
  }

  function enableAutofill() {
    if (!enabled) {
      clearError();
      $autofillContainer.append($autofill);
      updateButtonAbility();
      $button.on('click', handleAutofill);
      enabled = true;
    }
  }

  function disableAutofill() {
    if (enabled) {
      $autofill.detach();
      $button.off('click', handleAutofill);
      enabled = false;
    }
  }

  function updateAutofillAbility() {
    var country = $('#acf-field_5bbdc26030686').val();
    if ('cz' == country) {
      enableAutofill();
    } else {
      disableAutofill();
    }
  }

  function updateButtonAbility() {
    var inVal = $('#acf-field_5b5ecaf4052fb').val();
    $button.attr('disabled', !inVal.length);
  }
  
  $(init());

})(jQuery);