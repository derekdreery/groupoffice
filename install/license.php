<?php
require('header.php');
if($_SERVER['REQUEST_METHOD']=='POST'){
	if(GO_Base_Html_Error::checkRequired())
		redirect("configFile.php");
}


printHead();

?>
<h1>License terms</h1>
<p>The following license applies to this product:</p>
<div class="cmd">
<?php
echo GO_Base_Util_String::text_to_html(file_get_contents('../LICENSE.TXT'));
?>
</div>

<?php
GO_Base_Html_Checkbox::render(array(
		'required'=>true,
		'name'=>'agree',
		'value'=>1,
		'label'=>'I agree to the terms of the above license.'
		));

continueButton();
printFoot();