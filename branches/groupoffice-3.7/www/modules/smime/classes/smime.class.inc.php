<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.php 7363 2011-05-05 10:38:39Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class smime extends db{
	
	public function __on_load_listeners($events) {
		$events->add_listener('sendmail', __FILE__, 'smime','sendmail');
		$events->add_listener('get_message_with_body', __FILE__, 'smime','decrypt_message');
		$events->add_listener('save_email_account', __FILE__, 'smime','save_certificate');
		$events->add_listener('load_email_account', __FILE__, 'smime','load_certificate');
		$events->add_listener('init_composer', __FILE__, 'smime','init_composer');
	}
	
	public function init_composer(&$response){
		
		$account_certs=array();
		
		$smime = new smime();
		
		foreach($response['aliases']['results'] as &$alias){
	
			if(!isset($account_certs[$alias['account_id']])){
				$account_certs[$alias['account_id']]=$smime->get_pkcs12_certificate($alias['account_id']);
			}
			if($account_certs[$alias['account_id']]){
				$alias['has_smime_cert']=true;
				$alias['always_sign']=$account_certs[$alias['account_id']]['always_sign'];
			}
		}
	}
	
	public function load_certificate(&$response){
		$smime = new smime();
		
		$cert = $smime->get_pkcs12_certificate($response['data']['id']);
		
		if(!empty($cert['cert'])){
			$response['data']['cert']=true;
			$response['data']['always_sign']=$cert['always_sign'];			
		}		
	}
	
	public function save_certificate(&$account, $email, &$response){
		
		if (isset($_FILES['cert']['tmp_name'][0]) && is_uploaded_file($_FILES['cert']['tmp_name'][0])) {
			$cert = file_get_contents($_FILES['cert']['tmp_name'][0]);
		}
		if(isset($_POST['delete_cert']))
			$cert = '';
		
		$smime = new smime();
	
		if(isset($cert)){			
			$smime->set_pkcs12_certificate($account['id'], $cert, isset($_POST['always_sign']));			
		}
		
		$cert = $smime->get_pkcs12_certificate($account['id']);
		
		if(!empty($cert['cert'])){
			$response['cert']=true;			
		}
		
	}
	
	public function decrypt_message(&$message, cached_imap $imap){
		
		global $GO_MODULES, $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $lang;
		
		if(!$message['from_cache'] && $message['content-type']=='application/pkcs7-mime'){
			
			
			$smime = new smime();
			$cert = $smime->get_pkcs12_certificate($message['account_id']);
			
			if(!$cert)
				throw new Exception("No key was found to decrypt the message!");
			
			
			if(isset($_POST['password']))
				$_SESSION['GO_SESSION']['smime']['passwords'][$message['account_id']]=$_POST['password'];
			
			if(!isset($_SESSION['GO_SESSION']['smime']['passwords'][$message['account_id']])){
				$message['askPassword']=true;
				return false;
			}				
			
			$att = $message['attachments'][0];
			
//			array (
//      'type' => 'application',
//      'subtype' => 'pkcs7-mime',
//      'smime-type' => 'enveloped-data',
//      'name' => 'smime.p7m',
//      'id' => false,
//      'encoding' => 'base64',
//      'size' => '2302',
//      'md5' => false,
//      'disposition' => false,
//      'language' => false,
//      'location' => false,
//      'charset' => false,
//      'lines' => false,
//      'imap_id' => 1,
//      'extension' => 'p7m',
//      'human_size' => '2,2 KB',
//      'tmp_file' => false,
//    )
			
			$reldir='smimetmp/'.$GO_SECURITY->user_id.'/';
			$dir = $GO_CONFIG->file_storage_path.$reldir;
			File::mkdir($dir);
			
			$infilename=$dir.'encrypted.txt';
			$outfilename=$dir.'unencrypted.txt';
			
			$imap->save_to_file($message['uid'], $infilename);//,, $att['imap_id'], $att['encoding']);
			
			if(!file_exists($infilename)){
				throw new Exception("Could not save IMAP message to file for decryption");
			}
			
			//$pkcs12 = file_get_contents("/home/mschering/smime_cert_mschering.p12");
			//$password = trim(file_get_contents("/home/mschering/password.txt"));
			$password = $_SESSION['GO_SESSION']['smime']['passwords'][$message['account_id']];
			
			
			openssl_pkcs12_read ($cert['cert'], $certs, $password);
			
			if(empty($certs)){
				//password invalid
				$message['askPassword']=true;
				return false;
			}
			
			openssl_pkcs7_decrypt($infilename, $outfilename, $certs['cert'], array($certs['pkey'], $password));
			
			unlink($infilename);
			
			if(!file_exists($outfilename) || !filesize($outfilename)){
				//throw new Exception("Could not decrypt message");
				$GO_LANGUAGE->require_language_file('smime');
				$message['html_body']=$lang['smime']['noPrivateKeyForDecrypt'];
				return false;
			}
			
			require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
			$ml = new mailings();		
			
			$decrypted_message = $ml->get_message_for_client(0, $reldir.'unencrypted.txt','');
			
			unlink($reldir.'unencrypted.txt');
			
			$message['html_body']=$decrypted_message['body'];
			$message['attachments']=$decrypted_message['attachments'];
			$message['path']=$decrypted_message['path'];
			$message['smime_signed']=$decrypted_message['smime_signed'];	
			$message['smime_encrypted']=true;
		}
	}
	
	public function sendmail(GoSwift &$swift){
		global $GO_SECURITY, $GO_LANGUAGE, $lang;
		
		$smime = new smime();
		
		if(!empty($_POST['sign_smime'])){			
		
			//$password = trim(file_get_contents("/home/mschering/password.txt"));
			$password = $_SESSION['GO_SESSION']['smime']['passwords'][$swift->account['id']];

			$cert = $smime->get_pkcs12_certificate($swift->account['id']);

			$swift->message->setSignParams($cert['cert'], $password);
		}
		
		if(!empty($_POST['encrypt_smime'])){		
			
			$to = $swift->message->getTo();
			
			$cc = $swift->message->getCc();
			
			$bcc = $swift->message->getBcc();
			
			if(is_array($cc))
				$to = array_merge($to, $cc);
			
			if(is_array($bcc))
				$to = array_merge($to, $bcc);
			
			//lookup all recipients
			$failed=array();
			$public_certs=array();
			foreach($to as $email=>$name){
				$cert = $smime->get_public_certificate($GO_SECURITY->user_id, $email);				
				if(!$cert){
					$failed[]=$email;
				}				
				$public_certs[]=$cert['cert'];
			}
			
			if(count($failed)){
				$GO_LANGUAGE->require_language_file('smime');
				throw new Exception(sprintf($lang['smime']['noPublicCertForEncrypt'], implode(', ',$failed)));
			}

			if($cert)
				$swift->message->setEncryptParams($public_certs);
		}

	}
	
	public function add_public_certificate($user_id, $email, $cert){
		
		$id = $this->nextid('id');
		
		$this->insert_row('smi_certs', array(
				'id'=>$id,
				'email'=>$email,
				'user_id'=>$user_id,
				'cert'=>$cert
				));
		
		return $id;
	}
	
	public function get_public_certificates($user_id, $email=''){
		
		$sql = "SELECT * FROM smi_certs WHERE user_id=".intval($user_id);
		
		if($email!=''){
			$sql .= " AND email='".$this->escape($email)."'";
		}
		$this->query($sql);
		
		return $this->num_rows();		
	}
	
	public function get_public_certificate($user_id, $email){
		$this->get_public_certificates($user_id, $email);
		return $this->next_record();
	}
	
	
	
	public function set_pkcs12_certificate($account_id, $cert, $always_sign){
		$up['account_id']=$account_id;
		$up['cert']=$cert;
		$up['always_sign']=$always_sign;
		
		return $this->replace_row('smi_pkcs12',$up,'ibi',false);		
	}
	
	public function get_pkcs12_certificate($account_id){
		
		$sql = "SELECT * FROM smi_pkcs12 WHERE account_id=".intval($account_id);
		
		$this->query($sql);
		
		return $this->next_record();		
	}
	
	
}