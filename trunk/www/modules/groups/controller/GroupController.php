<?php
class GO_Groups_Controller_Group extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Base_Model_Group';
	
  /**
   * Retreive the group id from a given groupname.
   * 
   * @param String $groupname
   * @return int $id 
   */
//  public function actionIdFromName($groupname)
//  {
//    $group = $this->model->findSingleByAttribute('name', $groupname);
//    
//    return $group->id;
//  }
  
  /**
   * Add the username field to this default grid.
   * 
   * @param GO_Base_Provider_Grid $grid
   * @return GO_Base_Provider_Grid
   */
  protected function prepareGrid($grid){
    $grid->formatColumn('user_name','$model->user->name');
    return $grid;
  }
  
  /**
   * Retreive all users that belong to the given group.
   * 
   * @param int $id
   * @return array Users
   */
  public function actionGetUsers($id)
  {
    $group = GO_Base_Model_Group::model()->findByPk($id);
    
    $stmt = $group->users(GO_Base_Provider_Grid::getDefaultParams());
    
    $grid = new GO_Base_Provider_Grid($stmt,array(
        'name'=>array('format'=>'$model->name'),
        'username',
        'lastlogin'=>array('format'=>'GO_Base_Util_Date::get_timestamp($lastlogin)')
     ));
    $this->output($grid->getData());
  }
  
  
  /**
   * Delete a group by id
   *
   * @param int $id
   * @return bool $success 
   */
  public function actionDeleteGroup($id)
  {
    $group = $this->model->findByPk($id);
    return $group->delete();
  }
  
  /**
   * Clear a group.
   * Deletes every user that is inside the given group.
   * 
   * @param int $id 
   * @return bool $success 
   */
  public function actionClearGroup($id)
  {
    $group = $this->model->findByPk($id);
    
    // @TODO: Make relation to userGroup model in Group model and fix this deletion.
    
//    foreach($group->users as $user)
//    {
//      $user->delete();
//    }
    return true;
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
   * Delete a particular user from a Group.
   * 
   * @param int $group_id
   * @param int $user_id
   * @return bool $success 
   */
  public function actionDeleteUserFromGroup($group_id, $user_id)
  {
    $group = new GO_Base_Model_Group();
    
    $stmt = $group->users;
    while($user = $stmt->fetch())
    {
			if($user->id == $user_id)
        return $user->delete();
    }
    return false;
  }
  
  /**
   * Get the Group object by a given Id
   * 
   * @param int $id
   * @return object Group 
   */
  public function actionGetGroup($id)
  {
    return GO_Base_Model_Group::model()->findByPk($id);
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
  public function actionAddGroup($user_id,$name,$admin_only=false)
  {
    $group = new GO_Base_Model_Group();
    
    $group->user_id = $user_id;
    $group->name = $name;
    $group->admin_only = $admin_only;
    
    return $group->save();
  }
  
  
}