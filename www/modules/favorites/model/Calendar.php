<?php
/**
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @package GO.modules.Favorites
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */
 
/**
 * The GO_Favorites_Model_Calendar model
 *
 * @package GO.modules.Favorites
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 *
 * @property int $user_id
 * @property int $calendar_id
 * @property int $sort
 */

class GO_Favorites_Model_Calendar extends \GO\Base\Db\ActiveRecord{

	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return 'fav_calendar';
	 }
	 
	 public function primaryKey() {
		 return array('user_id','calendar_id');
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array(
				 'calendar' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Calendar', 'field' => 'calendar_id', 'delete' => false),
		 );
	 }	
}