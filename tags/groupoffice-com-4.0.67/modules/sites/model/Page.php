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
 * @version $Id: GO_Tasks_Model_Tasklist.php 7607 2011-09-20 10:07:07Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property GO_Sites_Model_Site $site
 * @property string $controller_action
 * @property string $controller
 * @property boolean $login_required
 * @property int $sort
 * @property boolean $hidden
 * @property string $content
 * @property string $template
 * @property string $path
 * @property string $keywords
 * @property string $description
 * @property string $title
 * @property string $name
 * @property int $mtime
 * @property int $ctime
 * @property int $user_id
 * @property int $site_id
 * @property int $parent_id
 * @property int $id
 */

class GO_Sites_Model_Page extends GO_Base_Db_ActiveRecord {

		
	private $_attachedJS = array();
	private $_attachedCSS = array();
	private $_attachedCustom = array();
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Sites_Model_Page 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {
		$this->columns['content']['gotype']='html';
		$this->columns['path']['unique']=array('site_id');
		
		return parent::init();
	}
	
		
	public function defaultAttributes() {
		return array(
				'parent_id'=>0,
				'user_id'=>GO::user()->id,
				'name'=>GO::t('newPage', 'sites'),
				'title'=>GO::t('newPage', 'sites'),
				'template'=>'Example',
				'content'=>GO::t('yourContent', 'sites'),
				'hidden'=>true,
				'sort'=>0,
				'controller_action'=>'index',
				'controller'=>'GO_Sites_Controller_Site',
				'login_required'=>false
		);
	}

	public function tableName() {
		return 'si_pages';
	}
	
	public function relations() {
		return array(
				'pages' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Page', 'field' => 'parent_id', 'delete' => true),
				'site'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Sites_Model_Site", 'field'=>'site_id')
				);
	}	
	
	public function getParent(){
		if($this->parent_id==0){
			return $this->site();
		}else
		{
			return GO_Sites_Model_Page::model()->findByPk($this->parent_id);
		}
	}	
	
	public function getUrl($params=array(),$relative=true, $htmlspecialchars=true){		
		return $this->site->pageUrl($this->path, $params, $relative, $htmlspecialchars);
	}
	
		
	public function renderHeaderIncludes(){
		
		$output = '';
		
		foreach($this->_attachedCSS as $cssFile){
			///<link href="/groupoffice/modules/sites/templates/Example/css/tabs.css" rel="stylesheet" type="text/css" />
			$output .= '<link href="'.$cssFile.'" rel="stylesheet" type="text/css" />'."\n";
		}
		
		foreach($this->_attachedJS as $jsFile){
			//<script type="text/javascript" src="myscript.js"></script>
			$output .= '<script type="text/javascript" src="'.$jsFile.'"></script>'."\n";
		}
		
		foreach($this->_attachedCustom as $jsCustom){
			$output .= $jsCustom."\n";
		}
		
		echo $output;
	}
	
	public function attachCustomHeaderScript($script){
		$this->_attachedCustom[] = $script;
	}
	
	public function attachHeaderInclude($type='css', $url){
		if($type=='js')
			$this->_attachedJS[] = $url;
		else
			$this->_attachedCSS[] = $url;
	}
	
//	public static function createDefaultPages($site_id){
//
//		$defaultPages = array(
//				'login'=>array('controller'=>'GO_Sites_Controller_User','template'=>'login','action'=>'login','title'=>'Login'),
//				'logout'=>array('controller'=>'GO_Sites_Controller_User','template'=>'logout','action'=>'logout','title'=>'Logout'),
//				'register'=>array('controller'=>'GO_Sites_Controller_User','template'=>'register','action'=>'register','title'=>'Register'),
//				'resetpassword'=>array('controller'=>'GO_Sites_Controller_User','template'=>'resetpassword','action'=>'resetpassword','title'=>'Reset Password'),
//				'lostpassword'=>array('controller'=>'GO_Sites_Controller_User','template'=>'lostpassword','action'=>'recover','title'=>'Lost Password')
//				);
//		
//		foreach($defaultPages as $p=>$c){
//			$page = new GO_Sites_Model_Page();
//			$page->site_id = $site_id;
//			$page->path = $p;
//			$page->name = $c['title'];
//			$page->title = $c['title'];
//			$page->controller = $c['controller'];
//			$page->controller_action = $c['action'];
//			$page->template = $c['template'];
//			$page->save();
//		}
//	}
}