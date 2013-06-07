<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: en.js 7708 2011-07-06 14:13:04Z wilmar1980 $
 * @author Dat Pham <datpx@fab.vn> +84907382345
 */
 
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Danh bạ';
$lang['addressbook']['description'] = 'Module quản lý tất cả danh bạ';



$lang['addressbook']['allAddressbooks'] = 'Tất cả danh bạ';
$lang['common']['addressbookAlreadyExists'] = 'Danh bạ bạn tạo đã có';
$lang['addressbook']['notIncluded'] = 'Không nhập';

$lang['addressbook']['comment'] = 'Chú thích';
$lang['addressbook']['bankNo'] = 'Số ngân hàng'; 
$lang['addressbook']['vatNo'] = 'Mã số thuế';
$lang['addressbook']['contactsGroup'] = 'Nhóm';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Danh bạ';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Công ty';

$lang['addressbook']['customers'] = 'Khách hàng';
$lang['addressbook']['suppliers'] = 'Nhà cung cấp';
$lang['addressbook']['prospects'] = 'Khách hàng tiềm năng';


$lang['addressbook']['contacts'] = 'Danh bạ';
$lang['addressbook']['companies'] = 'Công ty';

$lang['addressbook']['newContactAdded']='Đã thêm mới danh bạ';
$lang['addressbook']['newContactFromSite']='Danh bạ được từ form.';
$lang['addressbook']['clickHereToView']='Nhánh vào đây để xem chi tiết';

$lang['addressbook']['contactFromAddressbook']='Danh bạ từ %s';
$lang['addressbook']['companyFromAddressbook']='Công ty từ %s';
$lang['addressbook']['defaultSalutation']='Dear [Mr./Mrs.] {middle_name} {last_name}';

$lang['addressbook']['multipleSelected']='Nhiều địa chỉ được chọn';
$lang['addressbook']['incomplete_delete_contacts']='Bạn không có quyền xóa danh bạ';
$lang['addressbook']['incomplete_delete_companies']='Bạn không có quyền xóa công ty';

$lang['addressbook']['emailAlreadyExists']='Địa chỉ email đã thêm vào danh bạ';
$lang['addressbook']['emailDoesntExists']='Địa chỉ email không tìm thấy';

$lang['addressbook']['imageNotSupported']='Ảnh bạn tải lên không hỗ trợ, chỉ những ảnh gif, png, jpg';
?>