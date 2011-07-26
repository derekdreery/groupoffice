<?php
require('../../../Group-Office.php');
require('/etc/groupoffice/servermanager.inc.php');

$GLOBALS['GO_LANGUAGE']->set_language('en');

require_once ($GLOBALS['GO_MODULES']->modules['servermanager']['class_path']."servermanager.class.inc.php");
$sm = new servermanager();

require($GLOBALS['GO_LANGUAGE']->get_language_file('servermanager'));
require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');

require('header.inc.php');

$new_trial['name'] = isset($_POST['name']) ? ($_POST['name']) : '';
$new_trial['title'] = isset($_POST['title']) ? ($_POST['title']) : 'Group-Office';
$new_trial['email'] = isset($_POST['email']) ? ($_POST['email']) : '';
$new_trial['first_name'] = isset($_POST['first_name']) ? ($_POST['first_name']) : '';
$new_trial['last_name'] = isset($_POST['last_name']) ? ($_POST['last_name']) : '';

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$missing=false;
	foreach($new_trial as $key=>$value)
	{
		if(empty($value))
		{
			$missing=true;
			break;
		}
	}
	
	$db_name = str_replace('.', '_', $new_trial['name'].'.'.$sm_config['domain']);

	if($missing)
	{
		$feedback = $lang['common']['missingField'];
	}elseif(!preg_match('/^[a-z0-9-_]*$/', $new_trial['name']))
	{
		$feedback = $lang['servermanager']['invalidHost'];
	}elseif(file_exists('/var/lib/mysql/'.$db_name) ||
	file_exists('/etc/apache2/sites-enabled/'.$new_trial['name']) ||
	$sm->get_new_trial_by_name(($new_trial['name']))
	)
	{
		$feedback = $lang['servermanager']['duplicateHost'];
	}else
	{
		//ok create a new_trial entry

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$new_trial['password']=$GO_USERS->random_password();
		$new_trial['key']=md5($new_trial['name']);
		

		$body = file_get_contents('../templates/trial.txt');
		
		$url = $sm_config['protocol'].$new_trial['name'].'.'.$sm_config['domain'];

		$_body = str_replace('{url}', $url, $body);
		$_body = str_replace('{name}', $new_trial['first_name'].' '.$new_trial['last_name'], $_body);
		$_body = str_replace('{link}',$GLOBALS['GO_MODULES']->modules['servermanager']['full_url'].basename(dirname(__FILE__)).'/create.php?key='.$new_trial['key'], $_body);
		$_body = str_replace('{password}', stripslashes($new_trial['password']), $_body);

		$swift = new GoSwift($new_trial['email'], $lang['servermanager']['new_trial_subject'],0,0,'3',$_body);
		$swift->set_from($sm_config['sender_email'], $sm_config['sender_name']);
		if(!empty($sm_config['bcc_email']))
				$swift->message->addBcc($sm_config['bcc_email']);

		$swift->sendmail();
		
		$new_trial=$new_trial;
		$sm->add_new_trial($new_trial);
		?>
		<h1>Thank you</h1>
		<p>An e-mail has been sent to <?php echo $new_trial['email']; ?>. Open it and follow the instructions to complete your trial.</p>
		
		<?php
		
		exit();
	}
}
?>
<form method="post">
<?php
if(isset($feedback))
echo '<p class="error">'.$feedback.'</p>';
?>
<table>
	<tr>
		<td colspan="2">
		<h2>Group-Office installation</h2>
		</td>
	</tr>
	<tr>
		<td>Domain name:</td>
		<td><input type="text" name="name"
			value="<?php echo $new_trial['name']; ?>" />.<?php echo $sm_config['domain'] ?></td>
	</tr>
	<tr>
		<td>Title:</td>
		<td><input type="text" name="title"
			value="<?php echo $new_trial['title']; ?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
		<h2>Admin</h2>
		</td>
	</tr>
	<tr>
		<td>Firstname:</td>
		<td><input type="text" name="first_name"
			value="<?php echo $new_trial['first_name']; ?>" /></td>
	</tr>
	<tr>
		<td>Lastname:</td>
		<td><input type="text" name="last_name"
			value="<?php echo $new_trial['last_name']; ?>" /></td>
	</tr>
	<tr>
		<td>e-mail:</td>
		<td><input type="text" name="email"
			value="<?php echo $new_trial['email']; ?>" /></td>
	</tr>
</table>

<input type="submit" value="Create trial!" />
</form>

<?php
require('footer.inc.php');
?>