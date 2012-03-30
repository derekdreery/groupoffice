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
 
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='Địa chỉ IP đen';
$lang['blacklist']['description']='Module sẽ yêu cầu người dùng nhập những ký tự ngẫu nhiên sau ba lần đăng nhập lỗi.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='Địa chỉ IP';

$lang['blacklist']['blacklisted']='Địa chỉ IP của bạn %s đang bị khóa bởi vì đăng nhập liên tiếp 3 lần . Liên lạc với người quản trị để mở khóa.';
$lang['blacklist']['captchaIncorrect']='Mã bảo mật không đúng, xin nhập lại.';
$lang['blacklist']['captchaActivated']='Đã đăng nhập ba lần không được. Để đăng nhập bạn cần nhập thêm mã bảo mật.';
