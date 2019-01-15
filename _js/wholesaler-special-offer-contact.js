$(function() {

  $('[data-special-offer-contact]').on('click', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $contactForm = $('#wholesalerContactForm');
    $('html, body').animate({
      scrollTop: $contactForm.offset().top,
    }, 500, function () {
      $contactForm.find('[name=message]').val(
        'Dobrý den, mám zájem o Vaši akční nabídku „'
        + $this.attr('data-special-offer-contact')
        + '“. Děkuji.'
      ); // TODO: translatable js strings
      $contactForm.find('[name=name]').focus();
    });
  });

});
