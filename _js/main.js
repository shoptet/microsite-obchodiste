$(function() {

  $('.gallery .colorbox').colorbox({
    rel: 'gallery',
    maxWidth: '98%',
  });
  $('.special-offer-detail .colorbox').colorbox({
    maxWidth: '98%',
  });
  $('.product-gallery .colorbox').colorbox({
    rel: 'product-gallery',
    maxWidth: '98%',
  });

  var carouselItemCount = $('.owl-carousel').children().length;
  $('.owl-carousel').owlCarousel({
    loop: ( carouselItemCount >= 3 ), // do not clone items if items do not fill carousel
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
      0:    { items: 1 },
      576:  { items: 1 },
      768:  { items: 2 },
      992:  { items: 2 },
      1200: { items: 3 },
    }
  });

});
