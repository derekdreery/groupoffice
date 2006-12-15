<?php
require_once($GO_LANGUAGE->get_fallback_language_file('filesystem'));

$auml = utf8_encode('�');
$ouml = utf8_encode('�');
$uuml = utf8_encode('�');
$Auml = utf8_encode('�');
$Ouml = utf8_encode('�');
$Uuml = utf8_encode('�');
$szlig = utf8_encode('�');

$strLookIn = 'Öffnen';
$strType = "Typ";
$strModified = "Geändert";
$strCreated = "Angelegt";
$strAccessed = "Zugegriffen";
$strSize = "Grösse";
$fbRefresh = "Aktualisieren";
$fbProperties = "Eigenschaften";
$fbNewFolder = "Neuer Ordner";
$fbDelete = "Löschen";
$fbCut = "Ausschneiden";
$fbPaste = "Einfügen";
$fbCopy = "Kopieren";
$fbFiletypes = "Dateitypen";
$fbSharing = "Eigene Freigaben";
$fbShares = "Freigaben";
$fbShare = "Freigabe";
$fbPath = "Pfad";
$fbFolders = "Ordner";
$fbNoSelect = "Sie haben kein Element ausgewählt. Zum Auswählen bitte auf das Symbol neben dem Namen eines Elementes klicken.";
$fbConfirmDelete = "Löschung bestätigen";
$fbWritePermissions = "Schreibberechtigungen";
$fbReadPermissions = "Leseberechtigungen";
$fbGeneral = "Allgemein";
$fbConfirmOverwrite = "Überschreiben bestätigen";
$strOverwritePrefix = "Soll die Datei ";
$strOverwriteSuffix = " überschrieben werden?";
$fbDescription = "Beschreibung";
$fbLocation = "Position";
$fbUpload = "Hochladen";
$fbUpload_text = "Datei zum Hochladen auswählen";
$fbUpload_nofile = "Es wurde keine Datei empfangen.";
$fbVersions = "Versionen";
$fbDelete_version = "Soll die aktuelle Version gelöscht werden?";
$fbFolderExists = "Der Ordner existiert bereits.";
$fbEmailFile = "Email-Datei";
$fbFolder = "Ordner";

$fbNoFile = "Die Datei ist zu gross.<br /> Die maximale Grösse für eine zu übertragende Datei liegt bei: ";
$fbPleaseWait = "Bitte warten. Daten werden gesendet...";
$fbFilename = "Dateiname";
$fbUpLevel = "Ebene höher";
$fbItems = "Element(e)";
$fbFolderSize = "Grösse des Ordners";
$fbMaxUploadSize = "Maximale Grösse";
$ml_file_too_big = "Die Datei ist zu gross. Die maximale Grösse fuer einen Dateianhang liegt bei: ";
$fbNoRightToOverwrite = "Die Datei existiert bereits. Und Sie haben nicht die erforderlichen Rechte, diese zu überschreiben.";
$fbSaveFile = "Datei speichern unter...";
$fbNameExists = "Der Name existiert bereits in diesem Ordner.";
$fbThis = 'diese';
$fbFiles = 'Dateien';
$fs_activate_sharing = 'Freigabe aktivieren';
$fs_calculate_folder_size = 'Ordnergrösse berechnen';
$fs_no_multi_select = 'Es darf nicht mehr als ein Element mit dieser Option ausgewählt werden';
$fs_search = 'Suchen';
$search_title = "Suchen nach Dateien und Ordnern";
$fs_keyword = 'Suchbegriff';
$fs_enter_keyword = 'Bitte einen Suchbegriff eingeben';
$fs_modified_before = 'Geändert vor';
$fs_modified_after = 'Geändert nach';

//translation required
$fb_compress='Packen';
$fb_decompress='Entpacken';
$fb_failed_to_create = 'Das Packen ist fehlgeschlagen.';
$fb_failed_to_extract = 'Das Entpacken ist fehlgeschlagen.';
$fb_compression_type = 'Kompression';
$fbFreeQuotaSpace = 'Frei verfügbarer Speicher';

//2.11
$fb_unkown_compression = 'Kompressionstyp unbekannt';
$fs_new_doc = 'Neues Dokument';
$fs_cleanup = 'HTML bereinigen';
$fbMultipleFiles = 'Mehrere Dateien';
$fbSelect = "Datei wählen"; //changed

//2.14
$fs_confirm_unshare = 'Alle Berechtigungen bezogen auf diesen Ordner werden entfernt. Sind Sie sicher?';
$fs_users = 'Benutzer';
$fs_personal = 'Persönlich';

$fs_inssufficient_diskspace = 'Speicherplatz unzureichend';

//2.15
$fs_open_containing_folder = 'Öffne betreffende Ordner';
$fs_file_put_in='The file  "%s" ist hochgeladen nach "%s"';
$fs_new_file_uploaded = 'Neue Datei hochgeladen: %s';

$fs_download = 'Download';
$fs_view = 'Ansicht';

$fs_notify = 'Sende mir eine E-Mail wenn jemand eine neue Datei hochläd';
?>
