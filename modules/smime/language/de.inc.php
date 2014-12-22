<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('smime'));

$lang['smime']['name']='SMIME Unterstützung';
$lang['smime']['description']='Erweitert das E-Mailmodul mith SMIME-Signatur und -Verschlüsselung.';

$lang['smime']['noPublicCertForEncrypt']="E-Mail kann nicht verschlüsselt werden, da Sie nicht über das öffentliche Zertifikat für %s verfügen. Öffnen Sie eine signierte E-Mail des Empfängers und überprüfen und installieren Sie das Zertifikat.";
$lang['smime']['noPrivateKeyForDecrypt']="Diese E-Mail ist verschlüsselt jedoch verfügen Sie nicht über den privaten Schlüssel, um die E-Mail zu entschlüsseln.";

$lang['smime']['badGoLogin']="Das {product_name}-Kennwort war nicht korrekt.";
$lang['smime']['smime_pass_matches_go']="Ihr SMIME-Schlüsselkennwort entspricht Ihrem {product_name}-Kennwort. Dies widerspricht den Sicherheitsrichtlinien!";
$lang['smime']['smime_pass_empty']="Ihr SMIME-Schlüssel ist nicht durch ein Kennwort geschützt. Dies widerspricht den Sicherheitsrichtlinien!";

$lang['smime']['invalidCert']="Das Zertifikat ist ungültig!";
$lang['smime']['validCert']="Gültiges Zertifikat";

$lang['smime']['certEmailMismatch']="Gültiges Zertifikat, aber die Absenderadresse stimmt nicht mit der E-Mailadresse des Zertifikates überein.";

$lang['smime']['decryptionFailed']='SMIME-Entschlüsselung dieser Nachricht fehlgeschlagen.';
