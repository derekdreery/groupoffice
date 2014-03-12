<?php

namespace GO\Email\Model;

use GO;
use GO\Base\Db\ActiveRecord;

/**
 * Class Label
 *
 * @property int id
 * @property string name
 * @property string flag
 * @property string color
 * @property int user_id
 * @property boolean default
 */
class Label extends ActiveRecord
{

    /**
     * Returns a static model of itself
     *
     * @param String $className
     *
     * @return Label
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Returns the table name
     */
    public function tableName()
    {
        return 'em_labels';
    }

    /**
     * Get count of user labels
     *
     * @param int $user_id User ID
     *
     * @return int
     */
    public function getLabelsCount($user_id)
    {
        if ($user_id == 0) {
            $user_id = GO::user()->user_id;
        }

        $sql = "SELECT count(*) FROM `{$this->tableName()}` WHERE user_id = " . intval($user_id);
        $stmt = $this->getDbConnection()->query($sql);
        return intval($stmt->fetchColumn(0));
    }

    /**
     * Delete user labels
     *
     * @param int $user_id User ID
     *
     * @return bool
     */
    public function deleteUserLabels($user_id)
    {
        if ($user_id == 0) {
            $user_id = GO::user()->user_id;
        }

        $sql = "DELETE FROM `{$this->tableName()}` WHERE user_id = " . intval($user_id);
        $stmt = $this->getDbConnection()->query($sql);
        return $stmt->execute();
    }

    /**
     * Create default user labels
     *
     * @param int $user_id User ID
     *
     * @return bool
     */
    public function createDefaultLabels($user_id)
    {
        $labelsCount = $this->getLabelsCount($user_id);

        if ($labelsCount >= 5) {
            return false;
        }

        if ($labelsCount > 0 && $labelsCount < 5) {
            $this->deleteUserLabels($user_id);
        }

        $colors = array(
            1 => '7A7AFF',
            2 => '59BD59',
            3 => 'FFBD59',
            4 => 'FF5959',
            5 => 'BD7ABD'
        );

        for ($i = 1; $i < 6; $i++) {
            $label = new Label;
            $label->user_id = $user_id;
            $label->name = 'Label ' . $i;
            $label->flag = '$label' . $i;
            $label->color = $colors[$i];
            $label->default = true;
            $label->save();
        }

        return true;
    }

    protected function init()
    {
        $this->columns['name']['unique'] = true;
        parent::init();
    }

    protected function beforeSave()
    {
        if ($this->isNew && $this->getLabelsCount(GO::user()->id) == 10) {
            throw new Exception(sprintf(GO::t('labelsLimit', 'email'), 10));
        }

        if (!$this->default) {
            $flag = preg_replace('~[^\\pL0-9_]+~u', '-', $this->name);
            $flag = trim($flag, "-");
            $flag = iconv("utf-8", "us-ascii//TRANSLIT", $flag);
            $flag = strtolower($flag);
            $this->flag = preg_replace('~[^-a-z0-9_]+~', '', $flag);
        }
        return true;
    }

    public function getUserLabels()
    {
        $labels = array();

        $stmt = Label::model()->findByAttribute('user_id', GO::user()->id);
        while ($label = $stmt->fetch()) {
            $labels[$label->flag] = $label;
        }

        return $labels;
    }
}
