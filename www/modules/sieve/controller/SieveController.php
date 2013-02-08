<?php
class GO_Sieve_Controller_Sieve extends GO_Base_Controller_AbstractModelController{
	
	private $_sieve;
	
	function __construct() {
		$this->_sieve = new GO_Sieve_Util_Sieve();
		parent::__construct();
	}
	
	private function _sieveConnect($accountId) {		
		$accountModel = GO_Email_Model_Account::model()->findByPk($accountId);
		
		if (!empty($accountModel))
		$connectResponse = $this->_sieve->connect(
				$accountModel->username,
				$accountModel->decryptPassword(),
				$accountModel->host,
				$accountModel->sieve_port,
				null,
				!empty($accountModel->sieve_usetls),
				array(),
				true);
		
		if (empty($connectResponse))
		{
			throw new Exception('Sorry, manage sieve filtering not supported on '.$accountModel->host.' using port '.$accountModel->sieve_port);				
		}
		
		return true;
		
	}
	
	protected function actionIsSupported($params){
		
		try{
			$supported=$this->_sieveConnect($params['account_id']);
		}catch (Exception $e){
			$supported=false;
		}
		return array('success'=>true, 'supported'=>$supported);
	}
	
	protected function actionScripts($params) {
		
		$this->_sieveConnect($params['account_id']);
		
		if(!empty($params['set_active_script_name']))
			$this->_sieve->activate($params['set_active_script_name']);				

		$response['active']=$this->_sieve->get_active($params['account_id']);
		$all_scripts = $this->_sieve->get_scripts();

		$response['results'] = array();
		foreach($all_scripts as $script)
		{
			$name = $script;

			if($script == $response['active'])
			{
				$name .= ' ('.GO::t('active','sieve').')';
			}

			$response['results'][]=array('value'=>$script,'name'=>$name, 'active'=>$script == $response['active']);
		}

		$response['success'] = true;

		return $response;
	}
	
	protected function actionRules($params) {
		
		$this->_sieveConnect($params['account_id']);

		if(!empty($params['script_name']))
			$scriptName = $params['script_name'];
		else
			$scriptName = $this->_sieve->get_active($params['account_id']);

		$response['results']=array();

		$this->_sieve->load($scriptName);
		if(isset($params['delete_keys']))
		{
			try
			{
				$keys = json_decode($params['delete_keys']);

				foreach($keys as $key)
				{
					if($this->_sieve->script->delete_rule($key))
						$this->_sieve->save();
				}
				$response['deleteSuccess']=true;
			}

			catch(Exception $e)
			{
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}

		if(!empty($this->_sieve->script->content)) {
			$index=0;
			foreach($this->_sieve->script->content as $item)
			{
				//if ($item['name']!='Autoreply')
				{
					$i['name']=$item['name'];
					$i['index']=$index;
					$i['script_name']=$scriptName;
					$i['active']= !$item['disabled'];

					$response['results'][]=$i;
				}
				$index++;
			}
		}

		$response['success']=true;
		return $response;
	}
	
	protected function actionSubmitRules($params) {	
		$allCriteria			=		json_decode($params['criteria'], true);
		$allActions		=		json_decode($params['actions'], true);
		$account_id		=		$params['account_id'];
		$script_index =		$params['script_index'];
		$rule_name		=		$params['rule_name'];
		$script				=		$params['script_name'];
		$join					=		$params['join']; // allof, anyof, any

		try {
			$this->_sieveConnect($params['account_id']);

			$rule['disabled'] = !isset($params['active']);

			$rule['name'] = $params['rule_name'];
			$rule['tests'] = json_decode($params['criteria'], true);
			$rule['actions'] = json_decode($params['actions'], true);

			for($i=0,$c=count($rule['actions']);$i<$c;$i++)
			{
				if(isset($rule['actions'][$i]['addresses']) && !is_array($rule['actions'][$i]['addresses'])){
					if($rule['actions'][$i]['type']=='vacation') {
						if (!empty(GO::config()->sieve_vacation_subject))
							$rule['actions'][$i]['subject']=GO::config()->sieve_vacation_subject;
					}

					$rule['actions'][$i]['addresses']=explode(',',$rule['actions'][$i]['addresses']);
					$rule['actions'][$i]['addresses']=array_map('trim', $rule['actions'][$i]['addresses']);
				} else {
					unset($rule['actions'][$i]['vacationStart']);
					unset($rule['actions'][$i]['vacationEnd']);
				}
				
				if($rule['actions'][$i]['type'] == 'stop' && $i < $c-1){
					Throw new GO_Base_Exception_Save(GO::t('stopEndError','sieve'));
				}
			}

			if($join == 'allof') {
				$rule['join'] = 1;
			}
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
			$this->_sieve->load($script);

			// Het script ophalen en terugzetten
			if($script_index>-1 && isset($this->_sieve->script->content[$script_index]))
				$this->_sieve->script->update_rule($script_index,$rule);
			else {
				$this->_sieve->script->add_rule($rule);
			}

			// Het script opslaan
			if($this->_sieve->save()) {
				$response['success'] = true;
			} else {
				$response['feedback'] = $this->_sieve->error();
				$response['success'] = false;
			}
		} catch (Exception $e) {
			// you can change the feedback when debugging
			$response['feedback'] = nl2br($e->getMessage()); //.'<br>'.$e->getTraceAsString();
		}
		return $response;
	}
	
	protected function actionAccountAliases($params) {
		$response = array();
		$aliasesStmt = GO_Email_Model_Alias::model()->findByAttribute('account_id',$params['account_id']);
		$aliases = array();
		while ($aliasModel = $aliasesStmt->fetch()) {
			$aliases[] = $aliasModel->email;
		}
		$response['data']['aliases'] = implode(',',$aliases);
		$response['success'] = true;
		return $response;
	}
	
	protected function actionRule($params) {
		
		$this->_sieveConnect($params['account_id']);

		$response['criteria']=array();
		$response['actions']=array();

		$this->_sieve->load($params['script_name']);

		$current_rule = $this->_sieve->script->content[$params['script_index']];

		if($current_rule['join'] == 1)
			$response['data']['join'] = 'allof';
		else if($current_rule['join'] == '' && $current_rule['tests'][0]['test'] == 'true')
			$response['data']['join'] = 'any';
		else
			$response['data']['join']= 'anyof';

		$response['data']['active']= !$current_rule['disabled'];
		$response['data']['rule_name']=$current_rule['name'];
	
		foreach($current_rule['tests'] as $test)
		{
				//$test['test'];
				//$test['not'];
				//$test['type'];
				//$test['arg1'];
				//$test['arg2'];

				$response['criteria'][] = $test;
		}

		foreach($current_rule['actions'] as $action)
		{
				switch($action['type'])
				{
					case 'set_read':
						$action['text'] = GO::t('setRead','sieve');
						break;
					case 'fileinto':
						$action['text'] = GO::t('fileinto','sieve').' "'.$action['target'].'"';
						break;
					case 'copyto':
						$action['text']=GO::t('copyto','sieve').' "'.$action['target'].'"';
						break;
					case 'redirect':
						if (!empty($action['copy'])) {
							$action['type'] = 'redirect_copy';
							$action['text'] = GO::t('sendcopyto','sieve').' "'.$action['target'].'"';
						} else {
							$action['text'] = GO::t('forwardto','sieve').' "'.$action['target'].'"';
						}
						break;
					case 'reject':
						$action['text']=GO::t('refusewithmesssage','sieve').' "'.$action['target'].'"';
						break;
					case 'vacation':
						$addressesText = !empty($action['addresses']) && is_array($action['addresses'])
							? GO::t('vacAlsoMailTo','sieve').': '.implode(',',$action['addresses']).'. '
							: '';
						
						if(empty($action['days']))
							$action['days']=7;
						
						$action['text']=GO::t('vacsendevery','sieve').' '.$action['days'].' '.GO::t('vacsendevery2','sieve').'. '.$addressesText.GO::t('vacationmessage','sieve').' "'.$action['reason'].'"';
						break;
					case 'discard':
						$action['text']=GO::t('discard','sieve');
						break;
					case 'stop':
						$action['text']=GO::t('stop','sieve');
						break;
					default:
						$action['text']=GO::t('errorshowtext','sieve');
						break;
				}
				$response['actions'][] = $action;
		}

		$response['success'] = true;
		return $response;
	}

//	protected function actionSetActiveScript($params) {
//		$this->_sieveConnect($params['account_id']);
//
//		$this->_sieve->activate($params['script_name']);
//
//		if($this->_sieve->save())
//			$response['success'] = true;
//		else{
//			$response['success'] = false;
//			$response['feedback']=$this->_sieve->error();
//		}
//		return $response;
//	}
//	
	protected function actionSaveScriptsSortOrder($params) {
		
		$this->_sieveConnect($params['account_id']);

		$script = $this->_sieve->get_script($params['script_name']);
		$sort_order = json_decode($params['sort_order'], true);

		$this->_sieve->load($this->_sieve->get_active($params['account_id']));

		$count=count($sort_order);

		for($new_index=0;$new_index<$count;$new_index++){
			$old_index = $sort_order[$new_index];

			//oude script ophalen
			$temp = $this->_sieve->script->content[$old_index];

			//kopie toevoegen
			$this->_sieve->script->add_rule($temp);
		}

		//oude verwijderen
		for($i=0;$i < $count; $i++)
		{
			$this->_sieve->script->delete_rule($i);
		}

		$this->_sieve->save();
		$response['success'] = true;
		return $response;
	}
	
//	protected function actionLoadOutOfOffice($params) {
//		$response['data'] = array(
//				'message' => '',
//				'days' => '',
//				'start_date' => '',
//				'end_date' => '',
//				'email' => ''
//			);
//		
//		$this->_sieveConnect($params['account_id']);
//		$this->_sieve->load($this->_sieve->get_active());
//		
//		foreach($this->_sieve->script->content as $k => $item) {
//			if ($item['name']=='Autoreply') {
//				$response['data'] = $item;
//				$response['data']['script_index'] = $k;
//				break;
//			}
//		}
//		
//		$response['success'] = true;
//		return $response;
//	}
	
}
?>
