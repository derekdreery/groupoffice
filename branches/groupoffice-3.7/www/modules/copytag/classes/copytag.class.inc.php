<?php

class copytag extends db {
  
  private $_identifier = '@';
  
  public function __on_load_listeners($events) {
		$events->add_listener('calendar_add_event', __FILE__, 'copytag', 'copy_event');
    $events->add_listener('before_add_event', __FILE__, 'copytag', 'before_copy_event');
    
    $events->add_listener('task_add_event', __FILE__, 'copytag', 'copy_task');
    $events->add_listener('before_add_task', __FILE__, 'copytag', 'before_copy_task');
	}
  
  public static function before_copy_task(&$task, &$response) {
    GLOBAL $GO_MODULES;
    require_once ($GO_MODULES->modules['tasks']['class_path']."tasks.class.inc.php");
    $tsk = new tasks();
    
    $task_subject = $task['name'];
    
    $ct = new copytag();
    $tags = $ct->_getAllTags();
    
    $i = 0;
    foreach($tags as $tag)
    {
      $tagComplete = $ct->_identifier.$tag['tag'];
      $found = strpos($task_subject,$tagComplete);
      if($found !== false)
      {
        $tasklist = $tsk->get_default_tasklist($tag['user_id']);
        $task['name']= str_replace($tagComplete, '', $task['name']); // remove the tag from the task name

        $response[$i]['new_tasklist'] = $tasklist;
        $i++;
      }
              
    }
  }
  
  public static function copy_task($task, $before_task_response) {
    GLOBAL $GO_MODULES;
    require_once ($GO_MODULES->modules['tasks']['class_path']."tasks.class.inc.php");
    $tsk = new tasks();
    
    for($i=0; $i< count($before_task_response); $i++)
    {
      $tasklist = $before_task_response[$i]['new_tasklist'];
    
      unset($task['id'], $task['acl_id']);
      $task['tasklist_id']=$tasklist['id'];
      $tsk->add_task($task, $tasklist);
    }
  }
  
  public static function before_copy_event(&$event, &$response){
    GLOBAL $GO_MODULES;
    require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
    $cal = new calendar();
    
    $event_subject = $event['name'];
    
    $ct = new copytag();
    $tags = $ct->_getAllTags();
    
    $i = 0;
    foreach($tags as $tag)
    {
      $tagComplete = $ct->_identifier.$tag['tag'];
      $found = strpos($event_subject,$tagComplete);
      if($found !== false)
      {
        $calendar = $cal->get_default_calendar($tag['user_id']);
        $event['name']= str_replace($tagComplete, '', $event['name']); // remove the tag from the event name
        //$response['new_event'] = $event;
        $response[$i]['new_calendar'] = $calendar;
        $i++;
      }
              
    }
  }
  
  public static function copy_event($event, $before_event_response) {
    GLOBAL $GO_MODULES;
    require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
    $cal = new calendar();
    
    for($i=0; $i< count($before_event_response); $i++)
    {
      $calendar = $before_event_response[$i]['new_calendar'];
    
      unset($event['id'], $event['resource_event_id'],$event['uuid'],$event['files_folder_id']);
      $event['calendar_id']=$calendar['id'];
      $cal->add_event($event, $calendar);
    }
  }
    
  private function _getAllTags() {
    $sql = "SELECT tag, user_id FROM ct_tags";
    $this->query($sql);
    $tags=array();
    while($tag=$this->next_record())
       $tags[]=$tag;
    return $tags;
  }
  
  
  /**
   * Clear the whole table.
   * This will be done when changing the tags grid in the frontend.
   * (After this the new values of the table need to be added.)
   * 
   * @return boolean success
   */
  public function clearTable(){
    $sql = 'TRUNCATE TABLE ct_tags;';
    $this->query($sql);
    return true;
  }  
  
  /**
   * Get the data of this grid.
   * 
   * @param string $query
   * @param string $sortfield
   * @param string $sortorder
   * @param int $start
   * @param int $offset
   * @return data The grid data 
   */
  public function getGridData($query='', $sortfield='last_name', $sortorder='ASC', $start=0, $offset=0)
  {
    if($sortfield == 'user')
      $sortfield = 'last_name';

    $sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "* FROM go_users u LEFT JOIN ct_tags t ON u.id=t.user_id ";
		$types='';
		$params=array();
		if(!empty($query))
 		{
 			$sql .= " WHERE t.tag LIKE ?";
 			$types .= 's';
 			$params[]=$query;
 		} 		
		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);	
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		$this->query($sql, $types, $params);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
  }
  
  /**
   * Public function to check if a tag allready exists.
   * 
   * @param string $tag
   * @return boolean exists 
   */  
  public function tagExists($tag) {
    return $this->_isExistingTag($tag);
  }
  
  /**
   * Add a new tag to the database
   * 
   * @param int $user
   * @param string $tag
   * @return boolean success 
   */
  public function addTag($user,$tag) {
   
    $tag = $this->_verifyTag($tag);
    
    // Check already exists
    if($this->_isExistingTag($tag))
      return false;
    
    $sql = "INSERT INTO ct_tags (`user_id`,`tag`) VALUES ('".intval($user)."','".$tag."');";
    $this->query($sql);
    return true;
  }
 
  public function removeTag($user,$tag) {
    if(!$this->_isExistingTag($tag))
    {
      throw new Exception("Tag doesn't exist, so it cannot be deleted!");  
    }
    else
    {
      $sql = "DELETE FROM ct_tags WHERE `user_id`='".intval($user)."' AND `tag`='".$tag."');";
      $this->query($sql);
      return true;
    }
  }
  
  /**
   * Internal function to check the tag for errors
   * 
   * @param string $tag
   * @return string $tag modified 
   */
  private function _verifyTag($tag) {
    
    $found = strpos($tag,$this->_identifier);
    
    if($found === 0)
      $tag = substr($tag,1);
    
    
    
    return $tag;
  }
  
  /**
   * Internal function to check if a tag allereeady exists.
   * 
   * @param string $tag
   * @return bool exist 
   */
  private function _isExistingTag($tag) {

    $sql = "SELECT * FROM ct_tags WHERE `tag`='".$tag."';";
    $this->query($sql);
    if($this->found_rows() > 0)
      return true;
  }
  
  
}