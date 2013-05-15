<?php
class GO_Demodata_Controller_Demodata extends GO_Base_Controller_AbstractController {

	protected function actionCreate($params){
		
		if(!GO::user()->isAdmin())
			throw new GO_Base_Exception_AccessDenied();
	
		
		$category = GO_Customfields_Model_Category::model()->createIfNotExists("GO_Addressbook_Model_Contact", "Demo Custom fields");
		
		$types = GO_Customfields_CustomfieldsModule::getCustomfieldTypes();
		foreach($types as $t){
			GO_Customfields_Model_Field::model()->createIfNotExists($category->id, $t['type'],array(
					'datatype'=>$t['className'],
					'helptext'=>($t['className']=="GO_Customfields_Customfieldtype_Text" ? "Some help text for this field" : "")
					));
		}
		


		$addressbook = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('name', GO::t('customers', 'addressbook'));
		if (!$addressbook) {
			$addressbook = new GO_Addressbook_Model_Addressbook();
			$addressbook->setAttributes(array(
					'user_id' => 1,
					'name' => GO::t('prospects', 'addressbook'),
					//				'default_iso_address_format' => $default_language,
					'default_salutation' => GO::t('defaultSalutation', 'addressbook')
			));
			$addressbook->save();
			$addressbook->acl->addGroup(GO::config()->group_internal, GO_Base_Model_Acl::WRITE_PERMISSION);
		}
		
		$company = GO_Addressbook_Model_Company::model()->findSingleByAttribute('email', 'info@smith.demo');
		if (!$company) {
			$company = new GO_Addressbook_Model_Company();
			$company->setAttributes(array(
					'addressbook_id' => $addressbook->id,
					'name' => 'Smith Inc',
					'address' => 'Kalverstraat',
					'address_no' => '1',
					'zip' => '1012 NX',
					'city' => 'Amsterdam',
					'state' => 'Noord-Holland',
					'country' => 'NL',
					'post_address' => 'Kalverstraat',
					'post_address_no' => '1',
					'post_zip' => '1012 NX',
					'post_city' => 'Amsterdam',
					'post_state' => 'Noord-Brabant',
					'post_country' => 'NL',
					'phone' => '+31 (0) 10 - 1234567',
					'fax' => '+31 (0) 1234567',
					'email' => 'info@smith.demo',
					'homepage' => 'http://www.smith.demo',
					'bank_no' => '',
					'vat_no' => 'NL 1234.56.789.B01',
					'user_id' => 1,
					'comment' => 'Just a demo company'
			));
			$company->save();
		}

		$john = GO_Addressbook_Model_Contact::model()->findSingleByAttribute('email', 'john@smith.demo');
		if (!$john) {
			$john = new GO_Addressbook_Model_Contact();
			$john->addressbook_id = $addressbook->id;
			$john->company_id = $company->id;
			$john->salutation = 'Dear Mr. Smith';
			$john->first_name = 'John';
			$john->last_name = 'Smith';
			$john->function = 'CEO';
			$john->cellular = '06-12345678';
			$john->email = 'john@smith.demo';
			$john->address = 'Kalverstraat';
			$john->address_no = '1';
			$john->zip = '1012 NX';
			$john->city = 'Amsterdam';
			$john->state = 'Noord-Holland';
			$john->country = 'NL';
			$john->save();
			$john->setPhoto(GO::modules()->addressbook->path . 'install/noperson.jpg');
		}

		$acme = GO_Addressbook_Model_Company::model()->findSingleByAttribute('email', 'info@acme.demo');
		if (!$acme) {
			$acme = new GO_Addressbook_Model_Company();
			$acme->setAttributes(array(
					'addressbook_id' => $addressbook->id,
					'name' => 'ACME Corporation',
					'address' => '1111 Broadway',
					'address_no' => '',
					'zip' => '10019',
					'city' => 'New York',
					'state' => 'NY',
					'country' => 'US',
					'post_address' => '1111 Broadway',
					'post_address_no' => '',
					'post_zip' => '10019',
					'post_city' => 'New York',
					'post_state' => 'NY',
					'post_country' => 'US',
					'phone' => '(555) 123-4567',
					'fax' => '(555) 123-4567',
					'email' => 'info@acme.demo',
					'homepage' => 'http://www.acme.demo',
					'bank_no' => '',
					'vat_no' => 'US 1234.56.789.B01',
					'user_id' => 1,
					'comment' => 'The name Acme became popular for businesses by the 1920s, when alphabetized business telephone directories such as the Yellow Pages began to be widespread. There were a flood of businesses named Acme (some of these still survive[1]). For example, early Sears catalogues contained a number of products with the "Acme" trademark, including anvils, which are frequently used in Warner Bros. cartoons.[2]'
			));
			$acme->save();

			$acme->addComment("The company is never clearly defined in Road Runner cartoons but appears to be a conglomerate which produces every product type imaginable, no matter how elaborate or extravagant - none of which ever work as desired or expected. In the Road Runner cartoon Beep, Beep, it was referred to as \"Acme Rocket-Powered Products, Inc.\" based in Fairfield, New Jersey. Many of its products appear to be produced specifically for Wile E. Coyote; for example, the Acme Giant Rubber Band, subtitled \"(For Tripping Road Runners)\".");
			$acme->addComment("Sometimes, Acme can also send living creatures through the mail, though that isn't done very often. Two examples of this are the Acme Wild-Cat, which had been used on Elmer Fudd and Sam Sheepdog (which doesn't maul its intended victim); and Acme Bumblebees in one-fifth bottles (which sting Wile E. Coyote). The Wild Cat was used in the shorts Don't Give Up the Sheep and A Mutt in a Rut, while the bees were used in the short Zoom and Bored.");
		}
		$wile = GO_Addressbook_Model_Contact::model()->findSingleByAttribute('email', 'wile@acme.demo');
		if (!$wile) {
			$wile = new GO_Addressbook_Model_Contact();
			$wile->addressbook_id = $addressbook->id;
			$wile->company_id = $acme->id;
			$wile->salutation = 'Dear Mr. Coyote';
			$wile->first_name = 'Wile';
			$wile->middle_name = 'E.';
			$wile->last_name = 'Coyote';
			$wile->function = 'CEO';
			$wile->cellular = '06-12345678';
			$wile->email = 'wile@acme.demo';
			$wile->address = '1111 Broadway';
			$wile->address_no = '';
			$wile->zip = '10019';
			$wile->city = 'New York';
			$wile->state = 'NY';
			$wile->country = 'US';

			$wile->save();
			$wile->setPhoto(GO::modules()->addressbook->path . 'install/wecoyote.png');

			$wile->addComment("Wile E. Coyote (also known simply as \"The Coyote\") and The Road Runner are a duo of cartoon characters from a series of Looney Tunes and Merrie Melodies cartoons. The characters (a coyote and Greater Roadrunner) were created by animation director Chuck Jones in 1948 for Warner Bros., while the template for their adventures was the work of writer Michael Maltese. The characters star in a long-running series of theatrical cartoon shorts (the first 16 of which were written by Maltese) and occasional made-for-television cartoons.");

			$wile->addComment("In each episode, instead of animal senses and cunning, Wile E. Coyote uses absurdly complex contraptions (sometimes in the manner of Rube Goldberg) and elaborate plans to pursue his quarry. It was originally meant to parody chase cartoons like Tom and Jerry, but became popular in its own right, much to Jones' chagrin.");

			$file = new GO_Base_Fs_File(GO::modules()->addressbook->path . 'install/Demo letter.docx');
			$copy = $file->copy($wile->filesFolder->fsFolder);

			$wile->filesFolder->addFile($copy->name());
		}


		$dt = GO_Addressbook_Model_Template::model()->findSingleByAttribute('name', 'Letter');
		if (!$dt) {
			$dt = new GO_Addressbook_Model_Template();
			$dt->type = GO_Addressbook_Model_Template::TYPE_DOCUMENT;
			$dt->content = file_get_contents(GO::modules()->addressbook->path . 'install/letter_template.docx');
			$dt->extension = 'docx';
			$dt->name = 'Letter';
			$dt->save();
		}

		GO::config()->password_validate=false;

		$elmer = GO_Base_Model_User::model()->findSingleByAttribute('username', 'elmer');
		if (!$elmer) {
			$elmer = new GO_Base_Model_User();
			$elmer->username = 'elmer';
			$elmer->first_name = 'Elmer';
			$elmer->last_name = 'Fudd';
			$elmer->email = 'elmer@acmerpp.demo';
			$elmer->password='demo';
			if ($elmer->save()) {
				$this->_setUserContact($elmer);
				$elmer->checkDefaultModels();
			}
		}
		



		$demo = GO_Base_Model_User::model()->findSingleByAttribute('username', 'demo');
		if (!$demo) {
			$demo = new GO_Base_Model_User();
			$demo->username = 'demo';
			$demo->first_name = 'Demo';
			$demo->last_name = 'User';
			$demo->email = 'demo@acmerpp.demo';
			$demo->password='demo';
			if ($demo->save()) {
				$this->_setUserContact($demo);
				$demo->checkDefaultModels();
			}
		}


		$linda = GO_Base_Model_User::model()->findSingleByAttribute('username', 'linda');
		if (!$linda) {
			$linda = new GO_Base_Model_User();
			$linda->username = 'linda';
			$linda->first_name = 'Linda';
			$linda->last_name = 'Smith';
			$linda->email = 'linda@acmerpp.demo';
			$linda->password='demo';
							
			if ($linda->save()) {
				$this->_setUserContact($linda);
				$linda->checkDefaultModels();
			}
		}

		if (GO::modules()->calendar) {

			$events = array(
					array('Project meeting', 10),
					array('Meet Wile', 12),
					array('MT Meeting', 14)
			);

			foreach ($events as $e) {
				$event = new GO_Calendar_Model_Event();
				$event->name = $e[0];
				$event->location = "ACME NY Office";
				$event->start_time = GO_Base_Util_Date::clear_time(time(), $e[1]);
				$event->end_time = $event->start_time + 3600;
				$event->user_id = $demo->id;
				$event->calendar_id = GO_Calendar_Model_Calendar::model()->getDefault($demo)->id;
				$event->save();

				$participant = new GO_Calendar_Model_Participant();
				$participant->is_organizer = true;
				$participant->setContact($demo->createContact());
				$event->addParticipant($participant);

				$participant = new GO_Calendar_Model_Participant();
				$participant->setContact($linda->createContact());
				$event->addParticipant($participant);

				$participant = new GO_Calendar_Model_Participant();
				$participant->setContact($elmer->createContact());
				$event->addParticipant($participant);


				$participant = new GO_Calendar_Model_Participant();
				$participant->setContact($wile);
				$event->addParticipant($participant);

				$wile->link($event);
			}



			$events = array(
					array('Project meeting', 11),
					array('Meet John', 13),
					array('MT Meeting', 16)
			);

			foreach ($events as $e) {
				$event = new GO_Calendar_Model_Event();
				$event->name = $e[0];
				$event->location = "ACME NY Office";
				$event->start_time = GO_Base_Util_Date::date_add(GO_Base_Util_Date::clear_time(time(), $e[1]), 1);
				$event->end_time = $event->start_time + 3600;
				$event->user_id = $linda->id;
				$event->calendar_id = GO_Calendar_Model_Calendar::model()->getDefault($linda)->id;
				$event->save();

				$participant = new GO_Calendar_Model_Participant();
				$participant->is_organizer = true;
				$participant->setContact($linda->createContact());
				$event->addParticipant($participant);


				$participant = new GO_Calendar_Model_Participant();
				$participant->setContact($demo->createContact());
				$event->addParticipant($participant);


				$participant = new GO_Calendar_Model_Participant();
				$participant->setContact($john);
				$event->addParticipant($participant);

				$john->link($event);
			}
			
			
			$view = new GO_Calendar_Model_View();
			$view->name=GO::t('group_everyone');
			$view->save();			
			$view->addManyMany('groups', GO::config()->group_everyone);
			
			
			$view = new GO_Calendar_Model_View();
			$view->name=GO::t('group_everyone').' ('.GO::t('merge', 'calendar').')';
			$view->merge=true;
			$view->owncolor=true;
			$view->save();			
			$view->addManyMany('groups', GO::config()->group_everyone);
			
			
		}
		
		if(GO::modules()->tasks){			
			$task = new GO_Tasks_Model_Task();
			$task->tasklist_id=  GO_Tasks_Model_Tasklist::model()->getDefault($demo)->id;
			$task->name='Feed the dog';
			$task->start_time=time();
			$task->due_time=GO_Base_Util_Date::date_add(time(),2);
			$task->save();			
			
			
			$task = new GO_Tasks_Model_Task();
			$task->tasklist_id=  GO_Tasks_Model_Tasklist::model()->getDefault($linda)->id;
			$task->name='Feed the dog';
			$task->start_time=time();
			$task->due_time=GO_Base_Util_Date::date_add(time(),1);
			$task->save();			
			
			$task = new GO_Tasks_Model_Task();
			$task->tasklist_id=  GO_Tasks_Model_Tasklist::model()->getDefault($elmer)->id;
			$task->name='Feed the dog';
			$task->start_time=time();
			$task->due_time=GO_Base_Util_Date::date_add(time(),1);
			$task->save();
			
			
			
			$task = new GO_Tasks_Model_Task();
			$task->tasklist_id=  GO_Tasks_Model_Tasklist::model()->getDefault($demo)->id;
			$task->name='Prepare meeting';
			$task->start_time=time();
			$task->due_time=GO_Base_Util_Date::date_add(time(),1);
			$task->save();			
			$task->link($wile);
			$task->link($event);
			
			
			$task = new GO_Tasks_Model_Task();
			$task->tasklist_id=  GO_Tasks_Model_Tasklist::model()->getDefault($linda)->id;
			$task->name='Prepare meeting';
			$task->start_time=time();
			$task->due_time=GO_Base_Util_Date::date_add(time(),1);
			$task->save();			
			$task->link($wile);
			$task->link($event);
			
			$task = new GO_Tasks_Model_Task();
			$task->tasklist_id=  GO_Tasks_Model_Tasklist::model()->getDefault($elmer)->id;
			$task->name='Prepare meeting';
			$task->start_time=time();
			$task->due_time=GO_Base_Util_Date::date_add(time(),1);
			$task->save();
			$task->link($wile);
			$task->link($event);
		}
		

		if(GO::modules()->billing){		
			
			$rocket = GO_Billing_Model_Product::model()->findSingleByAttribute('article_id', '12345');
			if (!$rocket) {
				$rocket = new GO_Billing_Model_Product();
				$rocket->article_id=12345;
				$rocket->supplier_company_id=$acme->id;
				$rocket->unit='pcs';
				$rocket->cost_price=1000;
				$rocket->list_price=2999.99;
				$rocket->total_price=2999.99;
				$rocket->vat=0;
				if(!$rocket->save())
					var_dump($rocket->getValidationErrors ());
			
			
			
			
				$lang = new GO_Billing_Model_ProductLanguage();
				$lang->language_id=1;
				$lang->product_id=$rocket->id;
				$lang->name='Master Rocket 1000';
				$lang->description='Master Rocket 1000. The ultimate rocket to blast rocky mountains.';
				$lang->save();
			}
			
			$rocketLauncher = GO_Billing_Model_Product::model()->findSingleByAttribute('article_id', '234567');
			if (!$rocketLauncher) {
				$rocketLauncher = new GO_Billing_Model_Product();
				$rocketLauncher->article_id=234567;
				$rocketLauncher->supplier_company_id=$acme->id;
				$rocketLauncher->unit='pcs';
				$rocketLauncher->cost_price=3000;
				$rocketLauncher->list_price=8999.99;
				$rocketLauncher->total_price=8999.99;
				$rocketLauncher->vat=0;
				if(!$rocketLauncher->save())
					var_dump($rocket->getValidationErrors ());



				$lang = new GO_Billing_Model_ProductLanguage();
				$lang->language_id=1;
				$lang->product_id=$rocketLauncher->id;
				$lang->name='Rocket Launcher 1000';
				$lang->description='Rocket Launcher 1000. Required to launch rockets.';
				$lang->save();
			}
			
			
			$books = GO_Billing_Model_Book::model()->find();
			foreach($books as $book){			
				$order = new GO_Billing_Model_Order();
				$order->book_id=$book->id;
				$order->btime=time();
				$order->setCustomerFromContact($john);			
				$order->setCustomerFromCompany($company);			
				$order->save();

				$order->addProduct($rocketLauncher, 1);
				$order->addProduct($rocket, 4);
				
				$status = $book->statuses(GO_Base_Db_FindParams::newInstance()->single());
				$order->status_id=$status->id;
				$order->syncItems();
				
				
				
				$order = new GO_Billing_Model_Order();
				$order->book_id=$book->id;
				$order->btime=time();
				$order->setCustomerFromContact($wile);			
				$order->setCustomerFromCompany($acme);			
				$order->save();

				$order->addProduct($rocketLauncher, 1);
				$order->addProduct($rocket, 10);
				
				$status = $book->statuses(GO_Base_Db_FindParams::newInstance()->single());
				$order->status_id=$status->id;
				$order->syncItems();
			}
			
			
			
		}
		
		
		GO::modules()->demodata->delete();

		$this->redirect();
		
		
	}

	private function _setUserContact($user) {
		$contact = $user->createContact();

		$company = GO_Addressbook_Model_Company::model()->findSingleByAttribute('name', 'ACME Rocket Powered Products');
		if (!$company) {
			$company = new GO_Addressbook_Model_Company();
			$company->setAttributes(array(
					'addressbook_id' => $contact->addressbook_id,
					'name' => 'ACME Rocket Powered Products',
					'address' => '1111 Broadway',
					'address_no' => '',
					'zip' => '10019',
					'city' => 'New York',
					'state' => 'NY',
					'country' => 'US',
					'post_address' => '1111 Broadway',
					'post_address_no' => '',
					'post_zip' => '10019',
					'post_city' => 'New York',
					'post_state' => 'NY',
					'post_country' => 'US',
					'phone' => '(555) 123-4567',
					'fax' => '(555) 123-4567',
					'email' => 'info@acmerpp.demo',
					'homepage' => 'http://www.acmerpp.demo',
					'bank_no' => '',
					'vat_no' => 'US 1234.56.789.B01',
					'user_id' => 1,
					'comment' => 'The name Acme became popular for businesses by the 1920s, when alphabetized business telephone directories such as the Yellow Pages began to be widespread. There were a flood of businesses named Acme (some of these still survive[1]). For example, early Sears catalogues contained a number of products with the "Acme" trademark, including anvils, which are frequently used in Warner Bros. cartoons.[2]'
			));
			$company->save();
		}

		$contact->company_id = $company->id;
		$contact->function = 'CEO';
		$contact->cellular = '06-12345678';
		$contact->address = '1111 Broadway';
		$contact->address_no = '';
		$contact->zip = '10019';
		$contact->city = 'New York';
		$contact->state = 'NY';
		$contact->country = 'US';
		$contact->save();
		
		
		
	}

}