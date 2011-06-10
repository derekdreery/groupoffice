<?php
require($GO_LANGUAGE->get_fallback_language_file('smime'));

$lang['smime']['name']='SMIME ondersteuning';
$lang['smime']['description']='Uitbreiding van de e-mail module met SMIME ondertekeking en versleuteling.';

$lang['smime']['noPublicCertForEncrypt']="Kon het bericht niet versleutelen omdat u geen publieke sleutel heeft voor: %s. Open een gesigneerd bericht van deze ontvanger om de sleutel te importeren.";
$lang['smime']['noPrivateKeyForDecrypt']="Dit bericht is versleuteld en u heeft geen privésleutel om deze te ontcijferen.";

$lang['smime']['badGoLogin']="Het Group-Office wachtwoord was onjuist.";
$lang['smime']['smime_pass_matches_go']="Uw SMIME sleutelwachtwoord komt overeen met uw Group-Office wachtwoord. Dit is uit beveiligingsoogpunt niet toegestaan!";
$lang['smime']['smime_pass_empty']="Uw SMIME sleutel heeft geen wachtwoord. Dit is uit beveiligingsoogpunt niet toegestaan!";

$lang['smime']['invalidCert']="Dit certificaat is ongeldig!";
$lang['smime']['validCert']="Geldig certificaat";

$lang['smime']['decryptionFailed']='SMIME decodering van dit bericht is mislukt.';