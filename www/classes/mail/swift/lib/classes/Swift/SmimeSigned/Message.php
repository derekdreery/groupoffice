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
  
	
	public function toString(){
		$s = parent::toString();
		
		$unsigned = "/tmp/unsigned.txt";
		$signed="/tmp/signed.txt";
		
		file_put_contents($unsigned, $s);
		
		$pkcs12 = file_get_contents( "/home/mschering/smime_cert_mschering.p12" );
		
		$pass="test";
		
		openssl_pkcs12_read ( $pkcs12, $certs, $pass);

		openssl_pkcs7_sign($unsigned, $signed,$certs['cert'], array($certs['pkey'], $pass), NULL);
		
		//throw new Exception(file_get_contents($signed));
		
		return file_get_contents($signed);
	}
	
  /**
* Write this message to a {@link Swift_InputByteStream}.
* @param Swift_InputByteStream $is
*/
  public function toByteStream(Swift_InputByteStream $is)
  {		
		$is->write($this->toString());
    return;
  }
  
 
}

