$(function() {

  var moveMessages = function () {
    var $messages = $('.message-bottom');
    var $form = $('#loginform, #registerform, #lostpasswordform');
    $($messages.get().reverse()).each(function () {
      $form.after(this);
    })
  };
    
  var registerFormLayout = function () {
    
  };

  moveMessages();
  registerFormLayout();

});