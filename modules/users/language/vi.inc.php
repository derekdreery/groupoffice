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
require($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = 'Người dùng';
$lang['users']['description'] = 'Module quản trị người dùng';

$lang['users']['deletePrimaryAdmin'] = 'Bạn không thể xóa quản trị hệ thống';
$lang['users']['deleteYourself'] = 'Bạn không thể xóa mình';

$lang['link_type'][8]=$us_user = 'Người dùng';

$lang['users']['error_username']='Tên người dùng không hợp lệ';
$lang['users']['error_username_exists']='Xin lỗi, người dùng đã có';
$lang['users']['error_email_exists']='Xin lỗi, địa chỉ email này đã đăng ký';
$lang['users']['error_match_pass']='Mật khẩu không đúng';
$lang['users']['error_email']='Email không đúng';
$lang['users']['error_user']='Người dùng không được tạo';

$lang['users']['imported']='Đã import %s người dùng';
$lang['users']['failed']='Lỗi';

$lang['users']['incorrectFormat']='File không đúng định dạng';

$lang['users']['register_email_subject']='Chi tiết tài khoản';
$lang['users']['register_email_body']='Tài khoản đã được tạo cho bạn tại: {url}
Thông tin đăng nhập chi tiết:

Người dùng: {username}
Mật khẩu: {password}';


$lang['users']['max_users_reached']='Đã đến tối đa người dùng được sử dụng cho hệ thống.';