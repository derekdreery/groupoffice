<?php
require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');


function smarty_function_customfields($params, &$smarty) {
	global $co, $GO_MODULES;

	$cf = new customfields();

	$fields = $cf->get_category_fields($params['category_id']);

	for($i=0;$i<count($fields);$i++){
		
		$fieldparams=array(
			'name'=>$fields[$i]['dataname'],
			'class'=>isset($params['class']) ? $params['class'] : ''
		);

		switch($fields[$i]['datatype']){
			case 'select':

				require_once $smarty->_get_plugin_filepath('function','html_options');

				$fieldparams['options']=array();
				foreach($fields[$i]['options'] as $option){
					$fieldparams['options'][$option[0]]=$option[0];
				}

				$fieldparams['selected'] = isset($_POST[$fieldparams['name']]) ? ($_POST[$fieldparams['name']]) : $fieldparams['name'];


				$fields[$i]['html']=smarty_function_html_options($fieldparams, $smarty);
				break;
			default:
				require_once $smarty->_get_plugin_filepath('function','html_input');

				$fields[$i]['html']=smarty_function_html_input($fieldparams, $smarty);
				break;
		}		
	}

	$params['assign']=isset($params['assign']) ? $params['assign'] : 'customfields';

	$smarty->assign($params['assign'], $fields);
}
