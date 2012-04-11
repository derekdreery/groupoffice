<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('smime'));

$lang['smime']['name']='SMIME støtte';
$lang['smime']['description']='Utvide e-postmodulen med støtte for SMIME signering og kryptering.';

$lang['smime']['noPublicCertForEncrypt']="Kan ikke kryptere meldingen fordi du ikke har det offentlige sertifiktatet for %s. Du kan åpne en signert melding fra mottageren og kontrollere signaturen for å importere den offentlige nøkkelen.";
$lang['smime']['noPrivateKeyForDecrypt']="Denne meldingen er kryptert, men du har ikke den private nøkkelen som kreves for å dekryptere den.";

$lang['smime']['badGoLogin']="{product_name} passordet er feil.";
$lang['smime']['smime_pass_matches_go']="Passordet til SMIME nøkkelen er det samme som GroupOffice passordet. Av sikkerhetsårsaker er dette ikke tillatt!";
$lang['smime']['smime_pass_empty']="Denne SMIME nøkkelen har ikke noe passord. Av sikkerhetsårsaker er dette ikke tillatt!";

$lang['smime']['invalidCert']="Sertifikatet er ugyldig!";
$lang['smime']['validCert']="Gyldig sertifikat";
$lang['smime']['certEmailMismatch']="Sertfikatet er gyldig, men e-postadressen sertifikatet gjelder for er forskjellig fra denne e-postens avsenderadresse.";
$lang['smime']['decryptionFailed']='Feil ved SMIME dekryptering.';