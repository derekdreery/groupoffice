<?php

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
        
    protected $isNew = true;


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
     *
     * @return bool 
     */
	protected function beforeSave() {
        if ($this->isNew) {
            $this->modified_user_id = GO::user()->id;
            $this->mtime = time();
            $this->status = 0;
        }
        return parent::beforeSave();
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
                $fnRow->isNew = false;
                $notifications[] = $fnRow;
        }
        return $notifications;               
    }                
}
