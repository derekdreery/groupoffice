<?php
class GO_Addressbook_Controller_SearchQuery extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_SearchQuery';
		
	public function beforeSubmit(&$response, &$model, &$params) {
		$params['user_id'] = GO::user()->id;
		$model->setAttribute('user_id',GO::user()->id);
		parent::beforeSubmit($response, $model, $params);
	}
	
	public function actionAddressbookFields($params) {
		$contact_columns = GO_Addressbook_Model_Contact::model()->getColumns();
		$company_columns = GO_Addressbook_Model_Company::model()->getColumns();

		if($params['type']=='contacts')
		{

			$response['results']=array(
				//array('field'=>'ab_contacts.name', 'label'=>GO::t('name'], 'type'=>$contact_columns['name']),
				array('field'=>'t.title', 'label'=>GO::t('title'), 'type'=>$contact_columns['title']),
				array('field'=>'t.first_name', 'label'=>GO::t('firstName'), 'type'=>$contact_columns['first_name']['gotype']),
				array('field'=>'t.middle_name', 'label'=>GO::t('middleName'), 'type'=>$contact_columns['middle_name']['gotype']),
				array('field'=>'t.last_name', 'label'=>GO::t('lastName'), 'type'=>$contact_columns['last_name']['gotype']),
				array('field'=>'t.initials', 'label'=>GO::t('initials'), 'type'=>$contact_columns['initials']['gotype']),
				array('field'=>'t.sex', 'label'=>GO::t('sex'), 'type'=>$contact_columns['sex']['gotype']),
				array('field'=>'t.birthday', 'label'=>GO::t('birthday'), 'type'=>$contact_columns['birthday']['gotype']),
				array('field'=>'t.email', 'label'=>GO::t('email'), 'type'=>$contact_columns['email']['gotype']),
				array('field'=>'t.country', 'label'=>GO::t('country'), 'type'=>$contact_columns['country']['gotype']),
//					array('field'=>'t.iso_address_format', 'label'=>GO::t('address_format'], 'type'=>$contact_columns['iso_address_format']),
				array('field'=>'t.state', 'label'=>GO::t('state'), 'type'=>$contact_columns['state']['gotype']),
				array('field'=>'t.city', 'label'=>GO::t('city'), 'type'=>$contact_columns['city']['gotype']),
				array('field'=>'t.zip', 'label'=>GO::t('zip'), 'type'=>$contact_columns['zip']['gotype']),
				array('field'=>'t.address', 'label'=>GO::t('address'), 'type'=>$contact_columns['address']['gotype']),
				array('field'=>'t.address_no', 'label'=>GO::t('addressNo'), 'type'=>$contact_columns['address_no']['gotype']),
				array('field'=>'t.home_phone', 'label'=>GO::t('phone'), 'type'=>$contact_columns['home_phone']['gotype']),
				array('field'=>'t.work_phone', 'label'=>GO::t('workphone'), 'type'=>$contact_columns['work_phone']['gotype']),
				array('field'=>'t.fax', 'label'=>GO::t('fax'), 'fax'=>$contact_columns['fax']['gotype']),
				array('field'=>'t.work_fax', 'label'=>GO::t('workFax'), 'type'=>$contact_columns['work_fax']['gotype']),
				array('field'=>'t.cellular', 'label'=>GO::t('cellular'), 'type'=>$contact_columns['cellular']['gotype']),
				array('field'=>'ab_companies.name', 'label'=>GO::t('company'), 'type'=>$company_columns['name']['gotype']),
				array('field'=>'t.department', 'label'=>GO::t('department'), 'type'=>$contact_columns['department']['gotype']),
				array('field'=>'t.function', 'label'=>GO::t('function'), 'type'=>$contact_columns['function']['gotype']),
				array('field'=>'t.comment', 'label'=>GO::t('comment','addressbook'), 'type'=>$contact_columns['comment']['gotype']),
				array('field'=>'t.salutation', 'label'=>GO::t('salutation'), 'type'=>$contact_columns['salutation']['gotype'])
			);
			$model="GO_Addressbook_Model_Contact";
		}else
		{
			$response['results']=array(
				array('field'=>'t.name', 'label'=>GO::t('name'), 'type'=>$company_columns['name']['gotype']),
				array('field'=>'t.name2', 'label'=>GO::t('name2'), 'type'=>$company_columns['name2']['gotype']),
				//array('field'=>'t.title', 'label'=>GO::t('title'], 'type'=>$company_columns['title']),
				array('field'=>'t.email', 'label'=>GO::t('email'), 'type'=>$company_columns['email']['gotype']),
				array('field'=>'t.country', 'label'=>GO::t('country'), 'type'=>$company_columns['country']['gotype']),
//					array('field'=>'t.iso_address_format', 'label'=>GO::t('address_format'], 'type'=>$company_columns['iso_address_format']),
				array('field'=>'t.state', 'label'=>GO::t('state'), 'type'=>$company_columns['state']['gotype']),
				array('field'=>'t.city', 'label'=>GO::t('city'), 'type'=>$company_columns['city']['gotype']),
				array('field'=>'t.zip', 'label'=>GO::t('zip'), 'type'=>$company_columns['zip']['gotype']),
				array('field'=>'t.address', 'label'=>GO::t('address'), 'type'=>$company_columns['address']['gotype']),
				array('field'=>'t.address_no', 'label'=>GO::t('addressNo'), 'type'=>$company_columns['address_no']['gotype']),

				array('field'=>'t.post_country', 'label'=>GO::t('postCountry'), 'type'=>$company_columns['post_country']['gotype']),
				array('field'=>'t.post_state', 'label'=>GO::t('postState'), 'type'=>$company_columns['post_state']['gotype']),
				array('field'=>'t.post_city', 'label'=>GO::t('postCity'), 'type'=>$company_columns['post_city']['gotype']),
				array('field'=>'t.post_zip', 'label'=>GO::t('postZip'), 'type'=>$company_columns['post_zip']['gotype']),
				array('field'=>'t.post_address', 'label'=>GO::t('postAddress'), 'type'=>$company_columns['post_address']['gotype']),
				array('field'=>'t.post_address_no', 'label'=>GO::t('postAddressNo'), 'type'=>$company_columns['post_address_no']['gotype']),

				array('field'=>'t.phone', 'label'=>GO::t('phone'), 'type'=>$company_columns['phone']['gotype']),
				array('field'=>'t.fax', 'label'=>GO::t('name'), 'type'=>$company_columns['fax']['gotype']),

				array('field'=>'t.comment', 'label'=>GO::t('comment','addressbook'), 'type'=>$company_columns['comment']['gotype'])
			);
			$model="GO_Addressbook_Model_Company";
		}

		if (isset(GO::modules()->customfields)) {
			require_once(GO::config()->root_path.'GO.php');

			$stmt = GO_Customfields_Model_Category::model()->findByModel($model);
			while($category = $stmt->fetch()){
				$fstmt = $category->fields();
				while($field = $fstmt->fetch()){
					$arr=$field->getAttributes();
					$arr['dataname']=$field->columnName();
					$fields[]=$arr;
					if(empty($field->exclude_from_grid))
							$response['results'][] = array('id'=>$arr['id'], 'field'=>$field->columnName() ,'custom'=>true,'name' => $arr['name'] . ':' . $arr['name'],'label' => $arr['name'] . ':' . $arr['name'], 'value' => '`cf:' . $category->name . ':' . $arr['name'] . '`', 'type' => $arr['datatype']);
				}
			}
		}
		return $response;
	}
}

