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
		$events->add_listener('init_composer', __FILE__, 'smime','add_smime_info_to_aliases');
		$events->add_listener('all_aliases', __FILE__, 'smime','add_smime_info_to_aliases');
	}
	
	
	public static function get_root_certificates(){
		
		global $GO_CONFIG;
		
		$certs=array();
		
//		if(isset($GO_CONFIG->smime_root_cert_location)){
//			
//			$GO_CONFIG->smime_root_cert_location=rtrim($GO_CONFIG->smime_root_cert_location, '/');		
//			
//			if(is_dir($GO_CONFIG->smime_root_cert_location)){				
//							
//				$dir = opendir($GO_CONFIG->smime_root_cert_location);
//				if ($dir) {
//					while ($item = readdir($dir)) {
//						if ($item != '.' && $item != '..') {
//							$certs[] = $GO_CONFIG->smime_root_cert_location.'/'.$item;
//						}
//					}
//					closedir($dir);
//				}
//			}elseif(file_exists($GO_CONFIG->smime_root_cert_location)){
//				$certs[]=$GO_CONFIG->smime_root_cert_location;
//			}
//		}
//		
		if(file_exists($GO_CONFIG->smime_root_cert_location)){
				$certs[]=$GO_CONFIG->smime_root_cert_location;
			}
		
		//var_dump($certs);
		
		return $certs;
	}
	
	public function add_smime_info_to_aliases(&$response){
		
		$account_certs=array();
		
		$smime = new smime();
		
		if(isset($response['aliases'])){
			$arr = &$response['aliases']['results'];
		}else
		{
			$arr = &$response['results'];
		}
	
		
		foreach($arr as &$alias){
	
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
		global $GO_CONFIG, $GO_LANGUAGE, $lang;
		
		
		$cert = '';
		
		if (isset($_FILES['cert']['tmp_name'][0]) && is_uploaded_file($_FILES['cert']['tmp_name'][0])) {
			
			$GO_LANGUAGE->require_language_file('smime');
			
			
			require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
			$GO_AUTH = new GO_AUTH();
			if(!$GO_AUTH->login($_SESSION['GO_SESSION']['username'], $_POST['smime_password'])){
				throw new Exception($lang['smime']['badGoLogin']);
			}
			
			$cert = file_get_contents($_FILES['cert']['tmp_name'][0]);
			
			
			openssl_pkcs12_read ($cert, $certs, $_POST['smime_password']);
			if(!empty($certs)){
				throw new Exception($lang['smime']['smime_pass_matches_go']);
			}
			
			openssl_pkcs12_read ($cert, $certs, "");
			if(!empty($certs)){
				throw new Exception($lang['smime']['smime_pass_empty']);
			}
			
		}
				
		$smime = new smime();
			
		$smime->set_pkcs12_certificate($account['id'], $cert, isset($_POST['always_sign']), isset($_POST['delete_cert']));			
		
		
		$cert = $smime->get_pkcs12_certificate($account['id']);
		
		if(!empty($cert['cert'])){
			$response['cert']=true;			
		}
		
	}
	
	public static function decrypt_message(&$message, cached_imap $imap){
		
		global $GO_MODULES, $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $lang;
			
		go_debug('decrypt_message');

		if(!$message['from_cache'] && ($message['content-type']=='application/pkcs7-mime' || $message['content-type']=='application/x-pkcs7-mime')){
			
			$encrypted = !isset($message['content-type-attributes']['smime-type']) || $message['content-type-attributes']['smime-type']!='signed-data';
			if($encrypted){
				
				go_debug("Message is encrypted");
				
				$smime = new smime();
				$cert = $smime->get_pkcs12_certificate($message['account_id']);

				if(!$cert || empty($cert['cert']))
				{
					$GO_LANGUAGE->require_language_file('smime');
					go_debug('SMIME: No private key at all found for this account');
					$message['html_body']=$lang['smime']['noPrivateKeyForDecrypt'];
					return false;
				}


				if(isset($_POST['password']))
					$_SESSION['GO_SESSION']['smime']['passwords'][$message['account_id']]=$_POST['password'];

				if(!isset($_SESSION['GO_SESSION']['smime']['passwords'][$message['account_id']])){
					$message['askPassword']=true;
					return false;
				}				
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
			
			$outfilerel = $reldir.'unencrypted.txt';	
			
			if($encrypted){
				go_debug('Message is encrypted');
				
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


				$return = openssl_pkcs7_decrypt($infilename, $outfilename, $certs['cert'], array($certs['pkey'], $password));

				unlink($infilename);

				if(!$return || !file_exists($outfilename) || !filesize($outfilename)){
					//throw new Exception("Could not decrypt message");
					$GO_LANGUAGE->require_language_file('smime');
					$message['html_body']=$lang['smime']['decryptionFailed'].'<br />';
					
					while($str = openssl_error_string()){
						$message['html_body'].='<br />'.$str;
					}
					return false;
				}

			}else
			{
				$imap->save_to_file($message['uid'], $outfilename, $att['imap_id'], $att['encoding']);
				//$outfilerel=$reldir.'encrypted.txt';
				
				//file_put_contents($outfilename, String::clean_utf8(file_get_contents($outfilename)));
				
				//Outlook mime files contain strange binary data. We want to get rid of that.
				$content = file_get_contents($outfilename);
				//$content = String::clean_utf8($content);
				//$content= preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

				//$content= preg_replace('/[\x82]/', '', $content);
				$pos = strpos($content, 'Content-Type');
				file_put_contents($outfilename, substr($content, $pos));
			}
			
			require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
			$ml = new mailings();		
			
			$decrypted_message = $ml->get_message_for_client(0, $outfilerel,'');
			
			//can't unlink the file here because we need it for showing inline images etc.
			//unlink($outfilename);
			
			//go_debug($decrypted_message);
			
			$message['html_body']=$decrypted_message['body'];
			$message['attachments']=$decrypted_message['attachments'];
			$message['path']=$decrypted_message['path'];
			$message['smime_signed']=$decrypted_message['smime_signed'] || isset($message['content-type-attributes']['smime-type']) && $message['content-type-attributes']['smime-type']=='signed-data';	
			$message['smime_encrypted']=$encrypted;
		}
	}
	
	public function sendmail(GoSwift &$swift){
		global $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $lang;
		
		$smime = new smime();
		
		if(!empty($_POST['sign_smime'])){			
		
			//$password = trim(file_get_contents("/home/mschering/password.txt"));
			$password = $_SESSION['GO_SESSION']['smime']['passwords'][$swift->account['id']];

			$cert = $smime->get_pkcs12_certificate($swift->account['id']);

			$swift->message->setSignParams($cert['cert'], $password, isset($GO_CONFIG->smime_sign_extra_certs) ? $GO_CONFIG->smime_sign_extra_certs : "");
		}
		
		if(!empty($_POST['encrypt_smime'])){		
			
			if(!isset($cert)){
				$cert = $smime->get_pkcs12_certificate($swift->account['id']);
			}
			$password = $_SESSION['GO_SESSION']['smime']['passwords'][$swift->account['id']];
			openssl_pkcs12_read ($cert['cert'], $certs, $password);

			if(!isset($certs['cert']))
				throw new Exception("Failed to get your public key for encryption");				

			
			$to = $swift->message->getTo();
			
			$cc = $swift->message->getCc();
			
			$bcc = $swift->message->getBcc();
			
			if(is_array($cc))
				$to = array_merge($to, $cc);
			
			if(is_array($bcc))
				$to = array_merge($to, $bcc);
			
			//lookup all recipients
			$failed=array();
			$public_certs=array($certs['cert']);
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

			//if($cert)
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
	
	public function update_public_certificate($id, $cert){		
		return $this->update_row('smi_certs', 'id', array(
				'id'=>$id,
				'cert'=>$cert
				));
	}
	
	public function get_public_certificates($user_id, $query='',$start=0, $limit=0){
		
		$sql = "SELECT ";
		
		if($limit>0)
			$sql .= " SQL_CALC_FOUND_ROWS";
		
		$sql .= "* FROM smi_certs WHERE user_id=".intval($user_id);
		
		if($query!=''){
			$sql .= " AND email LIKE '".$this->escape($query)."'";
		}
		
		$sql .= ' ORDER BY email ASC';
		
		if($limit>0)
		{
			$sql .= ' LIMIT '.intval($start).','.intval($limit);
		}
		
		$this->query($sql);
		
		return $limit > 0 ? $this->found_rows() : $this->num_rows();		
	}
	
	public function get_public_certificate($user_id, $email){
		$this->get_public_certificates($user_id, $email);
		return $this->next_record();
	}
	
	public function get_public_certificate_by_id($cert_id){
		$sql = "SELECT * FROM smi_certs WHERE id=".intval($cert_id);
		$this->query($sql);
		return $this->next_record();
	}
	
	public function delete_public_certificate($id){
		$sql = "DELETE FROM smi_certs WHERE id=".intval($id);
		return $this->query($sql);
	}
	
	
	
	public function set_pkcs12_certificate($account_id, $cert, $always_sign, $delete_cert){
		
		//the code below doesn't work due to bug: http://bugs.php.net/bug.php?id=53483
//		$up['account_id']=$account_id;
//		
//		$types='ii';
//		if(isset($cert)){
//			$up['cert']=$cert;
//			$types='ibi';
//		}
//		
//		$up['always_sign']=$always_sign;	
//		
//		return $this->replace_row('smi_pkcs12',$up,$types,false);		
		
		$sql = "INSERT IGNORE INTO smi_pkcs12 (account_id) VALUES (".intval($account_id).")";
		$this->query($sql);
		
		$sql = "UPDATE smi_pkcs12 SET ";
		if(!empty($cert) || $delete_cert){
			$sql .= 'cert="'.addslashes($cert).'",';
		}
		$sql .= 'always_sign='.intval($always_sign).' WHERE account_id='.intval($account_id);
		
		return $this->query($sql);
	}
	
	public function get_pkcs12_certificate($account_id){
		
		$sql = "SELECT * FROM smi_pkcs12 WHERE account_id=".intval($account_id);
		
		$this->query($sql);
		
		return $this->next_record();		
	}
	
	
}