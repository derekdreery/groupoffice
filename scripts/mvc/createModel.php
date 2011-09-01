<?php
if(isset($_POST['createModelForm']))
{
	// Create the model
	
	$table = $_POST['tableName'];
	$className = $_POST['modelName'];
	$moduleName = $_POST['moduleName'];
	
	include "generateModel.php";
	
	generateModel($className, $table, $moduleName);
	
}
else
{
?>

<form method="POST" id="createModelForm" target="">
	Module Name: <input type="text" name="moduleName" id="moduleName" /><br />
	Model Name: <input type="text" name="modelName" id="modelName" /><br />
	Model Table:<input type="text" name="tableName" id="tableName" /><br />
	<input type="submit" name="createModelForm" id="createModelForm" value="Create_Model" />
</form>

<?php
}
?>