<?php
if(isset($_POST['createController']))
{
	// Create the controller
	
	$moduleName = $_POST['moduleName'];
	$modelName = $_POST['modelName'];
	
	// TODO: Check for existing module
	// TODO: Check for model exists
		
	generateController($moduleName, $modelName);
	
}
else
{
?>

<form method="POST" id="createControllerForm" target="">
	Module Name: <input type="text" name="moduleName" id="moduleName" /><br />
	Model Name: <input type="text" name="modelName" id="modelName" /><br />
	<input type="submit" name="createController" id="createController" value="createController" />
</form>

<?php
}

function generateController($moduleName, $modelName) {
	
	$date = date('Y-m-d H:i:s');
	$className = 'GO_'.$moduleName.'_Controller_'.$modelName;
	$fullModelName = 'GO_'.$moduleName.'_Model_'.$modelName;

	echo '<pre>'.htmlspecialchars('<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: '.$className.'.php 7607 '.$date.'Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */  

/**
 * The '.$className.' controller
 *
 */

class '.$className.' extends GO_Base_Controller_AbstractModelController {

	protected $model = \''.$fullModelName.'\';


}
').'</pre>';
}

?>