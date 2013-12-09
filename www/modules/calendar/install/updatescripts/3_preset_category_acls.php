<?php
$stmt = \GO_Calendar_Model_Category::model()->find(
	\GO_Base_Db_FindParams::newInstance()->ignoreAcl()
);
foreach ($stmt as $categoryModel) {
	$aclModel = $categoryModel->setNewAcl();
	$aclModel->addGroup(2, \GO_Base_Model_Acl::WRITE_PERMISSION); // Give 'everybody' group (id: 2) permission.
	$aclModel->save();
	$categoryModel->save();
}
?>