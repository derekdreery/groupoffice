<?php

/**
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 *
 */
class GO_Addressbook_Controller_Company extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Addressbook_Model_Company';

	protected function afterDisplay(&$response, &$model, &$params) {

		$response['data']['addressbook_name'] = $model->addressbook->name;

		$response['data']['google_maps_link'] = GO_Base_Util_Common::googleMapsLink(
										$model->address, $model->address_no, $model->city, $model->country);

		$response['data']['formatted_address'] = nl2br(GO_Base_Util_Common::formatAddress(
										$model->country, $model->address, $model->address_no, $model->zip, $model->city, $model->state
						));

		$response['data']['post_google_maps_link'] = GO_Base_Util_Common::googleMapsLink(
										$model->post_address, $model->post_address_no, $model->post_city, $model->post_country);

		$response['data']['post_formatted_address'] = nl2br(GO_Base_Util_Common::formatAddress(
										$model->post_country, $model->post_address, $model->post_address_no, $model->post_zip, $model->post_city, $model->post_state
						));

		$response['data']['employees'] = array();
		$stmt = $model->contacts();
		while ($contact = $stmt->fetch()) {
			$response['data']['employees'][] = array(
					'id' => $contact->id,
					'name' => $contact->name,
					'function' => $contact->function,
					'email' => $contact->email
			);
		}

		return parent::afterDisplay($response, $model, $params);
	}

	public function formatStoreRecord($record, $model, $store) {

		$record['name_and_name2'] = $model->name;

		if (!empty($model->name2))
			$record['name_and_name2'] .= ' - ' . $model->name2;

		$record['ab_name'] = $model->addressbook->name;

		return parent::formatStoreRecord($record, $model, $store);
	}

	protected function remoteComboFields() {
		return array(
				'addressbook_id' => '$model->addressbook->name'
		);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->addressbook_id);

		$stmt = $model->addresslists();
		while ($addresslist = $stmt->fetch()) {
			$response['data']['addresslist_' . $addresslist->id] = 1;
		}


		return parent::afterLoad($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		$stmt = GO_Addressbook_Model_Addresslist::model()->find();
		while ($addresslist = $stmt->fetch()) {
			$linkModel = $addresslist->hasManyMany('companies', $model->id);
			$mustHaveLinkModel = isset($params['addresslist_' . $addresslist->id]);
			if ($linkModel && !$mustHaveLinkModel) {
				$linkModel->delete();
			}
			if (!$linkModel && $mustHaveLinkModel) {
				$addresslist->addManyMany('companies', $model->id);
			}
		}

		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	/*
	 * This function initiates the contact filter by checked addressbooks.
	 */

	protected function getStoreMultiSelectProperties() {
		return array(
				'requestParam' => 'books',
				'permissionsModel' => 'GO_Addressbook_Model_Addressbook'
						//'titleAttribute'=>'name'
		);
	}

	/*
	 * This function initiates the contact filters by:
	 * - search query (happens automatically in GO base class)
	 * - by clicked letter
	 * - checked addresslists
	 */

	protected function getStoreParams($params) {


		$criteria = GO_Base_Db_FindCriteria::newInstance()
						->addModel(GO_Addressbook_Model_Company::model(), 't')
						->addInCondition('addressbook_id', $this->multiselectIds);

		// Filter by clicked letter
		if (!empty($params['clicked_letter'])) {
			if ($params['clicked_letter'] == '[0-9]') {
				$query = '^[0-9].*$';
				$query_type = 'REGEXP';
			} else {
				$query = $params['clicked_letter'] . '%';
				$query_type = 'LIKE';
			}
			$criteria->addCondition('name', $query, $query_type);
		}

		$storeParams = GO_Base_Db_FindParams::newInstance()
						->export("company")
						->criteria($criteria)
						->select('t.*t, ab.name AS addressbook_name')
						->joinModel(array(
				'model' => 'GO_Addressbook_Model_Addressbook',
				'localField' => 'addressbook_id',
				'tableAlias' => 'ab', //Optional table alias
						));

		if (!empty($params['addressbook_id'])) {
			$storeParams->getCriteria()->addCondition('addressbook_id', $params['addressbook_id']);
		}


		//if(empty($params['enable_addresslist_filter'])){
		// Filter by addresslist
		if (isset($params['addresslist_filter'])) {
			$addresslist_filter = json_decode(($params['addresslist_filter']), true);
			if (!empty($addresslist_filter)) {
				$storeParams->join(GO_Addressbook_Model_AddresslistCompany::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.company_id', '=', 't', true, true), 'ac'
				)->getCriteria()->addInCondition('addresslist_id', $addresslist_filter, 'ac');
			}
			GO::config()->save_setting('ms_addresslist_filter', implode(',', $addresslist_filter), GO::user()->id);
		}
//			elseif ($addresslist_filter = GO::config()->get_setting('ms_addresslist_filter', GO::user()->id))
//			{
//				$addresslist_filter = empty($addresslist_filter) ? array() : explode(',', $addresslist_filter);
//				$storeParams->join(GO_Addressbook_Model_AddresslistCompany::model()->tableName(),
//						GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.company_id', '=', 't', true, true),
//						'ac'
//					)->getCriteria()->addInCondition('addresslist_id', $addresslist_filter,'ac');
//			}
		//}

		return $storeParams;
	}

	protected function actionChangeAddressbook($params) {
		$ids = json_decode($params['items']);

		$response['success'] = true;
		$response['failedToMove'] = array();

		foreach ($ids as $id) {
			$model = GO_Addressbook_Model_Company::model()->findByPk($id);
			$failed_id = !(
							$model->setAttribute('addressbook_id', $params['book_id'])
							&& $model->save()) ? $id : null;
			if ($failed_id) {
				$response['failedToMove'][] = $failed_id;
				$response['success'] = false;
			}
		}

		return $response;
	}

	protected function actionMoveEmployees($params) {
		$to_company = GO_Addressbook_Model_Company::model()->findByPk($params['to_company_id']);
//		$to_company = $ab->get_company($_POST['to_company_id']);
//		$ab2 = new addressbook();
//		$ab->get_company_contacts($_POST['from_company_id']);

		$contacts = GO_Addressbook_Model_Contacts::model()->find(
						GO_Base_Db_FindCriteria::newInstance()
										->addCondition('company_id', $params['from_company_id'])
		);


		foreach ($contacts as $contact) {
			$attributes = array(
//				'id' => $contact->id,
					'addressbook_id' => $to_company->addressbook_id,
					'company_id' => $to_company->id
			);
			$contact->setAttributes($attributes);
			$contact->save();
//			$ab2->update_contact($up, false, $contact);
		}

		$response['success'] = true;
		return $response;
	}

	protected function beforeHandleAdvancedQuery($advQueryRecord, GO_Base_Db_FindCriteria &$criteriaGroup, GO_Base_Db_FindParams &$storeParams) {
		switch ($advQueryRecord['field']) {
			case 'employees.name':
				$storeParams->join(
								GO_Addressbook_Model_Contact::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()->addRawCondition('`t`.`id`', '`employees' . $advQueryRecord['id'] . '`.`company_id`'), 'employees' . $advQueryRecord['id']
				);
				$criteriaGroup->addRawCondition(
								'CONCAT_WS(\' \',`employees' . $advQueryRecord['id'] . '`.`first_name`,`employees' . $advQueryRecord['id'] . '`.`middle_name`,`employees' . $advQueryRecord['id'] . '`.`last_name`)', ':employee' . $advQueryRecord['id'], $advQueryRecord['comparator'], $advQueryRecord['andor'] == 'AND'
				);
				$criteriaGroup->addBindParameter(':employee' . $advQueryRecord['id'], $advQueryRecord['value']);
				return false;
				break;
			default:
				return true;
				break;
		}
	}

	protected function afterAttributes(&$attributes, &$response, &$params, GO_Base_Db_ActiveRecord $model) {
		//unset($attributes['t.company_id']);
		$attributes['employees.name'] = GO::t('cmdPanelEmployee', 'addressbook');
		return parent::afterAttributes($attributes, $response, $params, $model);
	}

}

