<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('servermanager');
require_once ($GO_MODULES->modules['servermanager']['class_path']."servermanager.class.inc.php");

require_once ($GO_LANGUAGE->get_language_file('servermanager'));

require('/etc/groupoffice/servermanager.inc.php');

$servermanager = new servermanager();

try{
	switch($_REQUEST['task'])
	{		
		case 'delete_installation':			
			$installation_name = ($_POST['installation_name']);
			$installation = $servermanager->get_installation_by_name($installation_name);

			if(!$installation)
				throw new Exception('Installation '.$installation_name.' does not exist');

			//$cmd = 'sudo '.$GO_MODULES->modules['servermanager']['path'].'sudo.php --go_config='.$GO_CONFIG->get_config_file().' --task=remove --name='.$installation_name
			//exec($cmd);
			
			$servermanager->delete_installation($installation['id']);			
			$servermanager->delete_report($installation_name);			
			
			$response['success']=true;
			break;

		case 'save_installation':
				
			$installation_id=$installation['id']=isset($_POST['installation_id']) ? ($_POST['installation_id']) : 0;



			if(isset($_POST['modules']))
			{
				$modules = json_decode($_POST['modules'],true);

				$allowed = array();

				foreach($modules as $module)
				{
					if($module['allowed'])
						$allowed[]=$module['id'];
				}

				$config['allowed_modules']=implode(',', $allowed);
			}elseif(empty($_POST['installation_id'])){
				$config['allowed_modules']=isset($default_config['allowed_modules']) ? $default_config['allowed_modules'] : '';
			}

			//var_dump($config['allowed_modules']);
			
			$config['max_users']=Number::to_phpnumber($_POST['max_users']);
			
			$config['webmaster_email']=$_POST['webmaster_email'];
			$config['title']=$_POST['title'];
			$config['default_country']=$_POST['default_country'];
			$config['language']=$_POST['language'];
			$config['default_timezone']=$_POST['default_timezone'];
			$config['default_currency']=$_POST['default_currency'];
			$config['default_time_format']=$_POST['default_time_format'];
			$config['default_date_format']=$_POST['default_date_format'];
			$config['default_date_separator']=$_POST['default_date_separator'];
			$config['default_thousands_separator']=$_POST['default_thousands_separator'];
			$config['theme']=$_POST['theme'];
			
			$config['default_decimal_separator']=$_POST['default_decimal_separator'];
			$config['first_weekday']=$_POST['first_weekday'];
				
				
			$config['allow_themes']=isset($_POST['allow_themes']) ? true : false;
			$config['allow_password_change']=isset($_POST['allow_password_change']) ? true : false;
		//	$config['allow_registration']=isset($_POST['allow_registration']) ? true : false;
			//$config['allow_duplicate_email']=isset($_POST['allow_duplicate_email']) ? true : false;
			//$config['auto_activate_accounts']=isset($_POST['auto_activate_accounts']) ? true : false;
			//$config['notify_admin_of_registration']=isset($_POST['notify_admin_of_registration']) ? true : false;
			
			$config['quota']=Number::to_phpnumber($_POST['quota'])*1024;
			$config['restrict_smtp_hosts']=$_POST['restrict_smtp_hosts'];
			$config['serverclient_domains']=$_POST['serverclient_domains'];
			
			if(!empty($_POST['admin_password1']))
			{
				if($_POST['admin_password1']!=$_POST['admin_password2'])
				{
					throw new Exception('The passwords didn\'t match. Please try again');
				}else
				{
					$admin_password=$_POST['admin_password1'];
				}
			}
			
			if(intval($config['max_users'])<1)
			{
				throw new Exception('You must set a maximum number of users');
			}			
				
			if(!String::validate_email($config['webmaster_email']))
			{
				throw new Exception($lang['servermanager']['invalidEmail']);
			}


			$installation['report_ctime']=time();
			$installation['max_users']=$config['max_users'];
				
			if($installation['id']>0)
			{
				$old_installation = $servermanager->get_installation($installation['id']);

				if($_POST['status']!=$old_installation['status'])
				{
					$installation['status']=$_POST['status'];
					$installation['status_change_time']=time();
				}

				function get_allowed_modules($name){
					require('/etc/groupoffice/'.$name.'/config.php');
					return $config['allowed_modules'];
				}

				if(!isset($config['allowed_modules'])){
					$config['allowed_modules']=get_allowed_modules($old_installation['name']);
				}
				$servermanager->check_license($config, $old_installation['name']);

				$report['professional']=0;
				$installation['billing']=strpos($config['allowed_modules'], 'billing')!==false ? 1 : 0;

				$allowed_modules = explode(',', $config['allowed_modules']);
				foreach($servermanager->pro_modules as $pro_module) {
					if(in_array($pro_module, $allowed_modules)) {
						$report['professional']=1;
						break;
					}
				}
				
				$servermanager->update_installation($installation);

				$config['enabled']=isset($_POST['enabled']) ? true : false;
				
				
				//$config_str = ($_POST['config']);
				
				$tmp_config = $GO_CONFIG->tmpdir.uniqid();
				copy('/etc/groupoffice/'.$old_installation['name'].'/config.php', $tmp_config);
				//file_put_contents($tmp_config, $config_str);

				//unset($config['id']);
				$servermanager->write_config($tmp_config, $config);

				
				$cmd = 'sudo '.$GO_MODULES->modules['servermanager']['path'].'sudo.php --go_config='.$GO_CONFIG->get_config_file().' --task=move_config --name='.$old_installation['name'].' --tmp_config='.$tmp_config;
				if(isset($admin_password))
				{
					$cmd .= ' "'.$admin_password.'"';
				}

				exec($cmd, $output, $return_var);

				if($return_var!=0){
					throw new Exception(implode('<br />', $output));
				}

				//$servermanager->delete_report($old_installation['name']);

				//



			}else
			{
				$servermanager->check_license($config);

				$installation['status']=$_POST['status'];
				$installation['status_change_time']=time();

				$installation['name']=strtolower((trim($_POST['name'])));
				$config['db_name']=str_replace('.','_',$installation['name']);

				if(empty($installation['name']))
				{
					throw new Exception($lang['servermanager']['noHost']);
				}
				if(!preg_match('/^[a-z0-9-_\.]*$/', $installation['name']))
				{
					throw new Exception($lang['servermanager']['invalidHost']);
				}
				if(file_exists('/var/lib/mysql/'.$config['db_name']) ||
				file_exists('/etc/apache2/sites-enabled/'.$installation['name'])
				)
				{
					throw new Exception($lang['servermanager']['duplicateHost']);
				}
		
				$config['host']='/';
				$config['root_path']=$sm_config['install_path'].$installation['name'].'/groupoffice/';
				$config['tmpdir']='/tmp/'.$installation['name'].'/';

				if(floatval($GO_CONFIG->version)<3.3){
					$config['local_path']=$sm_config['install_path'].'/sm-local/'.$installation['name'].'/';
					$config['local_url']='/sm-local/'.$installation['name'].'/';
				}
				
				$config['file_storage_path']=$sm_config['install_path'].$installation['name'].'/data/';
				

				$config=array_merge($config, $static_config);
				
				$config['id']=$installation['name'];
					
				$tmp_config = $GO_CONFIG->tmpdir.uniqid();
				touch($tmp_config);
				$servermanager->write_config($tmp_config, $config);

				//$servermanager->create_report($installation['name'], $tmp_config);

				//create temporary report otherwise the license check will fail.
				$intallation['professional']=0;
				
				$allowed_modules = explode(',', $config['allowed_modules']);
				foreach($servermanager->pro_modules as $pro_module) {
					if(in_array($pro_module, $allowed_modules)) {
						$intallation['professional']=1;
						break;
					}
				}
				$installation['billing']=strpos($config['allowed_modules'], 'billing')!==false ? 1 : 0;

			
				$cmd = 'sudo '.$GO_MODULES->modules['servermanager']['path'].'sudo.php --go_config='.$GO_CONFIG->get_config_file().' --task=install --name='.$installation['name'].' --tmp_config='.$tmp_config;
				if(isset($admin_password))
				{
					$cmd .= ' --password="'.$admin_password.'"';
				}
				exec($cmd, $output, $return_var);

				//go_debug($output);

				if($return_var!=0){
					throw new Exception(implode('<br />', $output));
				}
				
				$installation_id= $servermanager->add_installation($installation);

				$response['installation_id']=$installation_id;
				$response['success']=true;

			}

		
			$response['success']=true;
			break;
				
		case 'set_config':
			$installation_name = ($_POST['installation_name']);
			$values = array_map('', $_POST['values']);
				
			//throw new Exception(var_export($_POST, true));
			
			//go_debug(var_export($_POST, true));
				
			$config_file = '/etc/groupoffice/'.$installation_name.'/config.php';
				
			if(!file_exists($config_file))
			throw new Exception('Installation '.$installation_name.' does not exist!');
				
			require($config_file);
				
			foreach($values as $name=>$value)
			{
				if($value=='true')
				{
					$value = true;
				}elseif($value=='false')
				{
					$value = false;
				}

				$config[$name]=$value;
			}
				
			//throw new Exception(var_export($config, true));
				
			$tmp_config = $GO_CONFIG->tmpdir.uniqid();
			touch($tmp_config);

				
			$servermanager->write_config($tmp_config, $config);
				
			exec('sudo '.$GO_MODULES->modules['servermanager']['path'].'sudo.php --go_config='.$GO_CONFIG->get_config_file().'--task=move_config --name='.$installation_name.' --tmp_config='.$tmp_config);
				
			$response['success']=true;
				
			break;
			/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
