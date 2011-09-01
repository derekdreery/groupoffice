<?php
if(isset($_POST['createCFModelForm']))
{
	// Create the model
	
	$extendclassName = $_POST['modelExtendName'];
	$moduleName = $_POST['moduleName'];
	
	include "generateCFModel.php";
	
	generateCFModel($extendclassName, $moduleName);
	
}
else
{
?>

<form method="POST" id="createModelForm" target="">
	Module Name: <input type="text" name="moduleName" id="moduleName" /><br />
	Model Name: <input type="text" name="modelExtendName" id="modelName" /><br />
	<input type="submit" name="createCFModelForm" id="createCFModelForm" value="Create_CF_Model" />
</form>

<?php
}
?>