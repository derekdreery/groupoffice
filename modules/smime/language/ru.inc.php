<?php
require($GO_LANGUAGE->get_fallback_language_file('smime'));

$lang['smime']['name']='Поддержка SMIME';
$lang['smime']['description']='Расширение модуля Почта шифрованием и ЭЦП SMIME.';

$lang['smime']['noPublicCertForEncrypt']="Не могу заширвать сообщение потому что нет публичного сертификата для %s. Откройте подписанное жти отправителем сообщение и проверьте ЭЦП чтобы импортировать его сертификат.";
$lang['smime']['noPrivateKeyForDecrypt']="Это сообщение зашифровано и у Вас нет личного сертификата чтобы расшифровать его.";

$lang['smime']['badGoLogin']="Некоррктный  {product_name} пароль.";
$lang['smime']['smime_pass_matches_go']="Пароль вашего SMIME сертификата сопадает с паролем {product_name}. Это запрещено в целях безопасности!";
$lang['smime']['smime_pass_empty']="Ваш SMIME сертификат не имеет пароля. Это запрещено в целях безопасности!";

$lang['smime']['invalidCert']="Неверный сертификат!";
$lang['smime']['validCert']="Сертификат верен";
$lang['smime']['certEmailMismatch']="Сертификат верен, но e-mail сортификата не соотвествует адресу отправителя.";

$lang['smime']['decryptionFailed']='SMIME ошибка рассшифровки сообщения.';
