(function($) {
  $('.read-more').click(function() {
    $(this).toggleClass('d-none');
    $(this).siblings(".body-full").toggleClass('d-none');
    $(this).siblings(".read-less").toggleClass('d-none');
    $(this).siblings(".body-trimmed").toggleClass('d-none');
  });
  $('.read-less').click(function() {
    $(this).toggleClass('d-none');
    $(this).siblings(".body-full").toggleClass('d-none');
    $(this).siblings(".read-more").toggleClass('d-none');
    $(this).siblings(".body-trimmed").toggleClass('d-none');
  });
})(jQuery);