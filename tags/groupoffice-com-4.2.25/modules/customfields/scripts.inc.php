<?php
//if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
//{
//	require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//	$cf = new customfields();
////	$cf2 = new customfields();
//	$cf->get_authorized_categories(6,$GLOBALS['GO_SECURITY']->user_id);
//	$file_cf_categories = array();
////	$file_cf_fields = array();
//
//	while ($record = $cf->next_record()) {
//		$cat['id'] = $record['id'];
//		$cat['name'] = $record['name'];
////		$cf2->get_fields($record['id']);
////		while ($record2 = $cf2->next_record()) {
////			$field['id'] = $record2['id'];
////			$field['name'] = $record2['name'];
////			$file_cf_fields[]=$field;
////		}
//		$file_cf_categories[] = $cat;
//	}
//	
////	$GO_SCRIPTS_JS .= "GO.customfields.file_categories = new Array(); ";
////	foreach ($file_cf_categories as $cat)
////		$GO_SCRIPTS_JS .= "GO.customfields.file_categories.push({id:".$cat['id'].",name:'".$cat['name']."'}); ";
//
////	global $GO_SECURITY;
////	$addressbook_limits = $cf->get_addressbooks_limits_array($GLOBALS['GO_SECURITY']->user_id);
////	$GO_SCRIPTS_JS .= "GO.customfields.addressbook_limits = ".json_encode($addressbook_limits)."; ";
////	$GO_SCRIPTS_JS .= "GO.customfields.file_cfields = new Array(); ";
////	foreach ($file_cf_fields as $field)
////		$GO_SCRIPTS_JS .= "GO.customfields.file_cfields['".$field['id']."'] = ({id:".$field['id'].",name:'".$field['name']."'}); ";
//
////	$folder_limits = $cf->get_folder_limits_array($GLOBALS['GO_SECURITY']->user_id);
////	$GO_SCRIPTS_JS .= "GO.customfields.folder_limits= ".json_encode($folder_limits)."; ";
////
////	$GO_SCRIPTS_JS .= "console.log(GO.customfields.addressbook_limits);
////console.log(GO.customfields.folder_limits);";
//}


$GO_SCRIPTS_JS .= '
GO.customfields.types={};
GO.customfields.columns={};
GO.customfields.columnMap={};
';


$moduleObjects =  GO::modules()->getAllModules();
foreach($moduleObjects as $moduleObject)
{	
	$file = $moduleObject->path.ucfirst($moduleObject->id).'Module.php';
	//todo load listeners
	if(file_exists($file)){
//		require_once($file);
		$class='GO_'.ucfirst($moduleObject->id).'_'.ucfirst($moduleObject->id).'Module';

		$object = new $class;
		$models = $object->findClasses("customfields/model");
		
		foreach($models as $customFieldModel){
			
			if($customFieldModel->isSubclassOf('GO_Customfields_Model_AbstractCustomFieldsRecord')){
				$name =$customFieldModel->name;
				$model = new $name;
				
				$GO_SCRIPTS_JS .=  '


				GO.customfields.columns["'.$model->extendsModel().'"]=[];
				

				GO.customfields.types["'.$model->extendsModel().'"]={
					name: "'.GO::getModel($model->extendsModel())->localizedName.'",
					panels: []
				};'."\n";
				
				$stmt = GO_Customfields_Model_Category::model()->findByModel($model->extendsModel());
				
				while($category = $stmt->fetch()){
					
					$fields = array();
					$fstmt = $category->fields();
					while($field = $fstmt->fetch()){
						$fields[]=$field->toJsonArray();
					}
                                                                            
                                        
					// Makes global, client-side, editable form panels for every customfield category
					if($category->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
						$GO_SCRIPTS_JS .= "\n\n".'GO.customfields.types["'.$model->extendsModel().'"].panels.push({xtype : "customformpanel", itemId:"cf-panel-'.$category->id.'", category_id: '.$category->id.', title : "'.htmlspecialchars($category->name,ENT_QUOTES, 'UTF-8').'", customfields : '.json_encode($fields).'});'."\n";
					
					/**
					 * Registers customfield column information in a global, client-side object, ordered by model.
					 * Also, this loop ensures that every customfield data being used has such information
					 * described in a global, client-side object, ordered by customfield id.
					 */
					foreach($fields as $field) {
						$align = $field['datatype']=='GO_Customfields_Customfieldtype_Number' || $field['datatype']=='GO_Customfields_Customfieldtype_Date' || $field['datatype']=='GO_Customfields_Customfieldtype_Datetime' ? 'right' : 'left';
            $exclude_from_grid = $field['exclude_from_grid'] || $field['datatype']=='GO_Customfields_Customfieldtype_Heading' ? 'true' : 'false';
						
						$GO_SCRIPTS_JS .= 'GO.customfields.columns["'.$model->extendsModel().'"].push({'.
								'header: "'.GO_Base_Util_String::escape_javascript($field['name']).'",'.
								'dataIndex: "'.$field['dataname'].'" ,'.
								'datatype:"'.$field['datatype'].'", '.
								'align:"'.$align.'", '.
								'sortable:true,'.
								'id: "'.$field['dataname'].'",'.
								'customfield_id: "'.$field['id'].'",'.
								//'multiselect: "'.$field['multiselect'].'",'.
                'exclude_from_grid: "'.$exclude_from_grid.'",'.
								'hidden:true});'."\n".
								'GO.customfields.columnMap["'.$field['dataname'].'"]=GO.customfields.columns["'.$model->extendsModel().'"][GO.customfields.columns["'.$model->extendsModel().'"].length-1];'."\n";
					}
				}
			}
		}
	}
}


?>