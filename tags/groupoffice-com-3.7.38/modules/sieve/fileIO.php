<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: fileIO.php 0000 2010-12-29 9:16:54 wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

require('../../Group-Office.php');
require_once($GO_MODULES->modules['sieve']['class_path']."sieve.class.inc.php");
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
$GO_LANGUAGE->require_language_file('sieve');

$sieve = new sieve();
$email = new email();

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

$GO_CONFIG->sieve_port = empty($GO_CONFIG->sieve_port) ? 2000 : $GO_CONFIG->sieve_port;
$GO_CONFIG->sieve_usetls = empty($GO_CONFIG->sieve_usetls) ? false : $GO_CONFIG->sieve_usetls;

try
{
	switch($task)
	{
		case 'load_rule':
			$account = $email->get_account($_REQUEST['account_id']);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'],
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				throw new Exception('Login failed');
			}

			$response['tests']=array();
			$response['actions']=array();

			$sieve->load($_REQUEST['script_name']);

			$current_rule = $sieve->script->content[$_REQUEST['script_index']];

			if($current_rule['join'] == 1)
				$response['data']['join'] = 'allof';
			else if($current_rule['join'] == '' && $current_rule['tests'][0]['test'] == 'true')
				$response['data']['join'] = 'any';
			else
				$response['data']['join']= 'anyof';

			$response['data']['disabled']= $current_rule['disabled'];
			$response['data']['rule_name']=$current_rule['name'];
			
			foreach($current_rule['tests'] as $test)
			{
					//$test['test'];
					//$test['not'];
					//$test['type'];
					//$test['arg1'];
					//$test['arg2'];
					
					$response['tests'][] = $test;
			}
			
			foreach($current_rule['actions'] as $action)
			{
					//$action['type'];
					//$action['copy'];
					//$action['target'];

					$response['actions'][] = $action;
			}
						
			$response['success'] = true;
			break;

		case 'set_active_script':
			$account = $email->get_account($_REQUEST['account_id']);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'],
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				throw new Exception('Login failed');
			}

			$sieve->activate($_REQUEST['script_name']);
			
			if($sieve->save())
				$response['success'] = true;
			else{
				$response['success'] = false;
				$response['feedback']=$sieve->error();
			}
			break;

		case 'get_sieve_scripts':

			$account = $email->get_account($_REQUEST['account_id']);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'],
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				throw new Exception('Login failed');
				if($sieve->error() == $sieve(SIEVE_ERROR_CONNECTION))
				{
					
				}
			}
			
			$response['active']=$sieve->get_active();
			
			$all_scripts = $sieve->get_scripts();

			$response['results'] = array();
			foreach($all_scripts as $script)
			{
				$name = $script;
			
				if($script == $response['active'])
				{
					$name = $script.' (' . $lang['sieve']['active'] . ')';
				}

				$response['results'][]=array('value'=>$script,'name'=>$name);
			}
			
			$response['success'] = true;
			break;

		case 'get_sieve_rules':
			
			$account = $email->get_account($_REQUEST['account_id']);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'], 
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				throw new Exception('Login failed');
			}

			if(!empty($_REQUEST['script_name']))
				$script_name = $_REQUEST['script_name'];
			else
				$script_name = $sieve->get_active();
			
			$response['results']=array();

			$sieve->load($script_name);
			if(isset($_POST['delete_keys']))
			{
				try
				{
					$keys = json_decode($_POST['delete_keys']);

					foreach($keys as $key)
					{
						if($sieve->script->delete_rule($key))
							$sieve->save();
					}
					$response['deleteSuccess']=true;
				}

				catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			if(!empty($sieve->script->content)) {
				$index=0;
				foreach($sieve->script->content as $item)
				{
					$i['name']=$item['name'];
					$i['index']=$index;
					$i['script_name']=$script_name;
					$i['disabled']= $item['disabled'];

					$response['results'][]=$i;

					$index++;
				}
			}

			$response['success']=true;
			break;
		
		case 'save_sieve_rules':
			$allTests			=		json_decode($_REQUEST['tests'], true);
			$allActions		=		json_decode($_REQUEST['actions'], true);
			$account_id		=		$_REQUEST['account_id'];
			$script_index =		$_REQUEST['script_index'];
			$rule_name		=		$_REQUEST['rule_name'];
			$script				=		$_REQUEST['script_name'];
			$join					=		$_REQUEST['join']; // allof, anyof, any

			$account = $email->get_account($account_id);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'],
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				throw new Exception('Login failed');
			}

			$rule['disabled'] = isset($_REQUEST['disabled']) ? true : false;

			$rule['name'] = $_REQUEST['rule_name'];
			$rule['tests'] = json_decode($_REQUEST['tests'], true);
			$rule['actions'] = json_decode($_REQUEST['actions'], true);

			for($i=0,$c=count($rule['actions']);$i<$c;$i++)
			{
				if(isset($rule['actions'][$i]['addresses']) && !is_array($rule['actions'][$i]['addresses'])){
          if($rule['actions'][$i]['type']=='vacation' && !empty($GO_CONFIG->sieve_vacation_subject))
            $rule['actions'][$i]['subject']=$GO_CONFIG->sieve_vacation_subject;

					$rule['actions'][$i]['addresses']=explode(',',$rule['actions'][$i]['addresses']);
					$rule['actions'][$i]['addresses']=array_map('trim', $rule['actions'][$i]['addresses']);
				}
			}

			if($join == 'allof')
				$rule['join'] = 1;
			else if($join == 'any')
			{
				$rule['join'] = '';
				$rule['tests'] = array();
			  $rule['tests'][0]['test'] = 'true';
				$rule['tests'][0]['not'] = '';
				$rule['tests'][0]['type'] = '';
				$rule['tests'][0]['arg'] = '';
				$rule['tests'][0]['arg1'] = '';
				$rule['tests'][0]['arg2'] = '';
			}
			else
			{
				$rule['join'] = '';
				if($rule['tests'][0]['test'] == 'true' &&
					 $rule['tests'][0]['not'] == '' &&
					 $rule['tests'][0]['type'] == '' &&
					 $rule['tests'][0]['arg'] == '' &&
					 $rule['tests'][0]['arg1'] == '' &&
					 $rule['tests'][0]['arg2'] == '')
				{
					// Remove the first item from the array if it is an empty one where only TEST == true
					array_shift($rule['tests']);
				}
			}
			$response['results'] = array();

			// Het script laden
			$sieve->load($script);
					
			// Het script ophalen en terugzetten
			if($script_index>-1 && isset($sieve->script->content[$script_index]))
				$sieve->script->update_rule($script_index,$rule);
			else
				$sieve->script->add_rule($rule);

			// Het script opslaan
			if($sieve->save())
				$response['success'] = true;
			else
				$response['success'] = false;
			break;

		case 'check_is_supported':
			$account_id		=		$_REQUEST['account_id'];
			$sieve_enabled = true;

			$account = $email->get_account($account_id);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'],
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				$sieve_enabled = false;
			}

			if($sieve_enabled)
				$response['sieve_supported']=true;
			else
				$response['sieve_supported']=false;
			break;
			
		case 'save_scripts_sort_order':
			$account = $email->get_account($_REQUEST['account_id']);
			$account = $email->decrypt_account($account);
			if(!$sieve->connect($account['username'],
							$account['password'],
							$account['host'],
							$GO_CONFIG->sieve_port,
							null,
							$GO_CONFIG->sieve_usetls,
							array(),
							true))
			{
				throw new Exception('Login failed');
				if($sieve->error() == $sieve(SIEVE_ERROR_CONNECTION))
				{

				}
			}

			$script = $sieve->get_script($_POST['script_name']);
			$sort_order = json_decode($_POST['sort_order'], true);

			$sieve->load($sieve->get_active());

			$count=count($sort_order);

			for($new_index=0;$new_index<$count;$new_index++){
				$old_index = $sort_order[$new_index];

				//oude script ophalen
				$temp = $sieve->script->content[$old_index];
				
				//kopie toevoegen
				$sieve->script->add_rule($temp);
			}

			//oude verwijderen
			for($i=0;$i < $count; $i++)
			{
				$sieve->script->delete_rule($i);
			}

			$sieve->save();
			$response['success'] = true;
			break;
	}
}
catch(Exception $e)
{
	$response=array();
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);
?>