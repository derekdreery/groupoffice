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
 * The GO_Sites_Model_Page model
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Model_Tasklist.php 7607 2011-09-20 10:07:07Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 *
 * @property string register_user_groups Comma separated string with Group ids.
 * @property string $mod_rewrite_base_path
 * @property boolean $mod_rewrite
 * @property boolean $ssl
 * @property string $login_path
 * @property string $logout_path
 * @property string $register_path
 * @property string $reset_password_path
 * @property string $lost_password_path
 * @property string $template
 * @property string $domain
 * @property int $ctime
 * @property int $mtime
 * @property int $user_id
 * @property string $name
 * @property int $id
 */

class GO_Sites_Model_Site extends GO_Base_Db_ActiveRecord {

	/**
	 * The path of the latest page that you have visited before the current page.
	 * 
	 * @var string 
	 */
	private $_lastPath;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Sites_Model_Site 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	/**
	 * Get the tablename of this model
	 * 
	 * @return string The tablename of this model
	 */
	public function tableName() {
		return 'si_sites';
	}
	
	/**
	 * Get the relations of this model.
	 * 
	 * @return array The relational models 
	 */
	public function relations() {
		return array(
				'pages' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Page', 'field' => 'site_id', 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order('sort')->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('parent_id', 0)->addCondition('hidden',0)), 'delete' => true),
				);
	}	
	
	/**
	 * Set the last path property. This path is used to figure out what the last 
	 * page was before the current page.
	 * 
	 * @param string $path 
	 */
	public function setLastPath($path){
		$this->_lastPath = $path;
	}
	
	/**
	 * Get the last path property. This is usually the path of the page that you 
	 * have visited before the current page
	 *
	 * @return string The last path 
	 */
	public function getLastPath(){
		if(empty($this->_lastPath))
			$this->_lastPath = $this->getHomePagePath();
		return $this->_lastPath;
	}
	
	/**
	 * Get the path to the login page of this site.
	 * 
	 * @return string The path to the login page of this site
	 */	
	public function getLoginPath(){
		return $this->login_path;
	}
	
	/**
	 * Get the path to the logout page of this site.
	 *
	 * @return string The path to the logout page of this site
	 */
	public function getLogoutPath(){
		return $this->logout_path;
	}
	
	/**
	 * Get the path to the registration page of this site.
	 *
	 * @return string The path to the registration page of this site
	 */
	public function getRegisterPath(){
		return $this->register_path;
	}
	
	/**
	 * Get the path to the password reset page of this site.
	 * 
	 * @return string The path to the password reset page of this site
	 */
	public function getPasswordResetPath(){
		return $this->reset_password_path;
	}
	
	/**
	 * Get the path to the password recovery page of this site.
	 * 
	 * @return string The path to the password recovery page of this site
	 */
	public function getLostPasswordPath(){
		return $this->lost_password_path;
	}
	
	/**
	 * Get the path to the homepage of this site.
	 *
	 * @return string The path to the homepage of this site
	 */
	public function getHomePagePath(){
		return '';
	}
	
	/**
	 * Get the base url for this site
	 * 
	 * @param boolean $relative
	 * @return string The baseurl for this site
	 */
	public function getBaseUrl($relative=true){
		
		if($this->mod_rewrite){
			
			if($relative){
				return $this->mod_rewrite_base_path;
			}else
			{
				if($this->ssl)
					return "https://".$this->domain.$this->mod_rewrite_base_path;
				else
					return "http://".$this->domain.$this->mod_rewrite_base_path;
			}		
			
		} else {
			
			$url = $relative ? GO::config()->host : GO::config()->full_url;
			$url .= 'modules/sites/index.php?site_id='.$this->id;
			
			return $url;
		}
	}
	
	/**
	 * Get the url to the page of the given path
	 *
	 * @param string $path The path for the required page
	 * @param array $params Extra parameters
	 * @param boolean $relative Get relative url or not
	 * @param boolean $htmlspecialchars Set htmlspecialchars or not
	 * @return string url The url to the page
	 */
	public function pageUrl($path='', $params=array(), $relative=true, $htmlspecialchars=true){
		
		$url = $this->getBaseUrl($relative);
		
		if(empty($path) && empty($params))
			return $url;
		
		if(!empty($path)){
			if($this->mod_rewrite)
				$url .= $path;
			else
				$params['path']=$path;
		}
		
		$amp = $htmlspecialchars ? '&amp;' : '&';
		$amp = $this->mod_rewrite ? '?' : $amp;
		if($params){
			foreach($params as $name=>$value){
				$url .= $amp.$name.'='.urlencode($value);
				$amp = $htmlspecialchars ? '&amp;' : '&';
			}
		}

		return $url;
	}
	
}