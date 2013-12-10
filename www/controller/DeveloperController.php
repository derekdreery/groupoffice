<?php

class GO_Core_Controller_Developer extends \GO\Base\Controller\AbstractController {

	protected function allowGuests() {
		return array('testvobject', 'namespace');
	}
	
	protected function init() {
		
		if(!\GO::config()->debug)
			throw new Exception("Developer controller can only be accessed in debug mode");
		
		return parent::init();
	}
	
	
	public function actionNamespace(){
		
		$root = dirname(__FILE__).'/../';
		chdir($root);
		
		$cmd = 'find go/base -name "*.php"';
		exec($cmd, $output);
		
		foreach($output as $classFile){
			
			$parts = explode('/', substr($classFile, 0,-4));
			
			for($i=0;$i<count($parts);$i++){
				$parts[$i]=$i==0 ? 'GO' : ucfirst($parts[$i]);
			}
			
			$this->_replaceNamespace(implode('_', $parts));
		}
	}
	
	private function _replaceNamespace($className){
	
		echo "Replacing $className\n";
//		return;
		
		$test = false;
			
		$newClass = str_replace('_','\\', $className);
		
		$parts = explode('\\',$newClass);
		$newClassDefinition = array_pop($parts);
		$namespace = implode('\\',$parts);
		
		$root = dirname(__FILE__).'/../';
		chdir($root);
		
		if(!$test){
			$cmd = 'find controller go/base modules views/Extjs3 \( ! -name updates.php \)  -name "*.php"';
			exec($cmd, $output);
			$output[]="go/GO.php";
		}else
		{		
			$output = array('go/base/model/Acl.php');
		}
		
		foreach($output as $file){
			
		
			
			$path = GO::config()->root_path.$file;
			$content = $oldContent = file_get_contents($path);
			
			$count = 0;
			$content = preg_replace("/class\s+".preg_quote($className,'/')."([^A-Za-z0-9]+)/", "namespace ".preg_quote($namespace,'/').";\n\nclass $newClassDefinition$1", $content, -1, $count);
			
			$content = preg_replace('/class(\s+\w+\s)extends\s([A-Za-z_]+)/',"class$1extends \\\\$2", $content);

			$content = preg_replace('/\\\\'.preg_quote($className, '/').'([^A-Za-z0-9]+)/', '\\'.$newClass.'$1', $content);
			
			$content = preg_replace('/'.preg_quote($className, '/').'([^A-Za-z0-9]+)/', '\\'.$newClass.'$1', $content);
			
			
			if($count>0){				
				echo "In class declaration\n";				
				$content = preg_replace('/\\\\'.preg_quote($newClass, '/').'([^A-Za-z0-9]+)/', $newClassDefinition.'$1', $content);
			}
			
			if($test){
				echo $content;
			}else{			
				if ($content != $oldContent) {
						echo "Replacing in ".$file."\n";
					$content = file_put_contents($path, $content);
				}
			}
		}
		
		echo "All done!\n";
		

	}

	public function actionCreateManyUsers($params) {
		
		if(!\GO::user()->isAdmin())
			throw new Exception("You must be logged in as admin");
		
		$amount = 1000;
		$prefix = 'user';
		$domain = 'intermesh.dev';

		for ($i = 0; $i < $amount; $i++) {		

			echo "Creating $prefix$i\n";
			
			$user = \GO_Base_Model_User::model()->findSingleByAttribute('username', $prefix . $i);
			if(!$user){
				$user = new GO_Base_Model_User();
				$user->username = $prefix . $i;
				$user->email = $prefix . $i . '@' . $domain;
				$user->password = $prefix . $i;
				$user->first_name = $prefix;
				$user->last_name = $i;
				if(!$user->save()){
					var_dump($user->getValidationErrors());
					exit();
				}
				$user->checkDefaultModels();
			}

			if (\GO::modules()->isInstalled('email') && \GO::modules()->isInstalled('postfixadmin')) {

				$domainModel = \GO_Postfixadmin_Model_Domain::model()->findSingleByAttribute('domain', $domain);

				if (!$domainModel) {
					$domainModel = new GO_Postfixadmin_Model_Domain();
					$domainModel->domain = $domain;
					$domainModel->save();
				}

				$mailboxModel = \GO_Postfixadmin_Model_Mailbox::model()->findSingleByAttributes(array('domain_id' => $domainModel->id, 'username' => $user->email));

				if (!$mailboxModel) {
					$mailboxModel = new GO_Postfixadmin_Model_Mailbox();
					$mailboxModel->domain_id = $domainModel->id;
					$mailboxModel->username = $user->email;
					$mailboxModel->password = $prefix . $i;
					$mailboxModel->name = $user->name;	
					$mailboxModel->save();	
				}
				
				
				
				$accountModel = \GO_Email_Model_Account::model()->findSingleByAttributes(array('user_id'=>$user->id, 'username'=>$user->email));
				
				if(!$accountModel){
					$accountModel = new GO_Email_Model_Account();
					$accountModel->user_id = $user->id;
					$accountModel->host = "localhost";
					$accountModel->port = 143;

					$accountModel->name = $user->name;
					$accountModel->username = $user->email;

					$accountModel->password = $prefix . $i;

					$accountModel->smtp_host = 'localhost';
					$accountModel->smtp_port = 25;
					$accountModel->save();

					$accountModel->addAlias($user->email, $user->name);
				}
			}
		}
		
		echo "Done\n\n";
	}
	
	
	public function actionTestVObject($params){
		
		\GO::session()->runAsRoot();
		
		$ical_str='BEGIN:VCALENDAR
VERSION:1.0
BEGIN:VEVENT
UID:762
SUMMARY:weekly test
DTSTART:20040503T160000Z
DTEND:20040503T170000Z
X-EPOCAGENDAENTRYTYPE:APPOINTMENT
CLASS:PUBLIC
DCREATED:20040502T220000Z
RRULE:W1 MO #0
LAST-MODIFIED:20040503T101900Z
PRIORITY:0
STATUS:NEEDS ACTION
END:VEVENT
END:VCALENDAR';
		
	
		
		$vobject = \GO_Base_VObject_Reader::read($ical_str);
		
		$event = new GO_Calendar_Model_Event();
		$event->importVObject($vobject->vevent[0]);
		
		var_dump($event->getAttributes());
	}
	
	
	protected function actionGrouped($params){
		
		$stmt = \GO_Base_Model_Grouped::model()->load(
						'GO_Calendar_Model_Event',
						'c.name', 
						'c.name, count(*) AS count',
						\GO_Base_Db_FindParams::newInstance()
						->joinModel(array(
								'model'=>'GO_Calendar_Model_Calendar',
								'localField'=>'calendar_id',
								'tableAlias'=>'c'
						))
						);
		
		echo '<pre>';
		
		foreach($stmt as $calendar){
			echo $calendar->name.' : '.$calendar->count."\n";
		}
		
	}
	
	protected function actionAddRelation($params){
		\GO_Base_Model_User::model()->addRelation('events', array(
				'type'=>  \GO_Base_Db_ActiveRecord::HAS_MANY, 
				'model'=>'GO_Calendar_Model_Event', 
				'field'=>'user_id'				
		));
		
		
		$stmt = \GO::user()->events;
		
		foreach($stmt as $event){
			echo $event->toHtml();
			echo '<hr>';
		}
		
	}
	
	
	protected function actionGroupRelation($params){
		\GO_Base_Model_User::model()->addRelation('events', array(
				'type'=>  \GO_Base_Db_ActiveRecord::HAS_MANY, 
				'model'=>'GO_Calendar_Model_Event', 
				'field'=>'user_id'				
		));
		
		$fp = \GO_Base_Db_FindParams::newInstance()->groupRelation('events', 'count(events.id) as eventCount');

				
		$stmt = \GO_Base_Model_User::model()->find($fp);
		
		foreach($stmt as $user){
			echo $user->name.': '.$user->eventCount."<br />";
			echo '<hr>';
		}
		
	}
	
	
	protected function actionCreateEvents($params){
		
		$now = \GO_Base_Util_Date::clear_time(time());
		
		for($i=0;$i<30;$i++){
			$time = \GO_Base_Util_Date::date_add($now, -$i);
			
			for($n=0;$n<10;$n++){
				
				$event = new GO_Calendar_Model_Event();
				$event->name = 'test '.$n;
				
				$event->description = str_repeat('All work and no play, makes Jack a dull boy. ',100);
				
				$event->start_time = \GO_Base_Util_Date::date_add($time, 0,0,0,$n+7);
				$event->end_time = \GO_Base_Util_Date::date_add($time, 0,0,0,$n+8);
				
				$event->save();
					
				
				
			}			
		}		
	}
	
	protected function actionTest($params){
		
		$content = '<html>
			
		<site:img id="1" lightbox="1" path="testing">
		<img src="blabla" />
		</site:img>
		

		<site:img id="2" lightbox="0" path="testing2"><img src="blabla2" /></site:img>

		<site:img id="2" lightbox="0" path="testing3"></site:img>
		
<p>Paragraph</p>
';
		
		
		$tags = \GO_Base_Util_TagParser::getTags('site:img', $content);
		
		var_dump($tags);
		
		
	}
	
	
	protected function actionJoinRelation($params){
		$product = \GO_Billing_Model_Product::model()->findByPk(426	);
		
		$findParams = \GO_Base_Db_FindParams::newInstance()
						->order(array('book.name', 'order.btime'),array('ASC','DESC'))
						->joinRelation('order.book');
		
		$findParams->getCriteria()
						->addCondition('product_id', $product->id)
						->addCondition('btime', time(), '<', 'order')
						->addCondition('btime', 0, '>', 'order');
		
		$stmt = \GO_Billing_Model_Item::model()->find($findParams);
		
		$item = $stmt->fetch();
		
		//no queries needed to get this value
		echo $item->order->book->name;
	}
	
	
	protected function actionTestParams($test1,$test2,$hasDefault=true){
		
		var_dump($test1);
		
		var_dump($test2);
		
		var_dump($hasDefault);
		
	}
	
	
	protected function actionTestDbClose(){
		
//		\GO::unsetDbConnection();
		
		$stmt = \GO_Base_Model_User::model()->find();
		sleep(10);
		
		echo "Done";
		
	}
	
	
	protected function actionDefaultVat(){
		
		$order = \GO_Billing_Model_Order::model()->findSingle();
		
		$item = new GO_Billing_Model_Item();
		$item->description="test";
		$item->amount=1;
		$item->unit_price=10;
		$item->order_id=$order->id;
		$item->save();
		
		$order->syncItems();
		
		echo $order->order_id;
		
	}
	
	
	protected function actionDuplicateCF(){
		
		$stmt = \GO_Customfields_Model_Category::model()->findByModel("GO_Projects2_Model_Project");
		$stmt->callOnEach('delete');
		
		$sql = "DROP TABLE IF EXISTS cf_pr2_projects";
		\GO::getDbConnection()->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `cf_pr2_projects` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB;";
		\GO::getDbConnection()->query($sql);
		

		$stmt = \GO_Customfields_Model_Category::model()->findByModel("GO_Projects_Model_Project");

		foreach($stmt as $category){
			$category->duplicate(array(
					'extends_model'=>"GO_Projects2_Model_Project"
			));
		}
		
		
		
		
		
		
		
		

		
		
		$sql = "INSERT INTO cf_pr2_projects SELECT * FROM cf_pm_projects";
		\GO::getDbConnection()->query($sql);


		$stmt = \GO_Customfields_Model_Category::model()->findByModel("GO_Projects2_Model_TimeEntry");
		$stmt->callOnEach('delete');
		
		
		$sql = "DROP TABLE IF EXISTS cf_pr2_hours";
		\GO::getDbConnection()->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `cf_pr2_hours` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB;";
		\GO::getDbConnection()->query($sql);

		$stmt = \GO_Customfields_Model_Category::model()->findByModel("GO_Projects_Model_Hour");

		foreach($stmt as $category){
			$category->duplicate(array(
					'extends_model'=>"GO_Projects2_Model_TimeEntry"
			));
		}

		
		
		$sql = "INSERT INTO cf_pr2_hours SELECT * FROM cf_pm_hours";
		\GO::getDbConnection()->query($sql);
			

	}
	
}
