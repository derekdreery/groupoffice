<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
							
						
	<form method="POST" action="submit.php">
	
	<input type="hidden" name="return_to" value="<?php echo $_SERVER['PHP_SELF']; ?>" />
	<input type="hidden" name="addressbook" value="Globaal" />
	<!-- <input type="hidden" name="mailings[]" value="blabla" /> -->
	<!--
	Enable this input to send an e-mail confirmation. The first line of this template will be used as subject followed by an empty line.

	<input type="hidden" name="confirmation_template" value="confirm_registation" />
	-->
	<input type="hidden" name="notify_users" value="1,2" />
	
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


	<table class="formulier" cellpadding="0" cellspacing="2">
	<tr>
		<td class="label">e-mail *</td>
		<td><input class="textbox" type="" name="email" value="mschering@intermesh.nl"  /><input type="hidden" name="required[]" value="email" /></td>

	</tr>
	<tr>
		<td class="label">organisatie</td>
		<td><input class="textbox" type="" name="company" value="Intermesh"  /></td>
	</tr>
	<tr>
		<td class="label">functie</td>
		<td><input class="textbox" type="" name="function" value=""  /></td>

	</tr>
	<tr>
		<td class="label">aanhef</td>
		<td>
		<label for="id_1000">
		<input type="radio" name="sex" value="M" id="id_1000" checked="checked" />De heer
		</label>
		<label for="id_1001">
		<input type="radio" name="sex" value="F" id="id_1001" />Mevrouw
		</label>

		</td>
	</tr>
	<tr>
		<td class="label">voornaam *</td>
		<td><input class="textbox" type="" name="first_name" value="Merijn"  /><input type="hidden" name="required[]" value="first_name" /></td>
	</tr>
	<tr>
		<td class="label">achternaam *</td>

		<td><input class="textbox" type="" name="last_name" value="Schering"  /><input type="hidden" name="required[]" value="last_name" /></td>
	</tr>
	<tr>
		<td class="label">telefoonnummer</td>
		<td><input class="textbox" type="" name="home_phone" value=""  /></td>
	</tr>
	<tr>
		<td class="label">vraag | opmerking</td>

		<td><textarea class="textbox" name="comment[Opmerking]" ></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td>			
				<input type="submit" />	
		</td>
	</tr>
	</table>

	</form>

</body>
</html>