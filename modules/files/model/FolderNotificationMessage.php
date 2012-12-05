<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.files.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Files_Model_FolderNotificationMessage model
 *
 * @package GO.modules.files.model
 * @property int $modified_user_id
 * @property int $type
 * @property string $arg1
 * @property string $arg2
 * @property int $mtime
 * @property boolean $status
 */

class GO_Files_Model_FolderNotificationMessage extends GO_Base_Db_ActiveRecord {
        
    const ADD_FOLDER = 1;
    const RENAME_FOLDER = 2;
    const MOVE_FOLDER = 3;
    const DELETE_FOLDER = 4;

    const ADD_FILE = 5;
    const RENAME_FILE = 6;
    const MOVE_FILE = 7;
    const DELETE_FILE = 8;
    const UPDATE_FILE = 9;      

    /**
     *
     * @param sring $className
     * @return object 
     */
    public static function model($className=__CLASS__) {
            return parent::model($className);
    }

    /**
     *
     * @return string 
     */
    public function tableName() {
            return 'fs_notification_messages';
    }
        
    /**
     * Get unsent notifications by user
     * 
     * @param int $user_id
     * 
     * @return array 
     */
    public static function getNotifications($user_id) {

        $user_id = (int)$user_id;
        if (!$user_id)
            $user_id = GO::user()->id;

        $stmt = self::model()->findByAttributes(
                array(
                    'user_id' => GO::user()->id,
                    'status'  => 0
                )
        );

        $notifications = array();
        while ($fnRow = $stmt->fetch()) {
                $notifications[] = $fnRow;
        }
        return $notifications;               
    }
    
    public function defaultAttributes() {
        $attr = parent::defaultAttributes();
        
        $attr['modified_user_id'] = GO::user()->id;
        $attr['mtime'] = time();
        $attr['status'] = 0;
        
        return $attr;
    }
}
