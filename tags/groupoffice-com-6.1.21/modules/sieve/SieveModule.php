<?php

namespace GO\Sieve;

use GO\Base\Module;
use GO\Email\Controller\AccountController;


class SieveModule extends Module{
	
	public function autoInstall() {
		return true;
	}
	
	public function depends() {
		return array("email");
	}
	
	public function package() {
		return self::PACKAGE_COMMUNITY;
	}
	
	public static function initListeners() {
		// Add trigger
		$c = new AccountController();
		$c->addListener('submit', '\GO\Sieve\SieveModule', 'saveOutOfOfficeMessage');
		$c->addListener('load', '\GO\Sieve\SieveModule', 'loadOutOfOfficeMessage');
	}
	
	/**
	 * 
	 * Load the outofoffice message when it's available
	 * 
	 * @param SieveModule $this
	 * @param array $response
	 * @param Account $model
	 * @param array $params
	 * 
	 */
	public static function loadOutOfOfficeMessage(&$this,&$response,&$model,&$params){
		
		$sieve = new \GO\Sieve\Util\Sieve();
		$connected = $sieve->connect($model->username,$model->decryptPassword(),$model->host,$model->sieve_port,null,!empty($model->sieve_usetls),array(),true);

		if(empty($connected))	{
			
			\GO::debug('DON\'T LOAD OOO_SIEVE');
			
			return;
			throw new \Exception('Sorry, manage sieve filtering not supported on '.$model->host.' using port '.$model->sieve_port);
		}

		\GO::debug('LOAD OOO_SIEVE');
		
		$sieveScriptName = $sieve->get_active($model->id);
		
		$sieve->load($sieveScriptName);
		
		$rule = false;
		
		if(!empty($sieve->script->content)) {
			$index=0;
			foreach($sieve->script->content as $item){
				// Get the "Out of office" script because it need to be loaded here
				if(isset($item['name']) && $item['name']=='Out of office')
				{
					$rule = array();
					
					$rule['ooo_script_name']=$sieveScriptName;
					$rule['ooo_rule_name']=$item['name'];
					$rule['ooo_script_active']=!$item['disabled'];
					$rule['ooo_script_index']=$index;
					
					// Load the Rule that is set for this script
					$outOfOfficeRule = $sieve->script->content[$index];
					
					// Loop through the tests of this rule, the first test should be the "Activate" test
					// The second test is the "Deactivate" date
					// If there are more tests set, then they will be added to the response too (by index)
					for($i=0; $i < count($outOfOfficeRule['tests']); $i++){
						
						$date = date(\GO::user()->completeDateFormat, strtotime($outOfOfficeRule['tests'][$i]['arg']));
						
						switch($i){
							case 0:
								$rule['ooo_activate'] = $date;
								break;
							case 1:
								$rule['ooo_deactivate'] = $date;
								break;
							default:
								$rule[$i] = $date;
								break;
						}
					}
					
					// Loop through the actions and search for the "vacation" action
					foreach($outOfOfficeRule['actions'] as $action){
						if($action['type'] === "vacation"){

							$rule['ooo_message'] = $action['reason'];
							
							if(!empty($action['addresses'])){
								$rule['ooo_aliasses'] = $action['addresses'];
							} else {
								$rule['ooo_aliasses'] = '';
							}
						}
					}
											
					// Add the complete rule to the response
					$response['complete_rule']=$outOfOfficeRule;
					
				}
				$index++;
			}
		}
		
		if(empty($rule)){
			// If no rule with the name "Out of office" is found, then create a new one and add it to the response.
			$response['data'] = array_merge($response['data'],array(
				'ooo_script_name'=>'default',
				'ooo_rule_name'=>'Out of office',
				'ooo_script_active'=>false,
				'ooo_script_index'=>-1,
				'ooo_activate'=>date(\GO::user()->completeDateFormat),
				'ooo_deactivate'=>date(\GO::user()->completeDateFormat),
				'ooo_message'=> \GO::t('standardvacationmessage','sieve'),
				'ooo_aliasses'=>'',
			));
		} else {
			// Add the found rule to the response
			$response['data'] = array_merge($response['data'],$rule);
		}
	}
	
	/**
	 * Save the outofoffice message when it's data is posted.
	 * 
	 * @param SieveModule $this
	 * @param array $response
	 * @param Account $model
	 * @param array $params
	 * @param array $modifiedAttributes
	 */
	public static function saveOutOfOfficeMessage(&$this,&$response,&$model,&$params,$modifiedAttributes){
		
		// Check if the ooo_ fields are posted
		if(isset($params['ooo_message'])){
			
			$sieve = new \GO\Sieve\Util\Sieve();
			
			$connected = $sieve->connect($model->username,$model->decryptPassword(),$model->host,$model->sieve_port,null,!empty($model->sieve_usetls),array(),true);
		
			if(empty($connected))	{
				
				\GO::debug('DO NOT PROCESS OOO_SIEVE');
				
				return;
				throw new \Exception('Sorry, manage sieve filtering not supported on '.$model->host.' using port '.$model->sieve_port);
			}
			
			\GO::debug('PROCESSING OOO_SIEVE');

			$sieveScriptName = $sieve->get_active($model->id);

			$sieve->load($sieveScriptName);
			
//			$params['ooo_activate']				30-06-2015
//			$params['ooo_script_active']	false
//			$params['ooo_aliasses']				admin@intermesh.dev
//			$params['ooo_deactivate']			07-07-2015
//			$params['ooo_script_index']		0
//			$params['ooo_message']				I am on vacation
//			$params['ooo_rule_name']			Out of office
//			$params['ooo_script_name']		default
			
			$activateDate = date('Y-m-d',strtotime($params['ooo_activate']));
			$deactivateDate = date('Y-m-d',strtotime($params['ooo_deactivate']));
			
			// Convert posted data to the correct rule object			
			$rule = array(
				"type"=>"if",
				"tests"=>array(
					0=>array(
						"test"=>"currentdate",
						"not"=>false,
						"arg"=>$activateDate,//2015-06-30
						"part"=>"date", 
						"type"=>"value-ge"
					),
					1=>array(
						"test"=>"currentdate",
						"not"=>false,
						"arg"=>$deactivateDate,//2015-07-07
						"part"=>"date",
						"type"=>"value-le"
					)
				),
				"actions"=>array(
					0=>array(
						"type"=>"vacation",
						"reason"=>$params['ooo_message'],
						"days"=>"3",
						"addresses"=>$params['ooo_aliasses']
					),
					1=>array(
						"type"=>"stop"
					)
				),
				"join"=>true,
				"disabled"=>!$params['ooo_script_active'],
				"name"=>$params['ooo_rule_name']				
			);
			
			if (!empty(\GO::config()->sieve_vacation_subject))
				$rule['actions'][0]['subject']=\GO::config()->sieve_vacation_subject;
				
			$response['sieve_after'] = $rule;
			
			// Het script ophalen en terugzetten
			if($params['ooo_script_index']>-1 && isset($sieve->script->content[$params['ooo_script_index']])){
				$sieve->script->update_rule($params['ooo_script_index'],$rule);
			} else {
				$sieve->script->add_rule($rule);
			}

			// Het script opslaan
			if($sieve->save()) {
				$response['success'] = true;
			} else {
				$response['feedback'] = "Could not save filtering rules. Please check your input.<br />".$sieve->error();
				$response['success'] = false;
			}
		}
	}
}