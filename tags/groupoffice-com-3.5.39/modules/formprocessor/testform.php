<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Test form</title>
<style>
body{
	font: 12px arial;
}
input, textarea{
	width:250px;
}
</style>
</head>
<body>
							
						
	<form method="POST" action="submit.php" enctype="multipart/form-data">
	
	<input type="hidden" name="return_to" value="<?php echo $_SERVER['PHP_SELF']; ?>" />
	<input type="hidden" name="addressbook" value="Klanten" />
	<!-- <input type="hidden" name="mailings[]" value="test" /> -->
	<!-- <input type="hidden" name="mailings[]" value="blabla" /> -->
	<!--
	Enable this input to send an e-mail confirmation. 

	The path is relative to the directory where your config.php file is.


	<input type="hidden" name="confirmation_email" value="confirm.eml" />
	-->

	<input type="hidden" name="notify_users" value="1" />
	
	<input type="hidden" name="notify_addressbook_owner" value="0" />
	
	<?php 
	if(isset($_REQUEST['feedback']))
	{
		echo '<p style="color:red">'.$_REQUEST['feedback'].'</p>';
	}
	
	if(isset($_POST['submitted']))
	{
		echo '<p>You submitted:</p>';
		
		echo nl2br(var_export($_POST, true));
	}
	
	?>


	<table class="formulier" cellpadding="1" cellspacing="2">
	<tr>
		<td class="label">E-mail *</td>
		<td>: <input class="textbox" type="" name="email" value="mschering@intermesh.nl"  /><input type="hidden" name="required[]" value="email" /></td>

	</tr>
	<tr>
		<td class="label">Company</td>
		<td>: <input class="textbox" type="" name="company" value="Intermesh"  /></td>
	</tr>
	<tr>
		<td class="label">Function</td>
		<td>: <input class="textbox" type="" name="function" value=""  /></td>
	</tr>
	<tr>
		<td class="label">Salutation</td>
		<td>:
		<label for="id_1000">
		<input type="radio" name="sex" value="M" id="id_1000" checked="checked" />Mr.
		</label>
		<label for="id_1001">
		<input type="radio" name="sex" value="F" id="id_1001" />Mrs.
		</label>

		</td>
	</tr>
	<tr>
		<td class="label">First name *</td>
		<td>: <input class="textbox" type="" name="first_name" value="Merijn"  /><input type="hidden" name="required[]" value="first_name" /></td>
	</tr>
	<tr>
		<td class="label">Middle name</td>

		<td>: <input class="textbox" type="" name="middle_name" value=""  /><input type="hidden" name="required[]" value="last_name" /></td>
	</tr>
	<tr>
		<td class="label">Last name *</td>

		<td>: <input class="textbox" type="" name="last_name" value="Schering"  /><input type="hidden" name="required[]" value="last_name" /></td>
	</tr>
	<tr>
		<td class="label">Phone</td>
		<td>: <input class="textbox" type="" name="home_phone" value=""  /></td>
	</tr>
	<tr>
		<td style="vertical-align:top" class="label">Comments</td>

		<td style="vertical-align:top">: <textarea class="textbox" name="comment[Opmerking]" ></textarea></td>
	</tr>
	<tr>
		<td class="label">File attachment</td>
		<td>: <input type="file" name="attachment" /></td>
	</tr>
	<tr>
		<td colspan="2">
			<label for="email_allowed"><input id="email_allowed" type="checkbox" name="email_allowed" style="vertical-align: middle;" />I'd like to recieve newsletters</label>
		</td>
	</tr>

	<tr>
		<td></td>
		<td>			
				<input type="submit" value="Send" />
		</td>
	</tr>
	</table>

	</form>

</body>
</html>