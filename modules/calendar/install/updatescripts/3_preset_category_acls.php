<?php
$stmt = \GO\Calendar\Model\Category::model()->find(
	\GO\Base\Db\FindParams::newInstance()->ignoreAcl()
);
foreach ($stmt as $categoryModel) {
	$aclModel = $categoryModel->setNewAcl();
	$aclModel->addGroup(2, \GO\Base\Model\Acl::WRITE_PERMISSION); // Give 'everybody' group (id: 2) permission.
	$aclModel->save();
	$categoryModel->save();
}
?>