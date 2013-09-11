$(function () {
  $('#Company_postAddressIsEqual').change(function () {                
     $('.post-address').toggle(!this.checked);
  }).change(); //ensure visible state matches initially
});
