<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));

$lang['calendar']['name'] = 'Takvim';
$lang['calendar']['description'] = 'Takvim modülü; Tüm kullanıcılar davet gönderip ekleyebilirler, ayrıca gönderilmiş davetleri açıp kaydedebilir veya silebilirler.';

$lang['link_type'][1]='Davet';

$lang['calendar']['groupView'] = 'Gurup görünümü';
$lang['calendar']['event']='Olay';
$lang['calendar']['startsAt']='Başlangıç';
$lang['calendar']['endsAt']='Bitiş';

$lang['calendar']['exceptionNoCalendarID'] = 'HATA: Takvim TN yoktur!';
$lang['calendar']['appointment'] = 'Davet: ';
$lang['calendar']['allTogether'] = 'Hepsi Birlikte';

$lang['calendar']['location']='Yer';

$lang['calendar']['invited']='Belirtilen Aktiviteye davetlisiniz';
$lang['calendar']['acccept_question']='Bu aktiviteyi onaylıyormusunuz?';

$lang['calendar']['accept']='Kabul Et';
$lang['calendar']['decline']='Reddet';

$lang['calendar']['bad_event']='Olay artık mevcut değil';

$lang['calendar']['subject']='Konu';
$lang['calendar']['status']='Durum';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Aksiyon lazım';
$lang['calendar']['statuses']['ACCEPTED'] = 'Kabul Edilmiş';
$lang['calendar']['statuses']['DECLINED'] = 'Reddedilmiş';
$lang['calendar']['statuses']['TENTATIVE'] = 'Değişebilir';
$lang['calendar']['statuses']['DELEGATED'] = 'Delege Edilmiş';
$lang['calendar']['statuses']['COMPLETED'] = 'Bitmiş';
$lang['calendar']['statuses']['IN-PROCESS'] = 'İşlemde';


$lang['calendar']['accept_mail_subject'] = ' \'%s\' için davet kabul edilmiş';
$lang['calendar']['accept_mail_body'] = '%s yaptığınız daveti kabul etti:';

$lang['calendar']['decline_mail_subject'] = ' \'%s\' için olan davet reddedildi';
$lang['calendar']['decline_mail_body'] = '%s davetinizi reddetti :';

$lang['calendar']['location']='Yer';
$lang['calendar']['and']='ve';

$lang['calendar']['repeats'] = 'Her %s için tekrar et';
$lang['calendar']['repeats_at'] = 'Her %s için her %s de tekrarlanır';//eg. Her ayın ilk pazartesi günü tekrarlanır
$lang['calendar']['repeats_at_not_every'] = 'Her %s için %s te %s günü tekrarlanır';//eg. Her iki haftada bir pazartesi günü tekrarlanır
$lang['calendar']['until']='kadar'; 

$lang['calendar']['not_invited']='Belirtilen olay için davetiniz yoktur. Farklı bir kullanıcı ile sisteme bağlanmalısınız.';


$lang['calendar']['accept_title']='Kabul Edilmiş';
$lang['calendar']['accept_confirm']='Aktiviteyi gönderen kişi onayladığınızla ilgili bilgilenecektir';

$lang['calendar']['decline_title']='Reddedilmiş';
$lang['calendar']['decline_confirm']='Aktiviteyi gönderen kişi onaylamadığınızla ilgili bilgilenecektir';

$lang['calendar']['cumulative']='Geçersiz tekrarlama kuralı. Sonraki tekrar önceki bitmeden başlayamaz.';

$lang['calendar']['already_accepted']='Bu olayı zaten kabul etmişsiniz.';

$lang['calendar']['private']='Özel';

$lang['calendar']['import_success']='%s Aktivite İçeri Aktarılmıştır';

$lang['calendar']['printTimeFormat']=' %s dan(den) %s\' a(e) kadar';
$lang['calendar']['printLocationFormat']=' Yer olarak "%s"';
$lang['calendar']['printPage']='%s sayfa, %s Sayfada';
$lang['calendar']['printList']='Davet listesi';

$lang['calendar']['printAllDaySingle']='Tüm gün';
$lang['calendar']['printAllDayMultiple']='%s \'dan %s kadar dolu';
?>