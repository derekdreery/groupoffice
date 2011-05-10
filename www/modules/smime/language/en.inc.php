<?php
//require($GO_LANGUAGE->get_fallback_language_file('smime'));

$lang['smime']['name']='SMIME support';
$lang['smime']['description']='Enhance the mail module with SMIME signing and encryption.';

$lang['smime']['noPublicCertForEncrypt']="Could not encrypt message because you don't have the public certificate for %s. Open a signed message of the recipient and verify the signature to get the public key.";
$lang['smime']['noPrivateKeyForDecrypt']="This message is encrypted and you don't have the private key to decrypt this message.";