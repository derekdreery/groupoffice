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
 
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Full featured e-mail client. Every user will be able to send and receive emails';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Bạn chưa nhập người nhận';
$lang['email']['feedbackSMTPProblem'] = 'Có vấn đề khi liên kết với SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Có lỗi khi lập email: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Lỗi tạo thư mục';
$lang['email']['feedbackDeleteFolderFailed'] = 'Lỗi tạo thư mục';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Lỗi khi đánh dấu thư mục';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Lỗi không đánh dấu thư mục';
$lang['email']['feedbackCannotConnect'] = 'Không kết nối tới %1$s tại cổng %3$s<br /><br />Máy chủ email trả về: %2$s';
$lang['email']['inbox'] = 'Hòm thư';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Đệm';
$lang['email']['sent']='Thư đã gửi';
$lang['email']['drafts']='Thư nháp';

$lang['email']['no_subject']='Không chủ đề';
$lang['email']['to']='Tới';
$lang['email']['from']='Từ';
$lang['email']['subject']='Chủ đề';
$lang['email']['no_recipients']='Không đóng danh sách nhận';
$lang['email']['original_message']='--- Email gốc đươi đây ---';
$lang['email']['attachments']='Đính kèm';

$lang['email']['notification_subject']='Đọc: %s';
$lang['email']['notification_body']='Thư với chủ đề "%s" được hiển thị tại %s';

$lang['email']['errorGettingMessage']='Không lấy được email từ máy chủ';
$lang['email']['no_recipients_drafts']='Không người nhận';
$lang['email']['usage_limit'] = '%s trong %s đã dùng';
$lang['email']['usage'] = '%s dùng';

$lang['email']['event']='Lịch họp';
$lang['email']['calendar']='Lịch';

$lang['email']['quotaError']="Hòm thư đầy, xóa thư mục đệm trước. Nếu đã xóa hết bạn phải bỏ thư mục đệm và xóa thêm thư mục khác, ,bỏ đệm tại:\n\nThiết lập -> Tài khoản -> Kích đúp vào tài khoản -> Thư mục.";

$lang['email']['draftsDisabled']="Thư không được lưu vì gỡ bỏ thư mục nháp để kích hoạt .<br /><br />Vào E-mail -> Quản trị -> Tài khoản -> Nhấm đúp vào tài khoản -> Thư mục để cấu hình.";
$lang['email']['noSaveWithPop3']='Thư không được ghi vì POP3 không hỗ trợ';

$lang['email']['goAlreadyStarted']='hệ thống đã chạy. Trình soạn email được nạp. đóng cửa sổ này và soạn thư.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='Vào lúc %s, %s trên %s %s viết:';
$lang['email']['alias']='Viết tắt';
$lang['email']['aliases']='Viết tắt';
$lang['email']['alias']='Viết tắt';
$lang['email']['aliases']='Viết tắt';

$lang['email']['noUidNext']='Máy chủ email không hỗ trợ Unicode. Thư mục\'Nháp\' tự động không sử dụng.';

$lang['email']['disable_trash_folder']='Chuyển  e-mail tới thư mục đệm lỗi';

$lang['email']['error_move_folder']='Không thể chuyển tới thư mục';

$lang['email']['error_getaddrinfo']='Sai địa chỉ máy chủ';
$lang['email']['error_authentication']='Sai người dùng và mật khẩu';
$lang['email']['error_connection_refused']='Kết nối không được. kiểm tra lại thông tin kết nối.';

$lang['email']['iCalendar_event_invitation']='Thư này chứa sự kiện và lời mời.';
$lang['email']['iCalendar_event_not_found']='Thư này chứa sự kiện cập nhật chưa có ở đâu.';
$lang['email']['iCalendar_update_available']='Thư này chứa và cập nhật một sự kiện đã có.';
$lang['email']['iCalendar_update_old']='Thư này chứa sự kiện đã xử lý.';
$lang['email']['iCalendar_event_cancelled']='Thư này chứa sự kiện bị hủy.';
$lang['email']['iCalendar_event_invitation_declined']='Thư này chứa sự kiện và lời mời bị bỏ qua.';

$lang['email']['untilDateError']='Đã cố ghắng xử lý đến ngày hết hạn nhưng xử lý bị dừng vì có lỗi';