<?php

class GO_Email_Controller_Label extends GO_Base_Controller_AbstractModelController
{
    protected $model = "GO_Email_Model_Label";

    /**
     * Get store params
     *
     * @param array $params
     * @return GO_Base_Db_FindParams
     */
    protected function getStoreParams($params)
    {
        $criteria = GO_Base_Db_FindCriteria::newInstance()->addCondition('user_id', GO::user()->id);
        return GO_Base_Db_FindParams::newInstance()->criteria($criteria);
    }

    /**
     * Format store record
     *
     * @param $record
     * @param $model
     * @param $store
     * @return mixed
     */
    public function formatStoreRecord($record, $model, $store)
    {
        if (!empty($_POST['forContextMenu'])) {
            $record['text'] = $record['name'];
            $record['xtype'] = 'menucheckitem';
            unset($record['id']);
        }
        return $record;
    }

    /**
     * processStoreDelete
     *
     * @param $store
     * @param $params
     */
    protected function processStoreDelete($store, &$params)
    {
        if (isset($params['delete_keys'])) {

            $deleteRecords = json_decode($params['delete_keys'], true);
            $deleteRecords = array_filter($deleteRecords, 'intval');

            $criteria = GO_Base_Db_FindCriteria::newInstance();
            $criteria->addCondition('default', 0);
            $criteria->addInCondition('id', $deleteRecords);

            $findParams = GO_Base_Db_FindParams::newInstance()->criteria($criteria);
            $stmt = GO_Email_Model_Label::model()->find($findParams);

            $deleteRecords = array();
            while ($label = $stmt->fetch()) {
                $deleteRecords[] = $label->getPk();
            }

            if (!count($deleteRecords)) {
                $params['delete_keys'] = '[]';
            } else {
                $params['delete_keys'] = json_encode($deleteRecords);
            }
        }

        $store->processDeleteActions($params, $this->model);

        if (isset($params['delete_keys']) && !count($params['delete_keys'])) {
            $store->response['deleteSuccess'] = true;
        }
    }
}