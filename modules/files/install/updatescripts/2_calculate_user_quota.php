<?php
// use /files/file/eecalculateDiskUsage for manual execution or recalculation
$users = GO_Base_Model_User::model()->find();
foreach($users as $user) {
	$user->calculatedDiskUsage()->save();
}