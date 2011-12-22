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
 
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Lịch';
$lang['calendar']['description'] = 'Module lịch làm việc; Mỗi người dùng có thể thêm, sửa hay xóa các lịch họp, kể cả lịch của người khác nếu có quyền';

$lang['link_type'][1]='Lịch họp';

$lang['calendar']['groupView'] = 'Nhóm hiển thị';
$lang['calendar']['event']='Sự kiện';
$lang['calendar']['startsAt']='Bắt đầu';
$lang['calendar']['endsAt']='Kết thúc';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAL: No calendar ID!';
$lang['calendar']['appointment'] = 'Lịch họp: ';
$lang['calendar']['allTogether'] = 'Tất cả cùng';

$lang['calendar']['location']='Địa điểm';

$lang['calendar']['invited']='Bạn được mời tham gia các sự kiện sau';
$lang['calendar']['acccept_question']='Bạn có đồng ý tham gia?';

$lang['calendar']['accept']='Đồng ý';
$lang['calendar']['decline']='Bỏ qua';

$lang['calendar']['bad_event']='Sự kiện không còn';

$lang['calendar']['subject']='Chủ đề';
$lang['calendar']['status']='Hiện trạng';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Cần hành động';
$lang['calendar']['statuses']['ACCEPTED'] = 'Đã đồng ý';
$lang['calendar']['statuses']['DECLINED'] = 'Bỏ qua';
$lang['calendar']['statuses']['TENTATIVE'] = 'Do dự';
$lang['calendar']['statuses']['DELEGATED'] = 'Ủy quyền';
$lang['calendar']['statuses']['COMPLETED'] = 'Hoàn thành';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Đang xử lý';
$lang['calendar']['statuses']['CONFIRMED'] = 'Xác nhận';


$lang['calendar']['accept_mail_subject'] = 'Mời \'%s\' xác nhận';
$lang['calendar']['accept_mail_body'] = '%s đã nhận lời mời của bạn cho:';

$lang['calendar']['decline_mail_subject'] = 'Lời mời cho \'%s\' đã bỏ qua';
$lang['calendar']['decline_mail_body'] = '%s đã bỏ qua lời mời của bạn cho:';

$lang['calendar']['location']='Vị trí';
$lang['calendar']['and']='và';

$lang['calendar']['repeats'] = 'Lặp lại mỗi %s';
$lang['calendar']['repeats_at'] = 'Lặp lại mỗi %s tại %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Lặp lại mỗi %s %s tại %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['repeats_not_every'] = 'Lặp lại mỗi %s %s';
$lang['calendar']['until']='tới'; 

$lang['calendar']['not_invited']='Bạn không được mời tham gia sự kiện, bạn có thể cần đăng nhập sang người dùng khác';


$lang['calendar']['accept_title']='Đồng ý';
$lang['calendar']['accept_confirm']='Người tạo sẽ được báo là bạn đã đồng ý';

$lang['calendar']['decline_title']='Bỏ qua';
$lang['calendar']['decline_confirm']='Người tạo sẽ được báo là bạn bỏ qua';

$lang['calendar']['cumulative']='Sai quy tắc lặp, lần cảnh báo tiếp sẽ không thực hiện.';

$lang['calendar']['already_accepted']='Bạn đã đồng ý tham gia sự kiện.';

$lang['calendar']['private']='riêng tư';

$lang['calendar']['import_success']='%s sự kiện được nhập';

$lang['calendar']['printTimeFormat']='Từ %s tới %s';
$lang['calendar']['printLocationFormat']=' tại địa điểm "%s"';
$lang['calendar']['printPage']='Trang %s / %s';
$lang['calendar']['printList']='Danh sách cuộc họp';

$lang['calendar']['printAllDaySingle']='Cả ngày';
$lang['calendar']['printAllDayMultiple']='Cả ngày từ %s tới %s';

$lang['calendar']['calendars']='Lịch';

$lang['calendar']['open_resource']='Mở đặt chỗ';

$lang['calendar']['resource_mail_subject']='Nguồi \'%s\' đã đặt cho \'%s\' vào lúc \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s đã đặt cho nguồn \'%s\'. Bạn là người xem xét nguồn. Hãy mở thời hạn đặt hoặc chấp nhận.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Nguồn \'%s\' đặt cho \'%s\' vào lúc \'%s\' đã sửa';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s đã sửa đặt chỗ cho phòng \'%s\'. Bạn quản lý phòng này. Hãy mở hạn đặt chỗ hoặc chấp nhận.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Bạn đã đặt \'%s\' vào lúc \'%s\' với trạng thái \'%s\' đã thay đổi';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s đã sửa đặt lịch của bạn cho phòng  \'%s\'.';

$lang['calendar']['your_resource_accepted_mail_subject']='Lịch của bạn đặt\'%s\' vào lúc \'%s\' được chấp nhận';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s đã chấp nhận lịch đặt của bạn cho phòng \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_declined_mail_subject']='Lịch đặt của bạn cho \'%s\' vào lúc \'%s\' bị bỏ qua';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s đã bỏ qua đặt lịch của bạn cho \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='Sinh nhật: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} đã sang tuổi {AGE} vào hôm nay';

$lang['calendar']['unauthorized_participants_write']='Bạn không đủ quyền để lập lịch họp cho những người:<br /><br />{NAMES}<br /><br />Bạn có muốn gửi cho họ lời mời để họ có thể chấp nhận lịch họp đó.';

$lang['calendar']['noCalSelected'] = 'Không có lịch được chọn để xem. Chọn ít nhất một lịch.';

$lang['calendar']['month_times'][1]='đầu tiên';
$lang['calendar']['month_times'][2]='thứ 2';
$lang['calendar']['month_times'][3]='thứ 3';
$lang['calendar']['month_times'][4]='thứ 4';
$lang['calendar']['month_times'][5]='thứ 5';

$lang['calendar']['rightClickToCopy']='Nhấn chuột phải để copy';

$lang['calendar']['invitation']='Lời mời';
$lang['calendar']['invitation_update']='Cập nhật lời mời';
$lang['calendar']['cancellation']='Hủy hỏ';

$lang['calendar']['non_selected'] = 'Không có lịch chọn';

$lang['calendar']['linkIfCalendarNotSupported']='Sử dụng liên kết dưới đây nếu trình duyệt email của bạn không hỗ trợ chức năng đặt lịch.';