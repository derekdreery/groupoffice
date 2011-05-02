<?php
$ret = openssl_pkcs7_verify ("signed.txt",PKCS7_NOVERIFY, "output.txt");

var_dump($ret);

$cert = file_get_contents("output.txt");

print_r(openssl_x509_parse($cert));