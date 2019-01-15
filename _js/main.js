$(function() {

  $('.gallery .colorbox').colorbox({
    rel: 'gallery',
    maxWidth: '98%',
  });
  $('.special-offer-detail .colorbox').colorbox({
    maxWidth: '98%',
  });

  $('.owl-carousel').owlCarousel({
    nav: true,
    dots: false,
    responsive: {
      0:    { items: 1 },
      576:  { items: 1 },
      768:  { items: 2 },
      992:  { items: 2 },
      1200: { items: 3 },
    }
  });

});
