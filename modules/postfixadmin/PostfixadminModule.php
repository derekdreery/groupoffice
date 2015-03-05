<?php

class GO_Postfixadmin_PostfixadminModule extends GO_Base_Module {

	public function install() {

		parent::install();		
		
		$domains = empty(GO::config()->serverclient_domains) ? array() : explode(',', GO::config()->serverclient_domains);

		foreach ($domains as $domain) {
			if (!empty($domain)) {
				
				$domainModel = new GO_Postfixadmin_Model_Domain();
				$domainModel->domain=$domain;
				$domainModel->save();
				
				$mailboxModel = new GO_Postfixadmin_Model_Mailbox();
				$mailboxModel->domain_id=$domainModel->id;
				$mailboxModel->username='admin@'.$domain;
				$mailboxModel->password='admin';
				$mailboxModel->name="System administrator";
				$mailboxModel->save();				
			}
		}
	}
}