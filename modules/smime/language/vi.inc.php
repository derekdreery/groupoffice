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
require($GO_LANGUAGE->get_fallback_language_file('smime'));

$lang['smime']['name']='Hỗ trợ SMIME ';
$lang['smime']['description']='Mở rộng module mail với mã hóa và chứng thực.';

$lang['smime']['noPublicCertForEncrypt']="Thông thể mã hóa thư bởi vì bạn chưa có chứng chỉ công khai cho%s.Mở thư đã đăng ký và kiểm tra chữ ký nhập vào khóa công khai.";
$lang['smime']['noPrivateKeyForDecrypt']="Thư này đã mã hóa, bạn không có khóa riêng để giải mã.";

$lang['smime']['badGoLogin']="Mật khẩu vào hệ thống không hợp lệ.";
$lang['smime']['smime_pass_matches_go']="Vì lý do bảo mật kế thừa, mật khẩu SMIME của bạn phải đúng với mật khẩu hệ thống!";
$lang['smime']['smime_pass_empty']="Vì lý do bảo mật kế thừa, cần có mật khẩu cho khóa SMIME!";

$lang['smime']['invalidCert']="Chứng thực không hợp lệ!";
$lang['smime']['validCert']="Chứng thực hợp lệ";
$lang['smime']['certEmailMismatch']="Chứng thực hợp lệ nhưng email của chứng thực không khớp với địa chỉ email của người gửi.";

$lang['smime']['decryptionFailed']='Giải mã SMIME lỗi.';
