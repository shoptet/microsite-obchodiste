$(function() {

  $('.block-ellipsis').each(function () {
    var $this = $(this);
    var $p = $this.find('p');
    var blockHeight = $this.innerHeight();
    while ($p[0].scrollHeight > blockHeight) { // Check if the paragraph's height is taller than the container's height. If it is:
      $p.text($p.text().replace(/\W*\s(\S)*$/, '...')); // add an ellipsis at the last shown space
    }
  });

});
