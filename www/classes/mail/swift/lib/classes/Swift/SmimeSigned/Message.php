<?php

/*
Signed Message for SwiftMailer
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
* Signed Message Special Message where we can apply signatures
* @package Swift
* @subpackage Signatures
* @author Xavier De Cock <xdecock@gmail.com>
*/
class Swift_SmimeSigned_Message extends Swift_Message
{ 
	protected $signed;
	protected $unsigned;
	protected $pkcs12_path;
	protected $passphrase;
	
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
		global $GO_CONFIG;
		
		$this->unsigned = $GO_CONFIG->tmpdir."smime_unsigned.txt";
		$this->signed=$GO_CONFIG->tmpdir."smime_signed.txt";
		
		$this->pkcs12_path=$pkcs12_path;
		$this->passphrase=$passphrase;
	}
	
	private function do_sign(){		
		
		
		if(file_exists($this->unsigned))
			unlink($this->unsigned);
		
		if(file_exists($this->signed))
			unlink($this->signed);
		
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
		
		/*
		 * This class will stream the MIME structure to the unsigned text file in 
		 * a memory efficient way.
		 */
		
		$fbs = new Swift_ByteStream_FileByteStream($this->unsigned, true);		
		parent::toByteStream($fbs);
		
		if(!filesize($this->unsigned))
			throw new Exception('Could not write temporary message for signing');
		
		$pkcs12 = file_get_contents($this->pkcs12_path);
		
		openssl_pkcs12_read ($pkcs12, $certs, $this->passphrase);
		openssl_pkcs7_sign($this->unsigned, $this->signed,$certs['cert'], array($certs['pkey'], $this->passphrase), NULL);
		unlink($this->unsigned);
	}
  
	
	public function toString(){
		
		if(empty($this->pkcs12_path)){
			//no sign parameters. Do parent method
			return parent::toString();
		}
		
		$this->do_sign();
		
		return file_get_contents($this->signed);
	}
	
  /**
* Write this message to a {@link Swift_InputByteStream}.
* @param Swift_InputByteStream $is
*/
  public function toByteStream(Swift_InputByteStream $is)
  {
		if(empty($this->pkcs12_path)){
			//no sign parameters. Do parent method
			return parent::toByteStream($is);
		}
		
		$this->do_sign();
		
		$is->write($this->saved_headers);
		
		$fp = fopen($this->signed, 'r');
		if(!$fp)
			throw new Exception('Could not read signed file');
		while($line = fgets($fp)){			
			$is->write($line);
		}
		fclose($fp);		
		unlink($this->signed);
		
    return;
  }
  
 
}

