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
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Công việc ';
$lang['tasks']['description']='Thêm mô tả vào đây';

$lang['link_type'][12]=$lang['tasks']['task']='Công việc';
$lang['tasks']['status']='Hiện trạng';


$lang['tasks']['scheduled_call']='Kế hoạch sẽ được gọi vào lúc %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Cần xử lý';
$lang['tasks']['statuses']['ACCEPTED'] = 'Chấp nhận';
$lang['tasks']['statuses']['DECLINED'] = 'Bỏ qua';
$lang['tasks']['statuses']['TENTATIVE'] = 'Do dự';
$lang['tasks']['statuses']['DELEGATED'] = 'Ủy quyền';
$lang['tasks']['statuses']['COMPLETED'] = 'Hoàn thành';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Đang xử lý';

$lang['tasks']['import_success']='%s công việc được thêm';

$lang['tasks']['call']='Gọi';

$lang['tasks']['dueAtdate']='Đến hạn %s';

$lang['tasks']['list']='Danh sách công việc';
$lang['tasks']['tasklistChanged']="* Danh sách thay đổi từ'%s' tới '%s'";
$lang['tasks']['statusChanged']="* Hiện trạng thay đổi từ '%s' tới '%s'";
$lang['tasks']['multipleSelected']='Đã nhọn nhiều danh sách';
$lang['tasks']['incomplete_delete']='Bạn không có quyền xóa các danh sách chọn';