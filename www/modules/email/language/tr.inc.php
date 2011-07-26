<?php

require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));

$lang['email']['name'] = 'E-posta';
$lang['email']['description'] = 'Tam özellikli E-posta istemcisi. Her kullanıcı E-posta gönderip alabilme özelliğine sahiptir';
$lang['link_type'][9] = 'E-posta';
$lang['email']['feedbackNoReciepent'] = 'Bir alıcı belirtmediniz';
$lang['email']['feedbackSMTPProblem'] = 'SMTP ile iletişim kurarken sorun oluştu: ';
$lang['email']['feedbackUnexpectedError'] = 'E-posta oluşumu sırasında beklenmeyen bir sorun oluştu: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Klasör yaratılamıyor';
$lang['email']['feedbackDeleteFolderFailed'] = 'Klasör silinemiyor';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Klasöre erişim sağlanamıyor';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Klasörden erişim kaldırılamıyor';
$lang['email']['feedbackCannotConnect'] = ' %1$s ile %3$s portu üzerinden erişim sağlanamıyor<br /><br />Posta sunucusunun verdiği cevap: %2$s';
$lang['email']['inbox'] = 'Gelen Kutusu';
$lang['email']['spam'] = 'Spam';
$lang['email']['trash'] = 'Çöp';
$lang['email']['sent'] = 'Gönderilmiş öğeler';
$lang['email']['drafts'] = 'Taslaklar';
$lang['email']['no_subject'] = 'Konu yok';
$lang['email']['to'] = 'Alıcı';
$lang['email']['from'] = 'Kimden';
$lang['email']['subject'] = 'Konu';
$lang['email']['no_recipients'] = 'Alıcı mevcut değil';
$lang['email']['original_message'] = '--- Orjinal mesaj aşşağıdaki gibidir ---';
$lang['email']['attachments'] = 'Eklentiler';
$lang['email']['notification_subject'] = 'Oku: %s';
$lang['email']['notification_body'] = '"%s" konulu mesajınız %s görüntülendi';
$lang['email']['errorGettingMessage'] = 'Sunucudan mesaj alınamıyor';
$lang['email']['no_recipients_drafts'] = 'Alıcı yok';
$lang['email']['usage_limit'] = '%s : kullanılan %s içersinden';
$lang['email']['usage'] = '%s kullanıldı';
$lang['email']['event'] = 'Davet';
$lang['email']['calendar'] = 'takvim';
$lang['email']['quotaError'] = "Posta kutunuz dolu. İlk önce çöp kutusunu boşaltın. Eğer zaten boş ise ve Posta kutunuz dolu ise Çöpkutusu klasörünün diğer klasörleri silmemesi için aktivasyonunu kaldırmalısınız. Aktivasyonu kaldırmak için:\n\nAyarlar -> Hesaplar -> Hesabı çift tıklayın -> Klasörler.";
$lang['email']['draftsDisabled'] = "Mesaj kaydedilemiyor çünkü 'Taslaklar' klasörü aktif değil.<br /><br />Ayarlar -> Hesaplar -> hesabın üzerini çift tıkla -> Klasörler kısmına git ve ayarla lütfen.";
$lang['email']['noSaveWithPop3'] = 'Mesaj kaydedilemiyor çünkü bir POP3 hesabı bu durumu desteklemiyor.';
$lang['email']['goAlreadyStarted'] = 'Group-Office zaten çalışıyor.';
?>