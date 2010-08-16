<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


function smarty_function_html_textarea($params, &$smarty)
{
	if(!isset($params['value']))
		$params['value']='';
		
	$value = isset($_POST[$params['name']]) ? ($_POST[$params['name']]) : $params['value'];
	
	if(!isset($params['extra']))
		$params['extra']='';
		
	if(!isset($params['class']))
		$params['class']='textbox';

	
	if($pos = strpos($params['name'], '['))
	{
		$key1 = substr($params['name'],0,$pos);
		$key2 = substr($params['name'],$pos+1, -1);

		$isset = isset($_POST[$key1][$key2]);
		$value = isset($_POST[$key1][$key2]) ? ($_POST[$key1][$key2]) : $params['value'];

	}else
	{
		$value = isset($_POST[$params['name']]) ? ($_POST[$params['name']]) : $params['value'];
		$isset = isset($_POST[$params['name']]);
	}
		
	if($isset && empty($value) && $params['required'])
		$params['class'].=' error';

	if(empty($value) && !empty($params['empty_text'])){
		$value = $params['empty_text'];
	}

	$html ='<textarea class="'.$params['class'].'" name="'.$params['name'].'" '.$params['extra'];
	if(!empty($params['empty_text'])){
		$html .= ' onfocus="if(this.value==\''.$params['empty_text'].'\')this.value=\'\';" onblur="if(this.value==\'\')this.value=\''.$params['empty_text'].'\';"';
	}

	$html .= '>'.$value.'</textarea>';

	if($params['required'] && ($params['required']=='true' || $params['required']=='1'))
	{
		$html .= '<input type="hidden" name="required[]" value="'.$params['name'].'" />';
	}
	if(!empty($params['empty_text'])){
		$html .= '<input type="hidden" name="empty_texts[]" value="'.$params['name'].':'.$params['empty_text'].'" />';
	}
	return $html;
	
}
?>
