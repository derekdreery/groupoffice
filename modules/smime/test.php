<?php
//$ret = openssl_pkcs7_verify ("signed.txt",PKCS7_NOVERIFY, "output.txt");
//
//var_dump($ret);
//
//$cert = file_get_contents("output.txt");
//
//print_r(openssl_x509_parse($cert));


$pkcs12 = file_get_contents( "smime_cert_mschering.p12" );

openssl_pkcs12_read ( $pkcs12, &$certs, "mks14785");

var_dump($certs);
