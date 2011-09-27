<?php
class GO_Groups_Controller_Group extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Base_Model_Group';
  
//  /**
//   * Add the username field to this default grid.
//   * 
//   * @param GO_Base_Data_Store $grid
//   * @return GO_Base_Data_Store
//   */
//  protected function prepareGrid(GO_Base_Data_Store $grid){
//    $grid->formatColumn('user_name','$model->user->name');
//    return $grid;
//  }
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
		return parent::formatColumns($columnModel);
	}
  
  /**
   * Retreive all users that belong to the given group.
   * 
   * @param int $id
   * @return array Users
   */
  public function actionGetUsers($params)
  { 
    $group = GO_Base_Model_Group::model()->findByPk($params['id']);
    
    if(empty($group))
      $group = new GO_Base_Model_Group();
    
    if(isset($params['add_users']) && !empty($group->id))
    {
      $users = json_decode($params['add_users']);
      foreach($users as $usr_id)
      {
        if(!$group->addUser($usr_id))
          var_dump($usr_id); // TODO: create error messages
      }
    }
    
    $grid = new GO_Base_Data_Store(array(
        'id',
        'name'=>array('format'=>'$model->name'),
        'username',
        'email'
     ));
		
		$gridParams = $grid->getDefaultParams(array(
        'joinCustomFields'=>false
    ));

    // The users in the group "everyone" cannot be deleted

    $delresponse = array();
    if($group->id != GO::config()->group_everyone)
    { 
      $grid->processDeleteActions($params, 'GO_Base_Model_UserGroup', array('group_id'=>$group->id));
    }
    else
    {
      $delresponse['deleteSuccess'] = false;
      $delresponse['deleteFeedback'] = 'Members of the group everyone cannot be deleted.';
    }
    
    $stmt = $group->users($gridParams);
    $grid->setStatement($stmt);
    
    $response = $grid->getData();
    
    $response = array_merge($response,$delresponse);
    
    return $response;
  }

  /**
   * Add the given user to the given group.
   * 
   * @param int $group_id
   * @param int $user_id
   * @return bool $success 
   */
  public function actionAddUserToGroup($group_id, $user_id)
  {
    $group = new GO_Base_Model_Group();
    $group->addUser($user_id);
    return $userGroup->save();
  }
  
  /**
   * Update the params of a given Group
   * 
   * @param int $id
   * @param String $name
   * @param bool $admin_only
   * @return bool $success 
   */
  public function actionUpdateGroup($id, $name, $admin_only=-1)
  {
    $group = $this->model->findByPk($id);
    
    $group->id = $id;
    $group->name = $name;
    $group->admin_only = $admin_only;
        
    return $group->save();
  }
  
  /**
   *  Create a new group
   * 
   * @param int $user_id
   * @param String $name
   * @param bool $admin_only
   * @return bool $success 
   */
  public function actionSaveGroup()
  {
    $group = new GO_Base_Model_Group();
    $group->setAttributes($_POST);
    $group->user_id=GO::user()->id;
    
    return $group->save();
  }
  
  
}