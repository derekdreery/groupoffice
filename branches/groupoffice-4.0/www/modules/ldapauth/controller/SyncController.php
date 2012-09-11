<?php
class GO_Ldapauth_Controller_Sync extends GO_Base_Controller_AbstractController{
	
	protected function allowGuests() {
		return array("users");
	}
	
//	protected function ignoreAclPermissions() {
//		return array("*");
//	}
	
//	protected function actionTest($params){
//		
//		$la = new GO_Ldapauth_Authenticator();
//		
//		$ldapConn = GO_Base_Ldap_Connection::getDefault();
//		
//		$result = $ldapConn->search(GO::config()->ldap_basedn, 'uid=john');
//		$record = $result->fetch();
//		
//		var_dump($record);
//		
//	}
	
	/**
	 * 
	 * php /var/www/groupoffice-4.0/www/groupofficecli.php -r=ldapauth/sync/users --delete=1 --max_delete_percentage=34
	 * 
	 * @param type $params
	 * @throws Exception
	 */
	protected function actionUsers($params){
		
		
		$this->requireCli();		
		GO::session()->runAsRoot();
		
		
		$la = new GO_Ldapauth_Authenticator();
	
		$ldapConn = GO_Base_Ldap_Connection::getDefault();
		
		$result = $ldapConn->search(GO::config()->ldap_basedn, 'uid=*');
		
		//keep an array of users that exist in ldap. This array will be used later for deletes.
		//admin user is not in ldap but should not be removed.
		$usersInLDAP = array(1);
				
		$i=0;
		while($record = $result->fetch()){
			$i++;
			
			$user = $la->syncUserWithLdapRecord($record);
			
			echo "Synced ".$user->username."\n";
			
			$this->fireEvent("ldapsyncuser", array($user, $record));
			
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
				throw new Exception("Delete Aborted because script was about to delete more then $maxDeletePercentage% of the users (".$percentageToDelete."%, ".($totalInGO-$totalInLDAP)." users)");

			while($user = $stmt->fetch()){
				if(!in_array($user->id, $usersInLDAP)){
					echo "Deleting ".$user->username."\n";
					$user->delete();
				}
			}			
		}
		
		//var_dump($attr);
		
	}
}