<?php
require('../../../Group-Office.php');
require('/etc/groupoffice/servermanager.inc.php');

require_once ($GLOBALS['GO_MODULES']->modules['servermanager']['class_path']."servermanager.class.inc.php");
$sm = new servermanager();

require($GLOBALS['GO_LANGUAGE']->get_language_file('servermanager'));

$key = ($_REQUEST['key']);

require($GLOBALS['GO_CONFIG']->class_path.'html/select.class.inc.php');


require('header.inc.php');

$new_trial = $sm->get_new_trial_by_key($key);

if(!$new_trial)
{
	?>
	<h1>Trial not found</h1>
	<p>Sorry, but we couldn't find your trial. Maybe you copied the link to this page completely or you have waited more then 1 day to click on the link.</p>
	<a href="trial.php">Click here to try again</a> 
	<?php
}else
{
	$timezone = isset($_POST['timezone']) ? ($_POST['timezone']) : $GLOBALS['GO_CONFIG']->default_timezone;
	$language = isset($_POST['language']) ? ($_POST['language']) : $GLOBALS['GO_LANGUAGE']->language;
	$date_format = isset($_POST['date_format']) ? ($_POST['date_format']) : 'dd-mm-yyyy';
	$time_format = isset($_POST['time_format']) ? ($_POST['time_format']) : 'G:i';
	$country = isset($_POST['country']) ? ($_POST['country']) : $GLOBALS['GO_CONFIG']->default_country;
	$currency = isset($_POST['currency']) ? ($_POST['currency']) : $GLOBALS['GO_CONFIG']->default_currency;
	$number_format = isset($_POST['number_format']) ? ($_POST['number_format']) : '1.000,00';
	$first_weekday = isset($_POST['first_weekday']) ? ($_POST['first_weekday']) : '1';
	
	
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
		$installation['name']=$new_trial['name'];
		
		$config['webmaster_email']=$new_trial['email'];
		$config['title']=$new_trial['title'];
		$config['default_country']=$country;
		$config['language']=$language;
		$config['default_timezone']=$timezone;
		$config['default_currency']=$currency;
			
	
		$config['default_date_separator']=str_replace('yyyy', '', $date_format);
		$config['default_date_separator']=str_replace('dd', '', $config['default_date_separator']);
		$config['default_date_separator']=str_replace('mm', '', $config['default_date_separator']);
		$config['default_date_separator']=$config['default_date_separator'][0];
		
		$config['default_time_format']=$time_format;
		$config['default_date_format']=str_replace($config['default_date_separator'],'', $date_format);
		$config['default_date_format']=str_replace('yyyy','Y', $config['default_date_format']);
		$config['default_date_format']=str_replace('mm','m', $config['default_date_format']);
		$config['default_date_format']=str_replace('dd','d', $config['default_date_format']);
		
		$config['default_thousands_separator']=$number_format[1];
		$config['default_decimal_separator']=$number_format[5];
		$config['first_weekday']=$first_weekday;
		
		if(!preg_match('/^[a-z0-9-_]*$/', $installation['name']))
		{
			$feedback = $lang['servermanager']['invalidHost'];
		}elseif(file_exists('/var/lib/mysql/'.$installation['name']) ||
		file_exists('/etc/apache2/sites-enabled/'.$installation['name'])){
			$feedback = $lang['servermanager']['duplicateHost'];
		}else
		{
			$installation['name'].='.'.$sm_config['domain'];
			$installation['status']='trial';
			$installation['status_change_time']=time();
			
			$installation_id= $sm->add_installation($installation);

			$response['installation_id']=$installation_id;
			$response['success']=true;
			
			$installation=array_map('stripslashes', $installation);		

			$config['host']='/';
			$config['root_path']=$sm_config['install_path'].$installation['name'].'/groupoffice/';
			$config['tmpdir']='/tmp/'.$installation['name'].'/';
			//$config['local_path']=$sm_config['install_path'].'sm-local/'.$installation['name'].'/';
			//$config['local_url']='/sm-local/'.$installation['name'].'/';
			$config['file_storage_path']=$sm_config['install_path'].$installation['name'].'/data/';
			$config['db_name']=str_replace('.','_',$installation['name']);
			$config['db_name']=str_replace('-','_',$config['db_name']);

			$config=array_merge($static_config, $default_config, $config);
				
			$config['id']=$installation['name'];
			
			$tmp_config = $GLOBALS['GO_CONFIG']->tmpdir.uniqid();
			touch($tmp_config);
			$sm->write_config($tmp_config, $config);

			exec('sudo '.$GLOBALS['GO_MODULES']->modules['servermanager']['path'].'sudo.php --go_config='.$GLOBALS['GO_CONFIG']->get_config_file().' --task=install --name='.$installation['name'].' --tmp_config='.$tmp_config.' --password='.$new_trial['password']);		
			$sm->delete_new_trial($new_trial['name']);
			?>
			<h1>Trial installation complete!</h1>
			<p>Thank you for trying Group-Office.</p>
			<a href="<?php echo $sm_config['protocol'].$installation['name']; ?>">
			Click here to launch your new Group-Office!
			</a>
			<?php
			
			exit();
		}
	}
	?>
	<form method="post">
	<input type="hidden" name="key" value="<?php echo $_REQUEST['key']; ?>" />
	<h1>Complete Group-Office installation</h1>
	
	<?
	if(isset($feedback))
	echo '<p class="error">'.$feedback.'</p>';
	?>
	
	<p>Welcome back. We need some regional information to determine your time and notations.</p>
	
	<table>
		<tr>
			<td colspan="2">
			<h2>Regional settings</h2>
			</td>
		</tr>
		<tr>
			<td>Timezone:</td>
			<td><?php
			$select = new select('timezone', $timezone);
			require('../timezones.inc.php');
			foreach($timezones as $tz)
			{
				$select->add_value($tz, $tz);
			}
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>Country:</td>
			<td><?php
			$select = new select('country', $country);
			
			require($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));			
			foreach($countries as $code=>$desc)
			{
				$select->add_value($code, $desc);
			}
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>Language:</td>
			<td><?php
			$select = new select('language', $language);
			
			require($GLOBALS['GO_CONFIG']->root_path.'language/languages.inc.php');			
			foreach($languages as $language=>$desc)
			{
				$select->add_value($language, $desc);
			}
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>Date format:</td>
			<td><?php
			$formats[]='mm-dd-yyyy';
			$formats[]='dd-mm-yyyy';
			$formats[]='yyyy-mm-dd';
			$formats[]='dd/mm/yyyy';
			$formats[]='mm/dd/yyyy';
			$formats[]='yyyy/mm/dd';
			$formats[]='mm.dd.yyyy';
			$formats[]='dd.mm.yyyy';
			$formats[]='yyyy.mm.dd';
			
			$select = new select('date_format', $date_format);			
						
			foreach($formats as $df)
			{
				$select->add_value($df, $df);
			}
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>Time format:</td>
			<td><?php
			$formats=array();
			$formats['G:i']='24h';
			$formats['g:i a']='12h';
			$select = new select('time_format', $time_format);			
						
			foreach($formats as $value=>$desc)
			{
				$select->add_value($value, $desc);
			}
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>Number format:</td>
			<td><?php
			$formats=array();
			$formats[]='1.000,00';
			$formats[]='1,000.00';
			$formats[]='1 000,00';
			$formats[]='1 000.00';			
			
			$select = new select('number_format', $number_format);						
			foreach($formats as $value)
			{
				$select->add_value($value, $value);
			}
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>First day of the week:</td>
			<td><?php
			$select = new select('first_weekday', $first_weekday);						
			$select->add_value('0', 'Sunday');
			$select->add_value('1', 'Monday');
			$select->print_html();
			?></td>
		</tr>
		<tr>
			<td>Currency:</td>
			<td><input type="text" name="currency" value="<?php echo $currency; ?>" /></td>
		</tr>
		</table>
		<input type="submit" value="Create trial!" />
		</form>
	<?php
	
}
require('footer.inc.php');
