<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of {product_name}. You should have received a copy of the
 * {product_name} license along with {product_name}. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: en.js 7708 2011-07-06 14:13:04Z wilmar1980 $
 * @author Dat Pham <datpx@fab.vn> +84907382345
 */
 
require($GO_LANGUAGE->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Quản lý sao lưu';
$lang['backupmanager']['description']='Cấu hình việc sao lưu';
$lang['backupmanager']['save_error']='Lỗi khi ghi thiết lập';
$lang['backupmanager']['empty_key']='Khóa đang chưa có';
$lang['backupmanager']['connection_error']='Không kết nối được với máy chủ';
$lang['backupmanager']['no_mysql_config']='{product_name} was not able to find a mysql config file. This file is used to create a backup of the complete database. You can create this yourself by adding a file named backupmanager.inc.php in /etc/groupoffice/ with the following contents:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Without this file backups are still created, but not from the database.';
$lang['backupmanager']['target_does_not_exist']='The target directory doesn\'t exist!';