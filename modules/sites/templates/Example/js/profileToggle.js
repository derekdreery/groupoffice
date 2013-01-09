$(function () {
  $('#addressCheck').change(function () {                
     $('.post-address').toggle(!this.checked);
  }).change(); //ensure visible state matches initially
});
