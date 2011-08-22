<?php
/**
 * GROUPOFFICE authentication backend
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Wesley Smits <wsmits@intermesh.nl>
 * @company    Intermesh B.V. (http://www.intermesh.nl)
 * @product    Group-office (http://www.group-office.com)
 * 
 */

class auth_groupoffice extends auth_basic {

  /**
   * Constructor of this authentication class
   */
  function auth_groupoffice()
  {
    $this->cando['external'] = true;
    $this->cando['logoff'] = false;
    $this->success = true;
  }

  /**
   * Set the options that a user can do when logged in to Dokuwiki
   * 
   * @param type $cap
   * @return type 
   */
  function canDo($cap) {
    switch($cap)
    {
      case 'Profile':
        // can at least one of the user's properties be changed?
        return ( $this->cando['modPass']  ||
                 $this->cando['modName']  ||
                 $this->cando['modMail'] );
        break;
      case 'UserMod':
        // can at least anything be changed?
        return ( $this->cando['getGroups'] || 
                 $this->cando['modPass']   ||
                 $this->cando['modName']   ||
                 $this->cando['modMail']   ||
                 $this->cando['modLogin']  ||
                 $this->cando['modGroups'] ||
                 $this->cando['modMail'] );
        break;
      default:
        // print a helping message for developers
        if(!isset($this->cando[$cap])){
          msg("Check for unknown capability '$cap' - Do you use an outdated Plugin?",-1);
        }
        return $this->cando[$cap];
    }
  }

  /**
   * Get the user data to display in Dokuwiki
   * 
   * @param string $user The username of the user
   * @return array Array with user data 
   */
  function getUserData($user)
  {
		global $conf, $GO_USERS, $GO_CONFIG;
    
    require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
    $GO_USERS = new GO_USERS();

		$gouser = $GO_USERS->get_user_by_username($user);
		if(empty($gouser))
			return false;
		
		$data = array('name'=>$GO_USERS->get_user_realname($gouser['id']),'mail'=>$gouser['email'],'grps'=>$this->getGroups($gouser['id']));
		
		return $data;
  }
  
  /**
   * Check for Group-Office login and auto login to Dokuwiki when inside Group-Office.
   * 
   * @global type $USERINFO
   * @global type $conf
   * @global type $lang
   * @param type $user
   * @param type $pass
   * @param type $sticky
   * @return type 
   */
  function trustExternal($user,$pass,$sticky=false)
  {
    global $USERINFO;
    global $conf;
    global $lang;

    $sticky ? $sticky = true : $sticky = false; //sanity check
    
    if(isset($_COOKIE['groupoffice']))
    {
      $GO_SID=$_COOKIE['groupoffice'];
    }else
    {
      $GO_SID=false;
    }

//    var_dump($_SESSION['GO_SESSION']['GO_SID']);
//    var_dump($GO_SID);
    
    if(!isset($_SESSION['GO_SESSION']['GO_SID']) || $_SESSION['GO_SESSION']['GO_SID']!=$GO_SID){
      //Group-Office session id changed. Someone else logged in.      
      if(!$this->logOff())
        throw new Exception('The GO session is not closed properly.');
    }
    
    if ($GO_SID && empty($_SESSION['GO_SESSION']['user_id'])) 
    {
      $fname = session_save_path() . "/sess_" . $GO_SID;
      if (file_exists($fname)) 
      {
        $data = file_get_contents($fname);
        $data = $this->unserializesession($data);
        
        $_SESSION['GO_SESSION'] = $data['GO_SESSION'];
        $_SESSION['GO_SESSION']['GO_SID']=$GO_SID;
      }
    }
    
    if(!empty($_SESSION['GO_SESSION']['user_id']))
    {           
      $USERINFO['name'] = $_SESSION['GO_SESSION']['name'];
      $USERINFO['mail'] = $_SESSION['GO_SESSION']['email'];
      
      $USERINFO['grps'] = $this->getGroups($_SESSION['GO_SESSION']['user_id']);
            
      $_SERVER['REMOTE_USER'] = $_SESSION['GO_SESSION']['username'];
      $_SESSION[DOKU_COOKIE]['auth']['user'] = $_SESSION['GO_SESSION']['username'];
      $_SESSION[DOKU_COOKIE]['auth']['pass'] = $pass;
      $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
      return true;
    }
    else if(!empty($user))
    {
      return $this->checkPass($user, $pass);
    }
    else
    {
      // to be sure
      auth_logoff();
      return false;
    }
  }
  
//  /**
//   * Retrieve groups [implement only where required/possible]
//   *
//   * Set getGroups capability when implemented
//   *
//   * @author  Chris Smith <chris@jalakai.co.uk>
//   * @return  array
//   */
//  function retrieveGroups($start=0,$limit=0) {
//    return $this->getGroups($_SESSION['GO_SESSION']['user_id']);
//  }

  /**
   * Log out
   * 
   * @return type 
   */
  function logOff() 
  {
    unset($_SESSION[DOKU_COOKIE]);
    unset($_SESSION['GO_SESSION']);
    //session_destroy();
    return true;
  }
  
  /**
   *
   * Check the given username and password against the Group-Office users.
   * 
   * @global type $conf
   * @global GO_EVENTS $GO_EVENTS
   * @global type $USERINFO
   * @global type $GO_SECURITY
   * @param type $user
   * @param type $pass
   * @return type 
   */
  function checkPass($user, $pass)
  {
    global $conf, $GO_EVENTS,$USERINFO,$GO_SECURITY;
    define('GO_NO_SESSION', true);
    //unset($_SESSION['GO_SESSION']['config_file']);
    //$_SESSION['GO_SESSION']['config_file'] = $conf['GO_root'].'config.php';
    require_once($conf['GO_php']);
    
    require_once($GO_CONFIG->class_path.'base/events.class.inc.php');
    $GO_EVENTS = new GO_EVENTS();

    require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
    $GO_AUTH = new GO_AUTH();

    if (!$GO_AUTH->login($user, $pass, 'dokuwiki', false))
      return false;

    if (!$GO_SECURITY->logged_in()) // Check for login registration is ok.
      return false;
    
    if(!$GO_SECURITY->json_authenticate('dokuwiki')) // Check if the user has access to the dokuwiki module.
      return false;
    
    $this->checkRights();
        
    $USERINFO['name'] = $_SESSION['GO_SESSION']['name'];
    $USERINFO['mail'] = $_SESSION['GO_SESSION']['email'];

    $USERINFO['grps'] = $this->getGroups($_SESSION['GO_SESSION']['user_id']);

    $_SERVER['REMOTE_USER'] = $_SESSION['GO_SESSION']['username'];
    $_SESSION[DOKU_COOKIE]['auth']['user'] = $_SESSION['GO_SESSION']['username'];
    $_SESSION[DOKU_COOKIE]['auth']['pass'] = $pass;
    $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
    return true;
  }  
    
  /**
   * Check which rights the user has.
   * 
   * @global type $GO_MODULES 
   */
  function checkRights()
  {
    global $GO_MODULES;
    
    if($GO_MODULES->modules['dokuwiki']['write_permission'])
      $this->canDo('UserMod');
    else
      $this->canDo('Profile');
  }
  
  /**
   *
   * @global GO_CONFIG $conf
   * @param int $usr_id
   * @return array groupnames  
   */
  function getGroups($usr_id)
  {
    global $conf, $GO_CONFIG;
    require_once($conf['GO_php']);
    
    require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
    $groups = new GO_GROUPS();
    
    $groupnames = $groups->get_groupnames_by_user($usr_id);
   
    return $groupnames;
  }
  
  
  /**
   * Unserialize the Group-Office session
   * 
   * @param type $data
   * @return type 
   */
  function unserializesession($data) 
  {
    $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
    $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    for ($i = 0; isset($vars[$i]); $i++)
      $result[$vars[$i++]] = unserialize($vars[$i]);
    return $result;
  }
}

