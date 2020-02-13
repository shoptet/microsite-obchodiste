$(function() {

  var moveMessages = function () {
    var $messages = $('.message-bottom');
    var $form = $('#loginform, #registerform, #lostpasswordform');
    $($messages.get().reverse()).each(function () {
      $form.after(this);
    })
  };

  var setFooterElHeight = function () {
    var mapEl = document.getElementById('loginfooter');
    mapEl.style.minHeight = ( window.innerHeight - mapEl.offsetTop ) + 'px';
  };

  moveMessages();

  $(window).resize(setFooterElHeight);
  setFooterElHeight();

});