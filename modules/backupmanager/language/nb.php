<?php


$l['name']='Backupmodul';
$l['description']='Sette opp din backup cronjobb';
$l['save_error']='Feil ved lagring av instillinger';
$l['empty_key']='Nøkkelen er tom';
$l['connection_error']='Kan ikke koble til serveren';
$l['no_mysql_config']='{product_name} kunne ikke finne en mysql config fil. Denne filen brukes for å lage en backup av den totale databasen. Du kan lage denne filen selv ved å opprette filen backupmanager.inc.php i /etc/groupoffice/ med følgende innhold:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Uten denne filen blir det likevel tatt backup, men ikke av databasen.';
$l['target_does_not_exist']='Målkatalogen finnes ikke!';
$l["backupmanager"]='Backupmodul';
$l["rmachine"]='Ekstern maskin';
$l["rport"]='Port';
$l["rtarget"]='Målkatalog';
$l["sources"]='Kildekataloger';
$l["rotations"]='Rotasjoner';
$l["quiet"]='Stille';
$l["emailaddresses"]='E-postadresse';
$l["emailsubject"]='E-postemne';
$l["rhomedir"]='Ekstern homekatalog';
$l["rpassword"]='Passord';
$l["publish"]='Publiser';
$l["enablebackup"]='Start backup';
$l["disablebackup"]='Stopp backup';
$l["successdisabledbackup"]='Backup er deaktivert!';
$l["publishkey"]='Aktiver backup';
$l["publishSuccess"]='Backup er aktivert!';
$l["helpText"]='Denne modulen vil sikkerhetskopiere filer og alle MySQL databaser (pass på at du inkluderer /home/mysqlbackup i kildekatalogene) til en ekstern server, med rsync og SSH. Når du aktiverer backup vil den sende den offentlige SSH nøkkelen til serveren og den vil sjekke at målkatalogen eksisterer. Pass derfor først på at målkatalogen eksisterer på den den eksterne serveren. Som standard kjøres backupjobben ved midnatt. Du kan endre dette i filen /etc/cron.d/groupoffice-backup. Du kan opprette filen hvis den ikke finnes. Du kan også kjøre backup manuelt ved å skrive "php /usr/share/groupoffice/modules/backupmanager/cron.php" i terminalvinduet.';