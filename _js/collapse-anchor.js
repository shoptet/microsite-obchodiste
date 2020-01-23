$(function() {

  var hash = $(location).attr('hash');
  if ( ! hash ) return;
  
  var $hash = $(hash);
  if ( $hash.length != 1 ) return; 
  
  var $heading = $hash.find('[data-toggle=collapse]');
  if ( $heading.length != 1 ) return;

  var $targetCollapse = $( $heading.attr('data-target') );
  if ( $targetCollapse.length != 1 ) return;

  $targetCollapse.collapse('show');
    
} );