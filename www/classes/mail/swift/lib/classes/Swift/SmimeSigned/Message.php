<?php

/*
tempout Message for SwiftMailer
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
* tempout Message Special Message where we can apply signatures
* @package Swift
* @subpackage Signatures
* @author Xavier De Cock <xdecock@gmail.com>
*/
class Swift_SmimeSigned_Message extends Swift_Message
{ 
	protected $tempout;
	protected $tempin;
	protected $pkcs12_path;
	protected $passphrase;
	
	protected $recipcerts;
	
	 /**
   * Create a new Message.
   * @param string $subject
   * @param string $body
   * @param string $contentType
   * @param string $charset
   * @return Swift_Mime_Message
   */
  public static function newInstance($subject = null, $body = null,
    $contentType = null, $charset = null)
  {
    return new self($subject, $body, $contentType, $charset);
  }
	
	/**
	 * Call this function to sign a message with a pkcs12 certificate.
	 * 
	 * @global type $GO_CONFIG
	 * @param type $pkcs12_path
	 * @param type $passphrase 
	 */
	
	public function setSignParams($pkcs12_path, $passphrase){
	
		
		$this->pkcs12_path=$pkcs12_path;
		$this->passphrase=$passphrase;
	}
	
	public function setEncryptParams($recipcerts){
		
		$this->recipcerts=$recipcerts;	
	}
	
	private function save_headers(){
		go_debug('save_headers');	
		
			global $GO_CONFIG;			
		
		
		$this->tempin = $GO_CONFIG->tmpdir."smime_tempin.txt";
		$this->tempout=$GO_CONFIG->tmpdir."smime_tempout.txt";
		if(file_exists($this->tempin))
			unlink($this->tempin);
		
		if(file_exists($this->tempout))
			unlink($this->tempout);
		
		/*
		 * This class will stream the MIME structure to the tempin text file in 
		 * a memory efficient way.
		 */
		$fbs = new Swift_ByteStream_FileByteStream($this->tempin, true);		
		parent::toByteStream($fbs);
		
		if(!filesize($this->tempin))
			throw new Exception('Could not write temporary message for signing');
		
		/*
		 * Store the headers of the current message because the PHP function
		 * openssl_pkcs7_sign will rebuilt the MIME structure and will put the main
		 * headers in a nested mimepart. We don't want that so we remove them now 
		 * and add them to the new structure later.
		 */
		$headers = $this->getHeaders();
		$headers->removeAll('MIME-Version');
		$headers->removeAll('Content-Type');
		$this->saved_headers = $headers->toString();

		$h= $headers->getAll();
		foreach($h as $header){
			$headers->removeAll($header->getFieldName());
		}
	}
	
	private function do_sign(){		
		
		go_debug('do_sign');	
		
		$pkcs12 = file_get_contents($this->pkcs12_path);
		
		openssl_pkcs12_read ($pkcs12, $certs, $this->passphrase);
		openssl_pkcs7_sign($this->tempin, $this->tempout,$certs['cert'], array($certs['pkey'], $this->passphrase), NULL);
	}
	
	private function do_encrypt(){		
		go_debug('do_encrypt');		
		
		openssl_pkcs7_encrypt($this->tempin, $this->tempout,$this->recipcerts[0], array());	
	}
  
	
	public function toString(){
		
		if(empty($this->pkcs12_path) && empty($this->recipcerts)){
			//no sign or encrypt parameters. Do parent method.
			return parent::toString();
		}
		
		if(!empty($this->pkcs12_path)){
			$this->do_sign();
		}
		
		if(!empty($this->recipcerts)){
			$this->do_encrypt();
		}
		
		return $this->saved_headers.file_get_contents($this->tempout);
	}
	
  /**
* Write this message to a {@link Swift_InputByteStream}.
* @param Swift_InputByteStream $is
*/
  public function toByteStream(Swift_InputByteStream $is)
  {
		
		go_debug('toByteStream');
		
		if(empty($this->pkcs12_path) && empty($this->recipcerts)){
			//no sign or encrypt parameters. Do parent method.
			return parent::toByteStream($is);
		}
		
		$this->save_headers();
		
		if(!empty($this->pkcs12_path)){
			$this->do_sign();
		}
		
		if(!empty($this->recipcerts)){
			$this->do_encrypt();
		}
		
		$is->write($this->saved_headers);
		
		$fp = fopen($this->tempout, 'r');
		if(!$fp)
			throw new Exception('Could not read tempout file');
		while($line = fgets($fp)){			
			$is->write($line);
		}
		fclose($fp);		
		
		unlink($this->tempout);
		unlink($this->tempin);
		
    return;
  }
  
 
}

