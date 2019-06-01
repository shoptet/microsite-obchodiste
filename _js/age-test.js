function initAgeTest () {
  if ( localStorage.getItem('ageTest') ) return;
  // Kick off modal window
  $('#ageTest').modal({
    backdrop: 'static',
    keyboard: false,
  });
  // Handle continue button click
  $('#ageTestButton').on('click', function () {
    localStorage.setItem('ageTest', 'true');
    $('#ageTest').modal('hide');
  });
}