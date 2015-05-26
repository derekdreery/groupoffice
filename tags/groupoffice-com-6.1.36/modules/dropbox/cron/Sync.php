<?php


namespace GO\Dropbox\Cron;

use GO;
use GO\Base\Cron\AbstractCron;
use GO\Base\Cron\CronJob;
use GO\Base\Db\FindParams;
use GO\Base\Mail\Mailer;
use GO\Base\Mail\Message;
use GO\Base\Model\User;
use GO\Dropbox\Model;
use GO\Dropbox\Model\DropboxException;

class Sync extends AbstractCron {
	
	/**
	 * Return true or false to enable the selection fo users and groups for 
	 * this cronjob.
	 * 
	 * CAUTION: This will give the run() function a different behaviour. 
	 *					Please see the documentation of the run() function 
	 *					to see what is different.
	 */
	public function enableUserAndGroupSupport(){
		return false;
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return string
	 */
	public function getLabel(){
		return "Dropbox sync";
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return string
	 */
	public function getDescription(){
		return "";
	}
	
	/**
	 * The code that needs to be called when the cron is running
	 * 
	 * If $this->enableUserAndGroupSupport() returns TRUE then the run function 
	 * will be called for each $user. (The $user parameter will be given)
	 * 
	 * If $this->enableUserAndGroupSupport() returns FALSE then the 
	 * $user parameter is null and the run function will be called only once.
	 * 
	 * @param CronJob $cronJob
	 * @param User $user [OPTIONAL]
	 */
	public function run(CronJob $cronJob,User $user = null){
		
		$stmt = \GO\Dropbox\Model\User::model()->find(FindParams::newInstance()->select());
		
		$sync = new Model\Sync();
		
		foreach($stmt as $dbxUser){
			try{
				if(!empty($dbxUser->access_token)){
					$sync->start($dbxUser->user);
				}else{
					$sync::log("Skipped user ".$dbxUser->user->username." because there's no access token");
				}
			}catch(DropboxException $e){
				
				$sync::log($e->getMessage());				
				$this->_sendMessage($dbxUser, $e, GO::t('syncFailedBody','dropbox'));				
				
				//Remove user that does not work anymore so he can reconnect				
				//$dbxUser->delete();
			}catch(\Exception $e){
				$sync::log($e->getMessage());				
//				$this->_sendMessage($dbxUser, $e,"");
			}
		}
		
	}
	
	
	private function _sendMessage($dbxUser, $e, $body){
		
		Model\Sync::log("Sending failure e-mail to ".$dbxUser->user->email);
		
		$message = Message::newInstance();
		$message->setSubject(GO::t('syncFailed','dropbox'));				

		$body.="\n\nError: ".$e->getMessage();

		$message->setBody($body,'text/plain');
		$message->setFrom(GO::config()->webmaster_email,GO::config()->title);
		$message->addTo($dbxUser->user->email, $dbxUser->user->name);

		Mailer::newGoInstance()->send($message);
		
	}
}