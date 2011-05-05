<?php
class smime extends db{
	
	public function __on_load_listeners($events) {
		$events->add_listener('sendmail', __FILE__, 'smime','sendmail');
		$events->add_listener('get_message_with_body', __FILE__, 'smime','get_message_with_body');
	}
	
	public function get_message_with_body(&$message, cached_imap $imap){
		
		global $GO_MODULES, $GO_CONFIG;

		
		if(!$message['from_cache'] && $message['content-type']=='application/pkcs7-mime'){
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
			
			$dir = $GO_CONFIG->file_storage_path.'smimetmp/';
			File::mkdir($dir);
			
			$infilename=$dir.'encrypted.txt';
			$outfilename=$dir.'unencrypted.txt';
			
			$imap->save_to_file($message['uid'], $infilename);//,, $att['imap_id'], $att['encoding']);
			
			$pkcs12 = file_get_contents("/home/mschering/smime_cert_mschering.p12");
			$password = file_get_contents("/home/mschering/password.txt");
		
			openssl_pkcs12_read ($pkcs12, $certs, $password);
			
			openssl_pkcs7_decrypt($infilename, $outfilename, $certs['cert'], array($certs['pkey'], $password));
			
			require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
			$ml = new mailings();
			
			$message=array_merge($message, $ml->get_message_for_client(0, 'smimetmp/unencrypted.txt',''));
			$message['html_body']=$message['body'];
			unset($message['body']);			
		}
	}
	
	public function sendmail(&$swift){
		global $GO_SECURITY;
		
		$password = file_get_contents("/home/mschering/password.txt");
		
		$swift->message->setSignParams("/home/mschering/smime_cert_mschering.p12", $password);
		
		$smime = new smime();
		$cert = $smime->get_certificate($GO_SECURITY->user_id, 'mschering@intermesh.nl');
		
		if($cert)
			$swift->message->setEncryptParams(array($cert['cert']));

	}
	
	public function add_certificate($user_id, $email, $cert){
		
		$id = $this->nextid('id');
		
		$this->insert_row('smi_certs', array(
				'id'=>$id,
				'email'=>$email,
				'user_id'=>$user_id,
				'cert'=>$cert
				));
		
		return $id;
	}
	
	public function get_certificates($user_id, $email=''){
		
		$sql = "SELECT * FROM smi_certs WHERE user_id=".intval($user_id);
		
		if($email!=''){
			$sql .= " AND email='".$this->escape($email)."'";
		}
		$this->query($sql);
		
		return $this->num_rows();		
	}
	
	public function get_certificate($user_id, $email){
		$this->get_certificates($user_id, $email);
		return $this->next_record();
	}
	
	
}