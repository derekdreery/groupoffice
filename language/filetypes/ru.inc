<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_base_language_file('filetypes'));

$ft_title = "Filetypes";
$ft_text = "Filetypes are automatically added by Group-Office. You can change icons and names here.";

$ft_icon = "Иконка";
$ft_extension = "Расширение";
$ft_friendly = "Friendly name";
$ft_content = "Тип наполнения";
$ft_no_filetypes = "No filetypes found.";
$ft_upload_text = "Select the filetype icon to upload. Only GIF files are accepted.";
$ft_no_file = "No file was recieved.";
$ft_please_wait = "Please wait while uploading icon...";
$ft_properties = "Настройки";
$ft_filename = "Имя файла";
$ft_current_icon = "Текущая иконка";

$filetypes['unknown'] = 'Неизвестный тип файла';
$filetypes['txt'] = 'Текстовый файл';
$filetypes['sxw'] = 'Документ OpenOffice.Org Writer';
$filetypes['doc'] = 'Документ Microsoft Word';
$filetypes['xls'] = 'Электронные таблицы Microsoft Excel';
$filetypes['mdb'] = 'База данных Microsoft Access';
$filetypes['ppt'] = $filetypes['pps'] = 'Презентация Microsoft Powerpoint';
$filetypes['sxc'] = 'Электронные таблицы OpenOffice.Org Calc';
$filetypes['tar'] = 'Tar архив';
$filetypes['zip'] = 'ZIP compressed archive';
$filetypes['rar'] = 'RAR compressed archive';
$filetypes['gz'] = $filetypes['tgz'] = 'GZIP compressed archive';
$filetypes['bz2'] = 'BZIP2 compressed archive'; 
$filetypes['pdf'] = 'Документ PDF';
$filetypes['exe'] = 'Запускаемый Windows файл';
$filetypes['ttf'] = 'Шрифт True type';
$filetypes['html'] = $filetypes['htm'] = 'Документ HTML';
$filetypes['jpg'] = $filetypes['jpeg'] = 'Изображение';
$filetypes['gif'] = 'Изображение';
$filetypes['bmp'] = 'Изображение';
$filetypes['tif'] = 'Изображение';
$filetypes['png'] = 'Изображение';
$filetypes['php'] = 'Скрипт PHP';
$filetypes['ics'] = 'Календарная информация';
$filetypes['vcf'] = 'Контактная информация';
$filetypes['wav'] = $filetypes['ogg'] = $filetypes['mp3'] = $filetypes['wma'] = 'Звуковой файл';
$filetypes['mpg'] = $filetypes['mpeg'] = $filetypes['avi'] = $filetypes['wmv'] = $filetypes['wmf'] = 'Видео файл';

?>