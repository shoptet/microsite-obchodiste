$(function() {

  // TODO: translatable js strings
  var getContactText = function (itemType, itemTitle) {
    var contactText = '';
    if ( itemType === 'special-offer') {
      contactText = 'Dobrý den,\n\nmám zájem o Vaši akční nabídku „' + itemTitle + '“.\n\nDěkuji.';
    } else if ( itemType === 'product' ) {
      contactText = 'Dobrý den,\n\nmám zájem o Váš produkt „' + itemTitle + '“.\n\nMohli bychom Vás poprosit o bližší informace o odběru tohoto produktu a případné spolupráci?\n\nDěkuji.';
    }
    return contactText;
  };

  $('[data-wholesaler-contact]').on('click', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $contactForm = $('#wholesalerContactForm, #productContactForm');
    $('html, body').animate({
      scrollTop: $contactForm.offset().top,
    }, 500, function () {
      var contactText = getContactText(
        $this.attr('data-wholesaler-contact'),
        $this.attr('data-wholesaler-contact-item')
      );
      $contactForm.find('[name=message]').val(contactText);
      $contactForm.find('[name=name]').focus();
    });
  });

});
