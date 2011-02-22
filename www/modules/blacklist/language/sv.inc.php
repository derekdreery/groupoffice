<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='Svartlistade IP-adresser';
$lang['blacklist']['description']='Modul som blockerar IP-adresser efter 5 misslyckade inloggningsförsök i rad.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP-adresser';

$lang['blacklist']['blacklisted']='Din IP-adress %s blockeras eftersom du har försökt logga in 5 gånger i följd med felaktigt användarnamn eller lösenord. Kontakta system-administratören och tillhandahåll din IP-adress för att ta bort blockeringen.';
$lang['blacklist']['captchaIncorrect']='Säkerhetskoden du angav är inkorrekt, var vänlig och försök igen.';
$lang['blacklist']['captchaActivated']='Tre misslyckade inloggningsförsök har upptäckts. För att kunna logga in krävs att du även anger säkerhetskoden.';
