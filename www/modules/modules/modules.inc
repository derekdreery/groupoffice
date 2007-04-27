<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/



echo '<br />';
if (isset($feedback))
{
	echo $feedback;
}

echo '<h1>'.$menu_modules.'</h1>'.$admin_modules_text.'<br /><br />';

echo '<table width="100%"><tr><td valign="top">';
echo '<table border="0" class="go_table"><tr><th colspan="2">'.$mod_available.'</th></tr>';

$column_count = 0;

$modules = $fs->get_folders($GO_CONFIG->module_path);
$module_count = count($modules);
while($module = array_shift($modules))
{
	$GO_THEME->load_module_theme($module['name']);
	if (!$sql_module = $GO_MODULES->get_module($module['name']))
	{
		$version = '';
		$sort_order = '1000';

		//require language file to obtain module name in the right language
		$language_file = $GO_LANGUAGE->get_language_file($module['name']);
		if(file_exists($language_file))
		{
			require_once($language_file);
		}
		$lang_var = isset($lang_modules[$module['name']]) ? $lang_modules[$module['name']] : $module['name'];


		$info_file = $module['path'].'/module.info';
		$module_info = $GO_MODULES->get_module_info($module['name']);

		$version = $module_info['version'];
		$sort_order = isset($module_info['sort_order']) ? $module_info['sort_order'] : '1000';
		$admin_menu = isset($module_info['admin_menu']) ? $module_info['admin_menu'] : '0';

		if ($module_info)
		{
			$tooltip = '<table border="0">'.
			'<tr><td valign="top">'.$strDescription.':</td><td>'.text_to_html(trim(htmlspecialchars($module_info['description']))).'</td></tr>'.
			'<tr><td>'.$strVersion.':</td><td>'.trim($module_info['version']).'</td></tr>'.
			'<tr><td>'.$strStatus.':</td><td>'.trim($module_info['status']).'</td></tr>'.
			'<tr><td>'.$strReleaseDate.':</td><td>'.db_date_to_date(trim($module_info['release_date'])).'</td></tr>'.
			'<tr><td valign="top">'.$strAuthors.':</td><td>';
			while ($author = array_shift($module_info['authors']))
			{
				$tooltip .= trim(htmlspecialchars($author['name']).' &lt;'.mail_to($author['email']).'&gt;').'<br />';
			}
			$tooltip .= '</td></tr></table>';
		}else
		{
			$tooltip = $strNoModuleInfo;
		}

		echo '<tr>';

		$GO_THEME->images[$module['name']] = isset($GO_THEME->images[$module['name']]) ? $GO_THEME->images[$module['name']] : $GO_THEME->images['unknown'];

		$cell = new table_cell('<img style="margin-right: 5px;" src="'.$GO_THEME->images[$module['name']].'" border="0" width="32" height="32" align="absmiddle" />'.$lang_var);
		$cell->set_tooltip(new tooltip($tooltip));
		echo $cell->get_html();
		echo '<td align="right">';

		$button = new button($cmdInstall, "javascript:install('".$module['name']."', '".trim($version)."', '".$sort_order."', '".$admin_menu."');");
		echo $button->get_html();
		echo '</td></tr>';
	}
}
echo '</table>';

echo '</td><td valign="top">';

echo '<table class="go_table">'.
'<tr><th>'.$mod_installed.'</th>'.
'<th>'.$mod_sort_order.'</th>'.
'<th>'.$menu_admin.'</th>'.
'<th colspan="3">&nbsp;</th></tr>';

$column_count = 0;

$modules = $GO_MODULES->get_modules_with_locations();
$module_count = count($modules);
while($module = array_shift($modules))
{

	//require language file to obtain module name in the right language
	$language_file = $GO_LANGUAGE->get_language_file($module['id']);
	if(file_exists($language_file))
	{
		require_once($language_file);
	}
	$lang_var = isset($lang_modules[$module['id']]) ? $lang_modules[$module['id']] : $module['id'];


	$info_file = $module['path'].'/module.info';
	$module_info = $GO_MODULES->get_module_info($module['id']);
	$required = (isset($module_info['required']) && $module_info['required']);

	if ($module_info)
	{
		$tooltip = '<table border="0">'.
		'<tr><td valign="top">'.$strDescription.':</td><td>'.text_to_html(trim(htmlspecialchars($module_info['description']))).'</td></tr>'.
		'<tr><td>'.$strVersion.':</td><td>'.trim($module_info['version']).'</td></tr>'.
		'<tr><td>'.$strStatus.':</td><td>'.trim($module_info['status']).'</td></tr>'.
		'<tr><td>'.$strReleaseDate.':</td><td>'.db_date_to_date(trim($module_info['release_date'])).'</td></tr>'.
		'<tr><td valign="top">'.$strAuthors.':</td><td>';
		while ($author = array_shift($module_info['authors']))
		{
			$tooltip .= trim(htmlspecialchars($author['name']).' &lt;'.mail_to($author['email']).'&gt;').'<br />';
		}
		$tooltip .= '</td></tr></table>';
	}else
	{
		$tooltip = $strNoModuleInfo;
	}

	echo '<tr>';

	$GO_THEME->images[$module['id']] = isset($GO_THEME->images[$module['id']]) ? $GO_THEME->images[$module['id']] : $GO_THEME->images['unknown'];

	$cell = new table_cell('<img style="margin-right: 5px;" src="'.$GO_THEME->images[$module['id']].'" border="0" width="32" height="32" align="absmiddle" />'.$lang_var);
	$cell->set_tooltip(new tooltip($tooltip));
	echo $cell->get_html();



	echo '<td><input type="text" size="3" class="textbox" name="modules['.$module['id'].'][sort_order]" value="'.$module['sort_order'].'" /></td>'.
	'<td align="center">';
	$checkbox = new checkbox($module['id'], 'modules['.$module['id'].'][admin_menu]','1', '', ($module['admin_menu'] == '1'));
	echo $checkbox->get_html();
	echo '</td>'.
	'<td>';


	$button = new button($strPermissions, "javascript:popup('module_permissions.php?module_id=".
	urlencode($module['id'])."','450','450')");
	echo $button->get_html();
	echo '</td><td>';

	if(!$required)
	{
		$button = new button($cmdUninstall, "javascript:uninstall('".$module['id']."', '".$lang_var."');");
		echo $button->get_html();
	}
	echo '</td><td>';

	if ( isset( $GO_MODULES->modules[$module['id']] ) ) {
		$classfile = $GO_MODULES->modules[$module['id']]['class_path'].
		$module['id'].".class.inc";
		if ( file_exists( $classfile ) ) {
			require_once( $classfile );
			$mod = new $module['id'];
			if ( method_exists( $mod, 'consistencycheck' ) ) {
				$button = new button($strConsistencyCheck, "javascript:consistencycheck('".$module['id']."');");
				echo $button->get_html();
			}
		}
	}
	echo '</td></tr>';
}
echo '</table>';

if($module_count)
{
	$button = new button($cmdSave, "document.forms[0].task.value='sort_order';javascript:document.forms[0].submit()");
	echo $button->get_html();
}

echo '</td></tr></table>';
?>
<script type="text/javascript">
function uninstall(module_id, module_name)
{
	if (confirm("<?php echo $strConfirmUninstallPrefix; ?> '"+module_name+"' <?php echo $strConfirmUninstallSuffix; ?>"))
	{
		document.forms[0].task.value = 'uninstall';
		document.forms[0].module_id.value = module_id;
		document.forms[0].submit();
	}
}

function install(module_id, version, sort_order, admin_menu)
{
	document.forms[0].admin_menu.value = admin_menu;
	document.forms[0].sort_order.value = sort_order;
	document.forms[0].version.value = version;
	document.forms[0].task.value = 'install';
	document.forms[0].module_id.value = module_id;
	document.forms[0].submit();
}

function consistencycheck(module_id)
{
	document.forms[0].task.value = 'consistencycheck';
	document.forms[0].module_id.value = module_id;
	document.forms[0].submit();
}
</script>
