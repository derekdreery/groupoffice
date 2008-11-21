<?php

// LANG: "fr", ENCODING: UTF-8
// Author: L. Jullien
// 15 Jan 2007

//co-Author (only corrections of orthography): j.s Hubert jsh@interlug.net
//13 aout 2007

require_once($GO_LANGUAGE->get_fallback_base_language_file('filetypes'));

$ft_title = "Types de fichiers";
$ft_text = "Les types de fichiers sont automatiquement ajoutés par Group-Office. Vous pouvez changer les icones et les noms ici.";
$ft_icon = "Icone";
$ft_extension = "Extension";
$ft_friendly = "Surnom";
$ft_content = "Type de contenu";
$ft_no_filetypes = "Pas de type de fichiers trouvé.";
$ft_upload_text = "Choisissez l'icone du type de fichier à charger. Seuls les fichiers GIF sont acceptés.";
$ft_no_file = "Aucun fichier reçu.";
$ft_please_wait = "Patientez pendant que l'icone est téléchargée..";
$ft_properties = "Propriétés";
$ft_filename = "Nom de fichier";
$ft_current_icon = "Icone en cours";

$filetypes['unknown'] = 'Type inconnu';
$filetypes['txt'] = 'Fichier texte';
$filetypes['sxw'] = 'Document OpenOffice.Org Writer';
$filetypes['doc'] = 'Document Microsoft Word';
$filetypes['dot'] = 'Modèle Microsoft Word';
$filetypes['xls'] = 'Feuille de calcul Microsoft Excel';
$filetypes['xlt'] = 'Feuille de calcul Microsoft Excel';
$filetypes['mdb'] = 'Base de données Microsoft Access';
$filetypes['ppt'] = 'Présentation Microsoft Powerpoint';
$filetypes['pps'] = 'Présentation Microsoft Powerpoint';
$filetypes['sxc'] = 'Feuille de calcul OpenOffice.Org Calc';
$filetypes['tar'] = 'Archive Tar';
$filetypes['zip'] = 'Archive compressée ZIP';
$filetypes['rar'] = 'Archive compressée RAR';
$filetypes['gz'] = 'Archive compressée GZIP'; 
$filetypes['tgz'] = 'Archive compressée GZIP';
$filetypes['bz2'] = 'Archive compressée BZIP2'; 
$filetypes['exe'] = 'Fichier exécutable Windows';
$filetypes['ttf'] = 'Police TrueType';
$filetypes['html'] = 'Fichier Web HTML';
$filetypes['htm'] = 'Fichier Web HTM';
$filetypes['jpg'] = 'Image';
$filetypes['jpeg'] = 'Image';
$filetypes['gif'] = 'Image';
$filetypes['bmp'] = 'Image';
$filetypes['tif'] = 'Image';
$filetypes['png'] = 'Image';
$filetypes['php'] = 'Script PHP';
$filetypes['asp'] = 'Script ASP';
$filetypes['ics'] = 'Informations de calendrier';
$filetypes['vcf'] = 'Fiche contact';
$filetypes['wav'] = 'Fichier son WAV';
$filetypes['ogg'] = 'Fichier son OGG';
$filetypes['mp3'] = 'Fichier son MP3';
$filetypes['wma'] = 'Fichier son WMA';
$filetypes['mpg'] =  'Fichier vidéo MPG';
$filetypes['mpeg'] = 'Fichier vidéo MPEG';
$filetypes['avi'] =  'Fichier vidéo AVI';
$filetypes['wmv'] = 'Fichier vidéo WMV'; 
$filetypes['wmf'] = 'Fichier vidéo WMF';
$filetypes['pdf'] = 'Fichier Adobe Acrobat';
$filetypes['psd'] = 'Fichier Adobe Photoshop';
$filetypes['rtf'] = 'Fichier texte avec mise en forme';
$filetypes['swf'] = 'Animation Macromedia Flash';
$filetypes['fla'] = 'Animation Macromedia Flash (Source)';
$filetypes['ai'] = 'Fichier Adobe Illustrator';
$filetypes['eml'] = 'Message Email';
?>
