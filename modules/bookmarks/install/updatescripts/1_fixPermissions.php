<?php
$stmt = GO_Bookmarks_Model_Category::model()->findByAttribute('acl_id', 0);
while($category=$stmt->fetch()){
	$category->setNewAcl($category->user_id);
	$category->save();
}