<?php
echo "Converting existing categories from user to calendar\n";

//require_once('../../../../GO.php');

GO::$ignoreAclPermissions=true;

$oldCategories = array();

$stmt = GO_Calendar_Model_Category::model()->find();
while($category = $stmt->fetch()){
	
	if($category->calendar_id != 0){
		$oldCategories[] = $category->id;

		echo "Category $category->name\n";

		$calStmt = GO_Calendar_Model_Calendar::model()->findByAttribute('user_id', $category->calendar_id);
		while($calendar = $calStmt->fetch()){

			try{
				// Create the new categories for each calendar
				$newCategory = new GO_Calendar_Model_Category();
				$newCategory->name = $category->name;
				$newCategory->color = $category->color;
				$newCategory->calendar_id = $calendar->id;
				$newCategory->save();

				// Get all events that have the old category and change the category to the new one.
				$eventStmt = GO_Calendar_Model_Event::model()->findByAttributes(array('calendar_id'=>$calendar->id,'category_id'=>$category->id));
				while($event = $eventStmt->fetch()){
					//echo "Update event $event->name\n";
					$event->category_id = $newCategory->id;
					$event->save();
				}
			}catch(Exception $e){
				echo $e->getMessage()."\n";
			}
		}
	}
}

echo "Done creating new categories\n\n";
echo "Remove old categories\n\n";

foreach($oldCategories as $oldCat){
	$cat = GO_Calendar_Model_Category::model()->findByPk($oldCat);
	
	if($cat)
		$cat->delete();
}

echo "Done\n\n";