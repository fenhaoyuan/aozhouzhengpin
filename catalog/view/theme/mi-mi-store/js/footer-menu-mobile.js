$(document).ready(function() {
  $('.footer-menu-mobile div').hide();  

  $('.footer-menu-mobile h3').click(function() {
    $(this).next('div').slideToggle('fast')
    .siblings('div:visible').slideUp('fast');
  });
});
