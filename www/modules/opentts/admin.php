<?php
/************************************************************************/
/* TTS: Ticket tracking system                                          */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2002 by Meir Michanie                                  */
/* http://www.riunx.com                                                 */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
require_once("../../Group-Office.php");
require_once($GO_LANGUAGE->get_language_file('opentts'));
//authenticate the user
//if $GO_SECURITY->authenticate(true); is used the user needs admin permissons

$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('opentts');

//set the page title for the header file
$page_title = "Opentts";

require_once($GO_THEME->theme_path."header.inc");


$tts= new db();
require_once("classes.php");


require_once("menu.php");
$func='';
if (isset($_POST['func'])){$func=$_POST['func'];};
if (isset($_GET['func'])){$func=$_GET['func'];};
$button= new button();
$button_left=$button->get_button($cmdAdd,'addItems(this.form.AvailItems,this.form.AvailValue, this.form.SelItems);');
$button_right=$button->get_button($cmdDelete,'addItemToTextBox(this.form.SelItems,this.form.AvailValue,this.form.AvailItems);removeItems(this.form.SelItems);');
$button_submit=$button->get_button($cmdOk,"javascript:this.form.Sel.value = makeStringFromSelect(this.form.SelItems); this.form.submit();"); 
$button_reset=$button->get_button($cmdReset,"javascript:this.form.reset();");
$tabtable = new tabtable('admin_tabtable', $helpdesk_title_admin , '100%', '400');
#$tabtable->add_tab('welcome', $helpdesk_menu_administration);
$tabtable->add_tab('categories', $helpdesk_menu_categories);
$tabtable->add_tab('agents', $helpdesk_menu_groups);
$tabtable->add_tab('permissions', $helpdesk_menu_permissions);
$tabtable->add_tab('priorities', $helpdesk_menu_priorities);
$tabtable->add_tab('projects', $helpdesk_menu_projects);
$tabtable->add_tab('status', $helpdesk_menu_status);
#$tabtable->add_tab('edit_globals', $helpdesk_menu_globals);

$tabtable->print_head();
$admin_tabtable=$tabtable->active_tab;
if(Security::is_action_allowed("admin")){
switch($tabtable->get_active_tab_id())
{
  case 'welcome':
	echo '<h1>'.$name.'</h1>';
break;
                                                                                
	case 'permissions':
	if ($func=='mod_permissions') mod_permissions($_POST['Sel'],$_POST['dest_group']);
	$button_submit=$button->get_button($cmdOk,"this.form.Sel.value = makeStringFromSelect(this.form.SelItems); this.form.submit();");
	$button_left=$button->get_button($cmdAdd,"javascript:addItems(this.form.AvailItems, this.form.SelItems);");
	$button_right=$button->get_button($cmdDelete,"javascript:removeItems(this.form.SelItems);");
	edit_permissions();
break;
	case 'categories':
	if ($func=='update_categories') update_categories($_POST['Sel']);
	edit_categories();
break;
case 'agents':
	if ($func=='update_agents') update_agents($_POST['Sel'],$_POST['dest_group']);
	$button_submit=$button->get_button($cmdOk,"this.form.Sel.value = makeStringFromSelect(this.form.SelItems); this.form.submit();");
	$button_left=$button->get_button($cmdAdd,"javascript:addItems(this.form.AvailItems, this.form.SelItems);");
	$button_right=$button->get_button($cmdDelete,"javascript:removeItems(this.form.SelItems);");
	echo '<table width=100%><tr>';
	echo '<td>'.edit_agents().'</td></tr></table>';
break;
case 'projects':
	if ($func=='update_projects') update_projects($_POST['Sel']);
   edit_projects();                                                                                                               
break;
case 'priorities':
	if ($func=='update_priorities') update_priorities($_POST['Sel']);
	edit_priorities();                                                                                             
break;
                                                                                                  
case 'status':
	if ($func=='update_status')  update_status($_POST['Sel']);
	edit_status();
break;

case 'edit_globals':
	if ($func=='mod_globals')  mod_globals($_POST['mod_varname'],$_POST['mod_definition'],$_POST['mod_action']);
	edit_globals();
break;
}
}
$tabtable->print_foot();

function edit_agents(){
        global $admin_tabtable, $prefix,$hlpdsk_prefix,$name,$hlpdsk_theme,$nuke_user_id_fieldname,$nuke_username_fieldname,$action,$button_submit,$button_left,$button_right,$GO_THEME,$GO_LANGUAGE,$strUser,$strGroups;   
        require_once($GO_LANGUAGE->get_language_file('opentts'));

        $total_actions=10;
        $dbarray_group=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_groups","gid","group_name"," order by gid");
        $total_groups=count($dbarray_group);
        $options_actions=Common::fill_options("users","$nuke_user_id_fieldname","$nuke_username_fieldname",""," order by $nuke_username_fieldname");
        $options_groups=Common::fill_options("{$prefix}{$hlpdsk_prefix}_groups","gid","group_name",""," order by gid");
        $i=0;
        foreach($dbarray_group as $key=>$value){
                $array_gids[$i]=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_groups_members,users","uid","$nuke_username_fieldname"," and $nuke_user_id_fieldname=uid and  gid=$key order by uid");
                $i++;
        }
        $array_groups="\n";
        for ($i=0;$i<$total_groups;$i++){
                $temp_array=$array_gids[$i];
                if (isset($temp_array)){
                        $array_groups.="\tnew Array(\n ";
                        foreach($temp_array as $key=>$value){
                                $array_groups.="\t\tnew Array(\"$value\",\"$key\")\n ,";
                        }
                        $array_groups=substr($array_groups,0,-1);
                        $array_groups.="\n\t) ,\n";
                }else{
                        $array_groups.="\tnull\n ,";
                }
        }
        $array_groups=substr($array_groups,0,-2);
        $file="themes/$hlpdsk_theme/admin_agents.html";
        $file=addslashes (implode("",file($file)));
        eval("\$content=stripslashes(\" $file\");");
        echo $content;
}
function update_agents ($Sel,$dest_group){
        global $admin_tabtable, $prefix,$hlpdsk_prefix,$tts;
        $Sel=split(",",$Sel);
        $query_del="delete from {$prefix}{$hlpdsk_prefix}_groups_members where gid=$dest_group";
	$tts->query($query_del);
        foreach($Sel as $key=>$value){
                if ($value<>""){
                        $query_ins= "insert into {$prefix}{$hlpdsk_prefix}_groups_members (gid,uid,uid_default) values ('$dest_group','$value','')";
			$tts->query($query_ins);
                }
        }

}



function edit_projects(){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$name,$hlpdsk_theme,$button_submit,$button_reset,$button_left,$button_right,$GO_THEME,$GO_LANGUAGE; 
        require_once($GO_LANGUAGE->get_language_file('opentts'));
	$Available_Items=$tts_lang_project;
	$Selected_Items=$helpdesk_menu_projects;
        $options=Common::fill_options("{$prefix}{$hlpdsk_prefix}_projects","project_id","project_name",""," order by project_id");
        $form_action_func="update_projects";
        $file="themes/$hlpdsk_theme/admin_select.html";
$file=addslashes (implode("",file($file)));
eval("\$content=stripslashes(\" $file\");");
echo $content;

}

function update_projects($Sel){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$tts;
        $Sel=split(",",$Sel);
        $query_del="delete from {$prefix}{$hlpdsk_prefix}_projects";
        $tts->query($query_del);
        foreach($Sel as $key=>$value){
                if ($value<>""){
                        $project_id=substr($value,0,strpos($value,"=>"));
                        $project_name=substr($value,strpos($value,"=>") + 2  );
                        $query_ins= "insert into {$prefix}{$hlpdsk_prefix}_projects (project_id,project_name) values ('$project_id','$project_name')";
                        $tts->query($query_ins);
                }
        }
}

function edit_status(){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$name,$hlpdsk_theme,$button_submit,$button_reset,$button_left,$button_right,$GO_THEME,$GO_LANGUAGE;   
        require_once($GO_LANGUAGE->get_language_file('opentts'));
        $Available_Items=$tts_lang_status;
        $Selected_Items=$tts_lang_status;

        $options=Common::fill_options("{$prefix}{$hlpdsk_prefix}_status","status_id","status_name",""," order by status_id");
        $form_action_func="update_status";
        $file="themes/$hlpdsk_theme/admin_select.html";
$file=addslashes (implode("",file($file)));
eval("\$content=stripslashes(\" $file\");");
echo $content;

}

function update_status($Sel){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$tts;
        $Sel=split(",",$Sel);
        $query_del="delete from {$prefix}{$hlpdsk_prefix}_status";
        $tts->query($query_del);
        foreach($Sel as $key=>$value){
                if ($value<>""){
                        $status_id=substr($value,0,strpos($value,"=>"));
                        $status_name=substr($value,strpos($value,"=>") + 2  );
                        $query_ins= "insert into {$prefix}{$hlpdsk_prefix}_status (status_id,status_name) values ('$status_id','$status_name')";
                        $tts->query($query_ins);
                }
        }
}


function edit_categories(){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$name,$hlpdsk_theme,$button_submit,$button_reset,$button_left,$button_right,$GO_THEME,$GO_LANGUAGE;   
        require_once($GO_LANGUAGE->get_language_file('opentts'));
        $Available_Items=$tts_lang_category;
        $Selected_Items=$helpdesk_menu_categories;

        $options=Common::fill_options("{$prefix}{$hlpdsk_prefix}_categories","category_id","category_name",""," order by category_id");
$form_action_func="update_categories";
        $file="themes/$hlpdsk_theme/admin_select.html";
$file=addslashes (implode("",file($file)));
eval("\$content=stripslashes(\" $file\");");
echo $content;

}

function update_categories($Sel){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$tts;
        $Sel=split(",",$Sel);
        $query_del="delete from {$prefix}{$hlpdsk_prefix}_categories";
        $tts->query($query_del);
        foreach($Sel as $key=>$value){
                if ($value<>""){
			$category_id=substr($value,0,strpos($value,"=>"));
			$category_name=substr($value,strpos($value,"=>") + 2  );
                        $query_ins= "insert into {$prefix}{$hlpdsk_prefix}_categories (category_id,category_name) values ('$category_id','$category_name')";
                        $tts->query($query_ins);
                }
        }
}

function edit_priorities(){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$name,$hlpdsk_theme,$button_submit,$button_reset,$button_left,$button_right,$GO_THEME,$GO_LANGUAGE;   
        require_once($GO_LANGUAGE->get_language_file('opentts'));
        $Available_Items=$tts_lang_priority;
        $Selected_Items=$helpdesk_menu_priorities;

        $options=Common::fill_options("{$prefix}{$hlpdsk_prefix}_priorities","priority_id","priority_name",""," order by priority_id");
$form_action_func="update_priorities";
        $file="themes/$hlpdsk_theme/admin_select.html";

$file=addslashes (implode("",file($file)));
eval("\$content=stripslashes(\" $file\");");
echo $content;

}

function update_priorities($Sel){
global $admin_tabtable, $prefix,$hlpdsk_prefix,$tts;
        $Sel=split(",",$Sel);
        $query_del="delete from {$prefix}{$hlpdsk_prefix}_priorities";
        $tts->query($query_del);
        foreach($Sel as $key=>$value){
                if ($value<>""){
                        $priority_id=substr($value,0,strpos($value,"=>"));
                        $priority_name=substr($value,strpos($value,"=>") + 2  );
                        $query_ins= "insert into {$prefix}{$hlpdsk_prefix}_priorities (priority_id,priority_name) values ('$priority_id','$priority_name')";
                        $tts->query($query_ins);
                }
        }
}


function edit_permissions(){
        global $admin_tabtable, $prefix,$hlpdsk_prefix,$name,$hlpdsk_theme,$action,$button_submit,$button_left,$button_right,$GO_THEME,$GO_LANGUAGE;
        require_once($GO_LANGUAGE->get_language_file('opentts'));
        $total_actions=count($action);
        $dbarray_group=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_groups","gid","group_name"," order by gid");
        $total_groups=count($dbarray_group);
        $options_actions=Common::array_fill_options($action);
        $options_groups=Common::fill_options("{$prefix}{$hlpdsk_prefix}_groups","gid","group_name","",$order=" order by gid");
        $i=0;
        foreach($dbarray_group as $key=>$value){
                $array_gids[$i]=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_permissions","action_id","gid"," and  gid=$key order by action_id");
                $i++;
        }
        $array_groups="\n";
        for ($i=0;$i<$total_groups;$i++){
                $temp_array=$array_gids[$i];
                if (isset($temp_array)){
                        $array_groups.="\tnew Array(\n ";
                        foreach($temp_array as $key=>$value){
                                $array_groups.="\t\tnew Array(\"{$action[$key]}\",\"$key\")\n ,";
                        }
                        $array_groups=substr($array_groups,0,-1);
                        $array_groups.="\n\t) ,\n";
                }else{
                        $array_groups.="\tnull\n ,";
                }
        }
        $array_groups=substr($array_groups,0,-2);
        $file="themes/$hlpdsk_theme/admin_perm_groups.html";
$file=addslashes (implode("",file($file)));
eval("\$content=stripslashes(\" $file\");");
echo $content;
}

function mod_permissions($Sel,$dest_group){
        global $admin_tabtable, $prefix,$hlpdsk_prefix,$dbi,$tts;
        $Sel=split(",",$Sel);
        $query_del="delete from {$prefix}{$hlpdsk_prefix}_permissions where gid=$dest_group";
	$tts->query($query_del);
        foreach($Sel as $key=>$value){
                if ($value<>""){
                        $query_ins= "insert into {$prefix}{$hlpdsk_prefix}_permissions (gid,action_id,description) values ('$dest_group','$value','')";
			$tts->query($query_ins);
                }
        }
}

function mod_gid($mod_uid,$mod_gid,$mod_action){
        Opentts::mod_gid($mod_uid,$mod_gid,$mod_action);
	edit_agents();
}
function edit_globals(){
        global $admin_tabtable, $prefix,$hlpdsk_prefix;
        echo Tts_sql::show_query(
        "varname,definition",
        "{$prefix}{$hlpdsk_prefix}_config"
        ," order by varname",
        "Edit Globals");
        echo "<br>";
        echo Opentts::mod_globals();
}
function mod_globals($mod_varname,$mod_definition,$mod_action){
	Opentts::mod_globals($mod_varname,$mod_definition,$mod_action);
}
?>
