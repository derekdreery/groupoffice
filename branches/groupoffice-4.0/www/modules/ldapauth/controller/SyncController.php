<?php
class GO_Ldapauth_Controller_Sync extends GO_Base_Controller_AbstractController{
	
	protected function allowGuests() {
		return array("users", "lookupuser");
	}
	
	
	protected function actionLookupUser($params){
		
		$this->requireCli();		
		$this->checkRequiredParameters(array('uid'), $params);
		
		$la = new GO_Ldapauth_Authenticator();
		
		$ldapConn = GO_Base_Ldap_Connection::getDefault();
		
		$result = $ldapConn->search(GO::config()->ldap_peopledn, 'uid='.$params['uid']);
		$record = $result->fetch();
		$attr = $record->getAttributes();
		
		var_dump($attr);
		
	}
	
	/**
	 * 
	 * php /var/www/groupoffice-4.0/www/groupofficecli.php -r=ldapauth/sync/users --delete=1 --max_delete_percentage=34 --dry=1
	 * 
	 * @param type $params
	 * @throws Exception
	 */
	protected function actionUsers($params){
		
		
		$this->requireCli();		
		GO::session()->runAsRoot();
		
		$dryRun = !empty($params['dry']);
		
		if($dryRun)
			echo "Dry run enabled.\n\n";
		
		$la = new GO_Ldapauth_Authenticator();
	
		$ldapConn = GO_Base_Ldap_Connection::getDefault();
		
		$result = $ldapConn->search(GO::config()->ldap_peopledn, 'uid=*');
		
		//keep an array of users that exist in ldap. This array will be used later for deletes.
		//admin user is not in ldap but should not be removed.
		$usersInLDAP = array(1);
				
		$i=0;
		while($record = $result->fetch()){
			$i++;
			
			try{
				if(!$dryRun){
					$user = $la->syncUserWithLdapRecord($record);			
					$username = $user->username;
				}else
				{
					$attr = $la->getUserAttributes($record);		
					$username = $attr['username'];
					$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $attr['username']);
				}
				
				echo "Synced ".$username."\n";
			} catch(Exception $e){
				echo "ERROR:\n";
				echo (string) $e;
				
				echo "LDAP record:";
				var_dump($record->getAttributes());
			}
			
			if(!$dryRun)
				$this->fireEvent("ldapsyncuser", array($user, $record));
			
			if($user)
				$usersInLDAP[]=$user->id;
			
//			if($i==100)
//				exit("Reached 100. Exitting");
		}
		
		
		
		$stmt = GO_Base_Model_User::model()->find();
		
		$totalInGO = $stmt->rowCount();
		$totalInLDAP = count($usersInLDAP);
		
		echo "Users in Group-Office: ".$totalInGO."\n";
		echo "Users in LDAP: ".$totalInLDAP."\n";
		
		if(!empty($params['delete'])){
			$percentageToDelete = round((1-$totalInLDAP/$totalInGO)*100);
			
			$maxDeletePercentage = isset($params['max_delete_percentage']) ? intval($params['max_delete_percentage']) : 5;

			if($percentageToDelete>$maxDeletePercentage)
				die("Delete Aborted because script was about to delete more then $maxDeletePercentage% of the users (".$percentageToDelete."%, ".($totalInGO-$totalInLDAP)." users)\n");

			while($user = $stmt->fetch()){
				if(!in_array($user->id, $usersInLDAP)){
					echo "Deleting ".$user->username."\n";
					if(!$dryRun)
						$user->delete();
				}
			}			
		}
		
		echo "Done\n\n";
		
		//var_dump($attr);
		
	}
}