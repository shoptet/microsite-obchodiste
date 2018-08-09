$(function() {

  var $contactForm = $('#wholesalerContactForm');
  var $contactFormError = $('#wholesalerContactFormError');
  var $contactFormSuccess = $('#wholesalerContactFormSuccess');

  var formError = function(text) {
    if (text.length > 0) {
      $contactFormError.removeClass('d-none');
    } else {
      $contactFormError.addClass('d-none');
    }
    $contactFormError.text(text);
  };

  var formSuccess = function(text) {
    $contactFormSuccess.removeClass('d-none').text(text);
  };

  var isEmail = function(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

  var validateForm = function() {
    var isValid = true;
    var value = '';
    var $this = null;
    $contactForm.find('[name]').each(function() {
      $this = $(this);
      value = $.trim($this.val());
      if (!value) {
        formError('Vyplňte prosím všechna pole');
        isValid = false;
        return false;
      }
      if ($this.attr('type') === 'email' && !isEmail(value)) {
        formError('Vyplňte prosím správný e-mail');
        isValid = false;
        return false;
      }
    });

    return isValid;
  };

  var onSuccess = function() {
    $contactForm.find('button[type=submit]').remove();
    formError('');
    formSuccess('Odesláno!');
  };

  var onError = function(xhr) {
    formError('Omlouvám se, ale při odeslání došlo k chybě. Než chybu opravíme, kontaktujte raději obchodníka přímo e-mailem.');
    console.error(xhr);
  };

  var getFormData = function() {
    var data = {};

    var $this = null;
    $contactForm.find('[name]').each(function() {
      $this = $(this);
      data[$this.attr('name')] = $this.val();
    });

    return data;
  };

  var sendData = function(data) {
    $.ajax({
      type: 'POST',
      url: window.ajaxurl,
      data: Object.assign(
        {
          action: 'wholesaler_message',
        },
        data,
      ),
      success: onSuccess,
      error: onError,
      complete: function() {
        $contactForm.removeClass('is-loading');
      },
    });
  };

  $contactForm.on('submit', function(e) {
    e.preventDefault();
    if (!validateForm()) return;
    $contactForm.addClass('is-loading');
    var data = getFormData();
    sendData(data);
  });

});
