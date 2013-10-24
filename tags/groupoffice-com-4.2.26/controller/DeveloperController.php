<?php

class GO_Core_Controller_Developer extends GO_Base_Controller_AbstractController {

	protected function allowGuests() {
		return array('testvobject');
	}
	
	protected function init() {
		
		if(!GO::config()->debug)
			throw new Exception("Developer controller can only be accessed in debug mode");
		
		return parent::init();
	}

	public function actionCreateManyUsers($params) {
		
		if(!GO::user()->isAdmin())
			throw new Exception("You must be logged in as admin");
		
		$amount = 1000;
		$prefix = 'user';
		$domain = 'intermesh.dev';

		for ($i = 0; $i < $amount; $i++) {		

			echo "Creating $prefix$i\n";
			
			$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $prefix . $i);
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

			if (GO::modules()->isInstalled('email') && GO::modules()->isInstalled('postfixadmin')) {

				$domainModel = GO_Postfixadmin_Model_Domain::model()->findSingleByAttribute('domain', $domain);

				if (!$domainModel) {
					$domainModel = new GO_Postfixadmin_Model_Domain();
					$domainModel->domain = $domain;
					$domainModel->save();
				}

				$mailboxModel = GO_Postfixadmin_Model_Mailbox::model()->findSingleByAttributes(array('domain_id' => $domainModel->id, 'username' => $user->email));

				if (!$mailboxModel) {
					$mailboxModel = new GO_Postfixadmin_Model_Mailbox();
					$mailboxModel->domain_id = $domainModel->id;
					$mailboxModel->username = $user->email;
					$mailboxModel->password = $prefix . $i;
					$mailboxModel->name = $user->name;	
					$mailboxModel->save();	
				}
				
				
				
				$accountModel = GO_Email_Model_Account::model()->findSingleByAttributes(array('user_id'=>$user->id, 'username'=>$user->email));
				
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
		
		GO::session()->runAsRoot();
		
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
		
	
		
		$vobject = GO_Base_VObject_Reader::read($ical_str);
		
		$event = new GO_Calendar_Model_Event();
		$event->importVObject($vobject->vevent[0]);
		
		var_dump($event->getAttributes());
	}
	
	
	protected function actionGrouped($params){
		
		$stmt = GO_Base_Model_Grouped::model()->load(
						'GO_Calendar_Model_Event',
						'c.name', 
						'c.name, count(*) AS count',
						GO_Base_Db_FindParams::newInstance()
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
		GO_Base_Model_User::model()->addRelation('events', array(
				'type'=>  GO_Base_Db_ActiveRecord::HAS_MANY, 
				'model'=>'GO_Calendar_Model_Event', 
				'field'=>'user_id'				
		));
		
		
		$stmt = GO::user()->events;
		
		foreach($stmt as $event){
			echo $event->toHtml();
			echo '<hr>';
		}
		
	}
	
	
	protected function actionGroupRelation($params){
		GO_Base_Model_User::model()->addRelation('events', array(
				'type'=>  GO_Base_Db_ActiveRecord::HAS_MANY, 
				'model'=>'GO_Calendar_Model_Event', 
				'field'=>'user_id'				
		));
		
		$fp = GO_Base_Db_FindParams::newInstance()->groupRelation('events', 'count(events.id) as eventCount');

				
		$stmt = GO_Base_Model_User::model()->find($fp);
		
		foreach($stmt as $user){
			echo $user->name.': '.$user->eventCount."<br />";
			echo '<hr>';
		}
		
	}
	
	
	protected function actionCreateEvents($params){
		
		$now = GO_Base_Util_Date::clear_time(time());
		
		for($i=0;$i<30;$i++){
			$time = GO_Base_Util_Date::date_add($now, -$i);
			
			for($n=0;$n<10;$n++){
				
				$event = new GO_Calendar_Model_Event();
				$event->name = 'test '.$n;
				
				$event->description = str_repeat('All work and no play, makes Jack a dull boy. ',100);
				
				$event->start_time = GO_Base_Util_Date::date_add($time, 0,0,0,$n+7);
				$event->end_time = GO_Base_Util_Date::date_add($time, 0,0,0,$n+8);
				
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
		
		
		$tags = GO_Base_Util_TagParser::getTags('site:img', $content);
		
		var_dump($tags);
		
		
	}
	
	
	protected function actionJoinRelation($params){
		$product = GO_Billing_Model_Product::model()->findByPk(426	);
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->order(array('book.name', 'order.btime'),array('ASC','DESC'))
						->joinRelation('order.book');
		
		$findParams->getCriteria()
						->addCondition('product_id', $product->id)
						->addCondition('btime', time(), '<', 'order')
						->addCondition('btime', 0, '>', 'order');
		
		$stmt = GO_Billing_Model_Item::model()->find($findParams);
		
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
		
//		GO::unsetDbConnection();
		
		$stmt = GO_Base_Model_User::model()->find();
		sleep(10);
		
		echo "Done";
		
	}
	
	
	protected function actionDefaultVat(){
		
		$order = GO_Billing_Model_Order::model()->findSingle();
		
		$item = new GO_Billing_Model_Item();
		$item->description="test";
		$item->amount=1;
		$item->unit_price=10;
		$item->order_id=$order->id;
		$item->save();
		
		$order->syncItems();
		
		echo $order->order_id;
		
	}
}
