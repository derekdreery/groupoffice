<form data-type="GO.core.loginForm" id="goLoginForm" role="form" action="<?php echo GO::url("auth/login"); ?>" method="POST">
  <div class="form-goup">
    <label for="username">Username</label>
    <input type="text" class="form-control" name="username" placeholder="Enter username">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" name="password" placeholder="Password">
  </div>
  <div class="checkbox">
    <label data-on-mouseover="MyFancyTooltip">
      <input type="checkbox"> Remember
    </label>
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
	<button data-on-click="lostPassword" type="button" class="btn btn-default">Lost password?</button>
</form>


GO.core.loginForm = function(form){
	
	submit : function(){
	
	}
	
	lostPassword : function(button){
		
	}
	
}

<?php
$script = <<<END

$( "form" ).submit(function( event ) {
  event.preventDefault();
	$.ajax({
		url:this.action, 
		data: $('#goLoginForm').serialize(),
		dataType:'json',
		success: function(data, textStatus, jqXHR){
				if(data.success){
					document.location=GO.baseUrl;
				}else
				{
					GO.message.alert(data.feedback);
				}
		}
	})
});
END;

GO::scripts()->registerScript('submit', $script);

