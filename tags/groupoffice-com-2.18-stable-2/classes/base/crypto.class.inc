<?php
/**
*  Copyright Intermesh 2003
*  Author: Meir Michanie
*  Version: 1.0 Release date: Januari 2004
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
*/
 
/**
* Two way encryption class
*
* @package  Framework
* @author   Meir Michanie
* @since    Group-Office 2.02
*/


class GO_CRYPTO
{
  var $time="";
  //var $cryptokey;
  var $varcryptokey;

  function GO_CRYPTO(){
    $this->time="1073437878";
    //$this->cryptokey=isset($_SESSION['GO_SESSION']['password']) ? $_SESSION['GO_SESSION']['password'] : 'secret';
    $this->varcryptokey='secret';
  }

  function bytexor($a,$b)
  {
    $c="";
    for($i=0;$i<strlen($a);$i++) {
      $a{$i}= isset($a{$i}) ?  $a{$i} : null;
      $b{$i}= isset($b{$i}) ?  $b{$i} : null;
      $c.=$a{$i}^$b{$i};
    }
    return($c);
  }

  function binmd5($val)
  {
    return(pack("H*",md5($val)));
  }

  function decrypt_md5($msg,$heslo,$time)
  {
    $key=$heslo.$time;$sifra="";
    $key1=$this->binmd5($key);
    while($msg) {
      $m=substr($msg,0,16);
      $msg=substr($msg,16);
      $sifra.=$m=$this->bytexor($m,$key1);
      $key1=$this->binmd5($key.$key1.$m);
    }
    return($sifra);
  }

  function crypt_md5($msg,$heslo,$time)
  {
    $key=$heslo.$time;$sifra="";
    $key1=$this->binmd5($key);
    while($msg) {
      $m=substr($msg,0,16);
      $msg=substr($msg,16);
      $sifra.=$this->bytexor($m,$key1);
      $key1=$this->binmd5($key.$key1.$m);
    }
    return($sifra);
  }


  function encrypt($text,$cryptokey=null){

    if ($cryptokey==null) {$cryptokey=$_SESSION['GO_SESSION']['password'];}
#echo "encrypting with passphrase: $cryptokey!!!<br>";
    $key="12345678";
    $base64="base64_encode";
    $url="urlencode";
    $fce="\$this->crypt_md5";
    return $url($base64($this->crypt_md5($text,$cryptokey,$this->time)));
  }

  function decrypt($text,$cryptokey=null){

    if ($cryptokey==null) {$cryptokey=$_SESSION['GO_SESSION']['password'];}
#echo "decrypting with passphrase: $cryptokey!!!<br>";
    $key="12345678";
    $fce="\$this->decrypt_md5";
    $base64="base64_decode";
    $url="urldecode";
    return $this->decrypt_md5($base64($url($text)),$cryptokey,$this->time);
  }
}
