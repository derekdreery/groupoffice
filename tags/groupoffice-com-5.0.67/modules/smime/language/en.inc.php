<?php
//require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('smime'));

$lang['smime']['name']='SMIME support';
$lang['smime']['description']='Extend the mail module with SMIME signing and encryption.';

$lang['smime']['noPublicCertForEncrypt']="Could not encrypt message because you don't have the public certificate for %s. Open a signed message of the recipient and verify the signature to import the public key.";
$lang['smime']['noPrivateKeyForDecrypt']="This message is encrypted and you don't have the private key to decrypt this message.";

$lang['smime']['badGoLogin']="The {product_name} password was incorrect.";
$lang['smime']['smime_pass_matches_go']="Your SMIME key password matches your {product_name} password. This is prohibited for security reasons!";
$lang['smime']['smime_pass_empty']="Your SMIME key has no password. This is prohibited for security reasons!";

$lang['smime']['invalidCert']="The certificate is invalid!";
$lang['smime']['validCert']="Valid certificate";
$lang['smime']['certEmailMismatch']="Valid certificate but the e-mail of the certificate does not match the sender address of the e-mail.";

$lang['smime']['decryptionFailed']='SMIME Decryption of this message failed.';
