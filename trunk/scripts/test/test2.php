#!/usr/bin/php
<?php
/*require('../../www/classes/cryptastic.class.inc.php');
$c = new cryptastic();
$enc = $c->encrypt('Secret message', 'mysecretkey', true);
echo $c->decrypt($enc, 'mysecretkey', true);
echo "\n";*/

function md5_base64($data)
{
    return base64_encode(pack('H*',md5($data)));
}
function md5_syncml_new($user,$pass,$nonce)
{
    return md5_base64(md5_base64($user.":".$pass).":".$nonce);
}


$u='administrator';
$p='admin';
//echo base64_encode(pack('H*',md5($u.':'.$p.':')));

echo md5_syncml_new($u,$p,'');




