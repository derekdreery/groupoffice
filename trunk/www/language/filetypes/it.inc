<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('filetypes'));

$ft_title = "Tipi di file";
$ft_text = "I tipi di file vengono aggiunti automaticamente da Group-Office. Qui è possibile cambiarne icona e nome.";

$ft_icon = "Icona";
$ft_extension = "Estensione";
$ft_friendly = "Descrizione";
$ft_content = "Tipo contenuto";
$ft_no_filetypes = "Nessun tipo di file trovato.";
$ft_upload_text = "Seleziona l'icona da importare. Sono accettati solo file GIF.";
$ft_no_file = "File non ricevuto.";
$ft_please_wait = "Importazione dell'icona in corso...";
$ft_properties = "Proprietà";
$ft_filename = "Nome file";
$ft_current_icon = "Icona corrente";


$filetypes['unknown'] = 'Tipo di file sconosciuto';
$filetypes['txt'] = 'File di testo';
$filetypes['sxw'] = 'Documento OpenOffice.Org Writer';
//$filetypes['doc'] = 'Documento Microsoft Word';
//$filetypes['xls'] = 'Foglio elettronico Microsoft Excel';
$filetypes['mdb'] = 'Database Microsoft Access';
$filetypes['ppt'] = $filetypes['pps'] = 'Presentazione Microsoft Powerpoint';
$filetypes['sxc'] = 'Foglio elettronico OpenOffice.Org Calc';
$filetypes['tar'] = 'Archiovio Tar';
$filetypes['zip'] = 'Archivio compresso ZIP';
$filetypes['rar'] = 'Archivio compresso RAR';
$filetypes['gz'] = $filetypes['tgz'] = 'Archivio compresso GZIP';
$filetypes['pdf'] = 'Documento PDF';
$filetypes['exe'] = 'File eseguibile Microsoft Windows';
$filetypes['ttf'] = 'Font True Type';
$filetypes['html'] = $filetypes['htm'] = 'Documento HTML';
$filetypes['jpg'] = $filetypes['jpeg'] = 'Immagine';
$filetypes['gif'] = 'Immagine';
$filetypes['bmp'] = 'Immagine';
$filetypes['tif'] = 'Immagine';
$filetypes['png'] = 'Immagine';
$filetypes['php'] = 'Script PHP';
$filetypes['ics'] = 'Informazioni Calendario';
$filetypes['vcf'] = 'Informazioni Contatti';
$filetypes['wav'] = $filetypes['ogg'] = $filetypes['mp3'] = $filetypes['wma'] = 'File Sonori';
$filetypes['mpg'] = $filetypes['mpeg'] = $filetypes['avi'] = $filetypes['wmv'] = $filetypes['wmf'] = 'Filmati Video';
$filetypes['pdf'] = 'Adobe Acrobat PDF';
$filetypes['doc'] = $filetypes['dot'] = 'Documento Microsoft Word';
$filetypes['psd'] = 'Documento Adobe Photoshop';
$filetypes['rtf'] = 'Rich Text Format';
$filetypes['swf'] = 'Filmato Macromedia Flash';
$filetypes['fla'] = 'Documento Macromedia Flash (Sorgente)';
$filetypes['xls'] = $filetypes['xlt'] = 'Foglio elettronico Microsoft Excel';
$filetypes['ai'] = 'Documento Adobe Illustrator';

?>
