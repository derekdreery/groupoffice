<?php

/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Calendar_Model_Participant model
 *
 * @package GO.modules.Calendar
 * @version $Id: GO_Calendar_Model_Participant.php 7607 2011-09-28 10:31:03Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $email
 * @property int $user_id
 * @property int $status
 * @property string $last_modified
 * @property int $is_organizer
 * @property string $role
 * 
 * @property GO_Calendar_Model_Event $event
 * @property string $statusName;
 * 
 * 
 */
class GO_Calendar_Model_Participant extends GO_Base_Db_ActiveRecord {

	const STATUS_TENTATIVE = 3;
	const STATUS_DECLINED = 2;
	const STATUS_ACCEPTED = 1;
	const STATUS_PENDING = 0;

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Participant
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function validate() {
		if (empty($this->name))
			$this->name = $this->email;

		return parent::validate();
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'cal_participants';
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'event' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'event_id'),
		);
	}

	/**
	 * Check if the participant is available.
	 * 
	 * Returns a questionmark if the particiant is not a user.
	 * 
	 * @return boolean/?
	 */
	public function isAvailable() {
		if (empty($this->user_id) || !$this->_hasFreeBusyAccess()) {
			return '?';
		} else {
			return self::userIsAvailable($this->event->start_time, $this->event->end_time, $this->user_id, $this->event);
		}
	}

	/**
	 * @todo
	 */
	private function _hasFreeBusyAccess() {
		return true;
	}

	/**
	 * Check if a user has events between two given times.
	 * 
	 * @param type $periodStartTime
	 * @param type $periodEndTime
	 * @param type $userId
	 * @param type $ignoreEvent
	 * @return boolean 
	 */
	public static function userIsAvailable($periodStartTime, $periodEndTime, $userId, $ignoreEvent = false) {

		$findParams = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl();

		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addRawCondition('t.calendar_id', 'c.id');

		$findParams->join(GO_Calendar_Model_Calendar::model()->tableName(), $joinCriteria, 'c');

		$findParams->getCriteria()->addCondition('user_id', $userId, '=', 'c');

		if ($ignoreEvent) {
			$findParams->getCriteria()
							->addModel(GO_Calendar_Model_Event::model())
							->addCondition('id', $ignoreEvent->id, '!=')
							->addCondition('uuid', $ignoreEvent->uuid, '!=')
			;
		}

		$events = GO_Calendar_Model_Event::model()->findCalculatedForPeriod($findParams, $periodStartTime, $periodEndTime, true);

		return count($events) == 0;
	}

	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['user_id'] = 0;
		return $attr;
	}

	public function getSecurityToken() {
		return md5($this->event_id . $this->email . $this->event->ctime);
	}

	public function getStatusName() {
		switch ($this->status) {
			case self::STATUS_TENTATIVE :
				return GO::t('tentative','calendar');
				break;

			case self::STATUS_DECLINED :
				return GO::t('declined','calendar');
				break;

			case self::STATUS_ACCEPTED :
				return GO::t('accepted','calendar');
				break;

			default:
				return GO::t('notRespondedYet','calendar');
				break;
		}
	}

}