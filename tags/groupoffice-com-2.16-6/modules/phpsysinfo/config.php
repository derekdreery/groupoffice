<?php 

// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/

// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

// $Id: config.php,v 1.2 2005/04/15 21:30:53 mschering Exp $

// define the default lng and template here
require_once('../../Group-Office.php');
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('phpsysinfo');
if(file_exists($GO_CONFIG->root_path.'administrator/phpsysinfo/includes/lang/'.$GO_LANGUAGE->language['language_file'].'.php'))
{
	$default_lng=$GO_LANGUAGE->language['language_file'];
}else
{
	$default_lng='en';
}
$default_template='groupoffice';

// hide language and template picklist
// false = display picklist    true = do not display picklist
$hide_picklist = true;

// define the motherboard monitoring program here
// we support four programs so far
// 1. lmsensors  http://www2.lm-sensors.nu/~lm78/
// 2. healthd    http://healthd.thehousleys.net/
// 3. hwsensors  http://www.openbsd.org/
// 4. mbmon      http://www.nt.phys.kyushu-u.ac.jp/shimizu/download/download.html

// $sensor_program = "lmsensors";
// $sensor_program = "healthd";
// $sensor_program = "hwsensors";
// $sensor_program = "mbmon";
$sensor_program = "";

// show mount point
// true = display  false = do not display
$show_mount_point = true;

?>
