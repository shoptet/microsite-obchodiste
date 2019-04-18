$(function() {

  var containers = document.querySelectorAll('.block-ellipsis');
  Array.prototype.forEach.call(containers, function (container) {  // Loop through each container
    var p = container.querySelector('p');
    var divh = container.clientHeight;
    while (p.offsetHeight > divh) { // Check if the paragraph's height is taller than the container's height. If it is:
      p.textContent = p.textContent.replace(/\W*\s(\S)*$/, '...'); // add an ellipsis at the last shown space
    }
  });

});
