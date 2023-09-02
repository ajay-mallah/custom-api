(function($) {
  $('.read-more').click(function() {
    $(this).parent().toggleClass('d-none');
    $(this).parent().siblings(".body-full").toggleClass('d-none');
  });
  $('.read-less').click(function() {
    $(this).parent().toggleClass('d-none');
    $(this).parent().siblings(".body-trimmed").toggleClass('d-none');
  });
})(jQuery);