<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: modulegenerator.class.inc.php 1870 2008-05-05 15:14:37Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class modulegenerator extends db
{
	var $module;
	
	var $main_panel_replacements = array();
	
	public function test_sub_tag()
	{
		$data = '
		
		<gotpl if="$test">test zonder sub</gotpl>
		
		pipo
		
		<gotpl if="$test">blaat
		
		<gotpl if="$test2>
		blaat2
		</gotpl>
		
		sdasaca</gotpl>
		sdfdsfsdfsd"';
		
		$this->test_parse($data);
	}
	
	public function test_parse($content)
	{		
		while($tag = $this->get_tag('gotpl', $content))
		{
			var_dump($tag);
			
			$tagcontent = preg_replace("'<gotpl[^>]*>'si",'', substr($tag,0, strlen($tag)-8),1);
			$this->test_parse($tagcontent);
			
			$content = str_replace($tag, '', $content);
			
		}		
	}
	
	private function get_tag($tag, $content) {
		$start_pos = strpos($content, '<'.$tag);
		if ($start_pos !== false) {
			$end_pos = $this->get_end_pos($tag, $content, $start_pos);
			$sub_start_pos = 	strpos($content, '<'.$tag, $start_pos+strlen('<'.$tag));
			
			if($sub_start_pos!== false)
			{
				$sub_end_pos = $end_pos;
				
				//echo $sub_start_pos.' < '.$sub_end_pos."\n---\n";
				
				while($sub_start_pos<$sub_end_pos)
				{
					$sub_end_pos = $this->get_end_pos($tag, $content, $sub_end_pos);
					$sub_start_pos = 	strpos($content, '<'.$tag, $sub_start_pos+strlen('<'.$tag));
					
					if($sub_end_pos)
						$end_pos = $sub_end_pos;
				}	
			}
			if($end_pos === false)
			{
				return false;
			}
			$tag_length = $end_pos-$start_pos;
			return substr($content, $start_pos, $tag_length);
		}
		return false;
	}
	
	
	private function get_end_pos($tag, $content, $offset=0)
	{
		$end_pos = strpos($content, '</'.$tag.'>', $offset);
		if($end_pos!==false)
		{
			$end_pos+=strlen('</'.$tag.'>');
		}
		return $end_pos;		
	}

	private function get_attributes($tag) {
		$attributes = array ();
		$in_value = false;
		$in_name = false;
		$name = '';
		$value = '';
		$length = strlen($tag);
		
		$exit=false;
		
		for ($i = 0; $i < $length; $i ++) {
			
			if($exit)
			{
				break;
			}
			$char = $tag[$i];
			switch ($char) {
				case '"' :
					if ($in_value) {
						$in_value = false;

						$attributes[trim($name)] = trim($value);
						$name = '';
						$value = '';
					} else {
						$in_value = true;
					}

					break;

				case ' ' :
					if (!$in_value) {
						$in_name = true;
					} else {
						$value .= $char;
					}
					break;

				case '=' :
					$in_name = false;
					if ($in_value) {
						$value .= $char;
					}
					break;

				default :
					if ($in_name) {
						$name .= $char;
					}

					if ($in_value) {
						$value .= $char;
					}
					break;
					
				case '>':
					$exit=true;
					
					break;
			}
		}
		return $attributes;
	}

	
	private function replace_template(&$content, $replacements=array())
	{
		$replacements['prefix']=$this->prefix;
		$replacements['module']=$this->module;
		
		foreach($replacements as $key=>$value)
		{
			if(!empty($key))
			{
				$content = str_replace('{'.$key.'}', $value, $content);
			}
		}
	}
	
	private function parse_tags(&$content, $conditions)
	{
		//var_dump($conditions);
		foreach($conditions as $varname=>$value)
		{
			$$varname = $value;
		}
		
		while ($tag = $this->get_tag('gotpl', $content)) {

			$attributes = $this->get_attributes($tag);
			
			$print = false;
			//echo '$print = '.$attributes['if'].';'."\n\n";
			eval('$print = '.html_entity_decode($attributes['if']).';');
			//var_dump($print);
			if($print)
			{
				//var_dump($conditions);
				$tagcontent = preg_replace("'<gotpl[^>]*>'si",'', substr($tag,0, strlen($tag)-8),1);	
				$this->parse_tags($tagcontent, $conditions);

				
			}else
			{
				$tagcontent = '';
			}
			
		//	echo $tagcontent;
			
			//echo "\n\n########\n\n";
			
			$content = str_replace($tag, $tagcontent, $content);
		}
		
	}
	
	private function get_template_content($template, $conditions=array())
	{		
		$content = file_get_contents(dirname(dirname(__FILE__)).'/templates/'.$template);	
		
		$this->parse_tags($content, $conditions);
		
		
		$lines = explode("\n", $content);
		
		$content='';		
		foreach($lines as $line)
		{
			$test = trim($line);
			if(!empty($test))
			{
				$content .= $line."\n";
			}
		}
		
		return $content;		
	}
	
	public function create_file($filename, $template, $replacements=array(), $conditions=array())
	{
		if(file_exists($filename))
		{
			echo "Skipped file '.$filename.' becuase it already exists\n";
		}else
		{
			$conditions['replacements']=$replacements;
			
			$content = $this->get_template_content($template, $conditions);
			$this->replace_template($content, $replacements);
			
			file_put_contents($filename, $content);
		}		
	}
	
	public function create_module($module, $prefix, $main_template, $tables)
	{
		global $GO_CONFIG;
		
		$this->module=$module;
		$this->prefix=$prefix;
		$this->main_template=$main_template;
		$this->tables=$tables;
		
		
		$this->module_dir = $GLOBALS['GO_CONFIG']->root_path.'modules/'.$module.'/';
		
		mkdir($this->module_dir);
		mkdir($this->module_dir.'classes/');
		mkdir($this->module_dir.'install/');
		mkdir($this->module_dir.'language/');
		mkdir($this->module_dir.'themes/');
		mkdir($this->module_dir.'themes/Default');
		mkdir($this->module_dir.'themes/Default/images');
		
		touch($this->module_dir.'install/install.sql');
		touch($this->module_dir.'install/uninstall.sql');
		touch($this->module_dir.'install/install.inc.php');
		touch($this->module_dir.'install/uninstall.inc.php');
	
		if(!file_exists($this->module_dir.'themes/Default/images/'.$this->module.'.png'))
		{
			copy($GLOBALS['GO_CONFIG']->root_path.'themes/Default/images/16x16/icon-help.png', $this->module_dir.'themes/Default/images/'.$this->module.'.png');
		}

		$this->create_file($this->module_dir.'classes/'.$this->module.'.class.inc.php', 'class.tpl');		
		$this->create_file($this->module_dir.'language/en.inc.php', 'lang.tpl');
		$this->create_file($this->module_dir.'language/en.js', 'jslang.tpl');
		
		$this->create_file($this->module_dir.'themes/Default/style.css', 'style.tpl');
		
		$this->create_file($this->module_dir.'json.php', 'json.tpl');
		$this->create_file($this->module_dir.'action.php', 'action.tpl');
		$this->create_file($this->module_dir.'scripts.txt', 'scripts.tpl');
		//$this->create_file($this->module_dir.'namespaces.js', 'namespaces.tpl');
		
		
		if(!file_exists($this->module_dir.'scripts.inc.php'))
		{
			file_put_contents($this->module_dir.'scripts.inc.php', "<?php\nrequire(".'$GLOBALS['GO_LANGUAGE']->get_language_file(\''.$this->module.'\')'.");\n\n");
		}
		
		
		$this->create_file($this->module_dir.'MainPanel.js', $this->main_template);
		
		$this->process_tables();
		

		$main_content=file_get_contents($this->module_dir.'MainPanel.js');		
		$this->replace_template($main_content, $this->main_panel_replacements);
		
		file_put_contents($this->module_dir.'MainPanel.js', $main_content);
		
	}
	
	function get_jslang_var($field_name)
	{
		//file_put_contents($this->module_dir.'language/en.js', "/* $field_name */\n", FILE_APPEND);
		switch($field_name)
		{
			case'user_id':
				return 'GO.lang.strOwner';
			break;
	
			case 'ctime':
				return 'GO.lang.strCtime';
				break;
	
			case 'mtime':
				return 'GO.lang.strMtime';
				break;
			case 'name':
				return 'GO.lang.strName';
				break;
			case 'description':
				return 'GO.lang.strDescription';
				break;
	
			default:
				
				$varname = 'GO.'.$this->module.'.lang.'.$this->capitalize_underscore($field_name);
				
				$language=$varname.'="'.$this->var_to_lang($field_name).'";'."\n";				
				file_put_contents($this->module_dir.'language/en.js', $language, FILE_APPEND);
				
				return $varname;
				break;
	
		}
	}
	
	function ucfirst($string)
	{
		$string = ucfirst($string);
		
		while($pos = strpos($string,'_'))
		{
			$first_part = substr($string, 0, $pos);
			$last_part = ucfirst(substr($string, $pos+1));
			
			$string = $first_part.$last_part;
		}
		return $string;
	}
	
	function capitalize_underscore($string)
	{
		while($pos = strpos($string,'_'))
		{
			$first_part = substr($string, 0, $pos);
			$last_part = ucfirst(substr($string, $pos+1));
			
			$string = $first_part.$last_part;
		}
		return $string;
	}
	
	function process_tables()
	{
		
		$hidden_fields = array(
			'id',
			'acl_id'
		);
		
		
		
		foreach($this->tables as $table)
		{

			$replacements=array();
			$replacements['friendly_single']=$table['friendly_single'];
			$replacements['friendly_single_ucfirst']=$this->ucfirst($table['friendly_single']);
			$replacements['friendly_multiple']=$table['friendly_multiple'];
			$replacements['friendly_multiple_ucfirst']=$this->ucfirst($table['friendly_multiple']);
			
			$replacements['friendly_single_js']=$this->capitalize_underscore($table['friendly_single']);
			$replacements['friendly_multiple_js']=$this->capitalize_underscore($table['friendly_multiple']);
			
			if(isset($table['link_type']))
			{
				$replacements['link_type']=$table['link_type'];
			}else
			{
				$replacements['link_type']=0;
			}
			if(isset($table['relation']))
			{
				$replacements['related_friendly_single']=$table['relation']['remote_table']['friendly_single'];
				$replacements['related_friendly_single_ucfirst']=$this->ucfirst($table['relation']['remote_table']['friendly_single']);				
				$replacements['related_friendly_multiple']=$table['relation']['remote_table']['friendly_multiple'];
				$replacements['related_friendly_multiple_ucfirst']=$this->ucfirst($table['relation']['remote_table']['friendly_multiple']);
				
				$replacements['related_friendly_single_js']=$this->capitalize_underscore($table['relation']['remote_table']['friendly_single']);
				$replacements['related_friendly_multiple_js']=$this->capitalize_underscore($table['relation']['remote_table']['friendly_multiple']);
				
				$replacements['related_field_id']=$table['relation']['field'];
			}
			

			
			file_put_contents($this->module_dir.'language/en.js', "\n/* table: ".$table['name']." */\n", FILE_APPEND);
			
			$langvars = 
				'GO.'.$this->module.'.lang.'.$replacements['friendly_single_js'].'="'.$this->var_to_lang($replacements['friendly_single']).'";'."\n".
				'GO.'.$this->module.'.lang.'.$replacements['friendly_multiple_js'].'="'.$this->var_to_lang($replacements['friendly_multiple']).'";'."\n";
				
			//append language vars
			file_put_contents($this->module_dir.'language/en.js', $langvars, FILE_APPEND);
			
			
			$langvars= '$lang[\''.$this->module.'\'][\''.$replacements['friendly_single'].'\']=\''.$this->var_to_lang($replacements['friendly_single']).'\';'.
			"\n".
			'$lang[\''.$this->module.'\'][\''.$replacements['friendly_multiple'].'\']=\''.$this->var_to_lang($replacements['friendly_multiple']).'\';'.
			"\n";
			
			//append language vars
			file_put_contents($this->module_dir.'language/en.inc.php', $langvars, FILE_APPEND);

			
			$columns = array();
			$fields = array();
			$formfields = array();
			$actionfields = array();
			$displayfields=array();
			
			
			
			$db = new db();
			$db->query('SHOW FIELDS FROM '.$table['name']);
			while($db->next_record())
			{	
				if($db->f('Field') == 'ctime')
				{
					$table['ctime']=true;
				}
				if($db->f('Field') == 'mtime')
				{
					$table['mtime']=true;
				}
				if($db->f('Field') == 'user_id')
				{
					$table['user_id']=true;
				}
				
				
				if(!in_array($db->f('Field'), $hidden_fields))
				{
					$column_replacements['HEADER']=$formfield_replacements['HEADER']=$this->get_jslang_var($db->f('Field'));
					$formfield_replacements['DATAINDEX']=$db->f('Field');
					switch($db->f('Field'))
					{
						case 'user_id':
							$column_replacements['DATAINDEX']='user_name';		
							break;
							
						default:
							$column_replacements['DATAINDEX']=$db->f('Field');
							break;
					}
					$table['replacements']=array_merge($column_replacements, $replacements);
					$column = $this->get_template_content('Column.tpl',$table);
					$this->replace_template($column, $table['replacements']);
					$columns[]=$column;
					
					
					if($db->f('Field')!='ctime' && $db->f('Field')!='mtime')
					{					
						if(isset($table['relation']['field'])  && $table['relation']['field']==$db->f('Field'))
						{
							$template='Combo.tpl';
						}else if($db->f('Field')=='user_id')
						{
							$template='UserId.tpl';
						}else
						{										
							$field_type = $this->parse_field_type($db->f('Type'));
							
							//echo $field_type['type']."\n";
							
							switch($field_type['type'])
							{
								case 'text':
									$template='TextArea.tpl';
									break;
									
								default:
									$template='TextField.tpl';
									break;
							}						
						}
						$table['replacements']=array_merge($formfield_replacements, $replacements);
						$formfield = $this->get_template_content($template,$table);
						$this->replace_template($formfield, $table['replacements']);
						$formfields[]=$formfield;
						
						if($db->f('Field')=='user_id')
							$actionfields[] = "\t\t\t".'if(isset($_POST[\''.$db->f('Field').'\']))'."\n\t\t\t\t".'$'.$table['friendly_single'].'[\''.$db->f('Field').'\']=$_POST[\''.$db->f('Field').'\'];';
						else
							$actionfields[] = "\t\t\t".'$'.$table['friendly_single'].'[\''.$db->f('Field').'\']=$_POST[\''.$db->f('Field').'\'];';
					}
					
					$displayfield = $this->get_template_content('DisplayField.tpl',$table);
					$this->replace_template($displayfield, $table['replacements']);
					$displayfields[]=$displayfield;
					
				}
				$fields[]=$db->f('Field')=='user_id' ? 'user_name' : $db->f('Field');
			}
			
			$replacements['STOREFIELDS']="'".implode("','",$fields)."'";
			$replacements['COLUMNS']=implode(',',$columns);			
			$replacements['FORMFIELDS']=implode(",",$formfields);
			$replacements['ACTIONFIELDS']=implode("\n",$actionfields);
			$replacements['DISPLAYFIELDS']=implode("\n",$displayfields);
			
			//var_dump($columns);
			
			//Create JS grid object
			$this->main_panel_replacements[$table['mainpanel_tag']]='GO.'.$this->module.'.'.$replacements['friendly_multiple_ucfirst'].'Grid';
			
			if(isset($table['mainpanel_tags']))
			{
				foreach($table['mainpanel_tags'] as $key=>$value)
				{
					$this->main_panel_replacements[$key]=$value;
				}
			}
			
			file_put_contents($this->module_dir.'scripts.txt', "\nmodules/".$this->module.'/'.$replacements['friendly_multiple_ucfirst']."Grid.js\n".file_get_contents($this->module_dir.'scripts.txt'));
			$grid_panel_file = $this->module_dir.$replacements['friendly_multiple_ucfirst'].'Grid.js';			
			$this->create_file($grid_panel_file, $table['template'], $replacements, $table);
			
			
			//create class functions
			$this->insert_template($this->module_dir.'classes/'.$this->module.'.class.inc.php','CLASSFUNCTIONS', 'functions.tpl', $replacements, $table);
			
			//insert on_search
			
			
			//echo "\n######\n\n# JSON \n\n****";
			
			//insert json
			$this->insert_template($this->module_dir.'json.php','TASKSWITCH', 'jsontasks.tpl', $replacements, $table);			
			
			//create actions
			$this->insert_template($this->module_dir.'action.php','TASKSWITCH', 'actiontasks.tpl', $replacements, $table);
			
			//create dialog			
			file_put_contents($this->module_dir.'scripts.txt', "modules/".$this->module.'/'.$replacements['friendly_single_ucfirst']."Dialog.js\n".file_get_contents($this->module_dir.'scripts.txt'));
			$dialog_file = $this->module_dir.$replacements['friendly_single_ucfirst'].'Dialog.js';			
			$this->create_file($dialog_file, 'Dialog.tpl', $replacements, $table);		
			
			file_put_contents($this->module_dir.'scripts.txt', "\nmodules/".$this->module.'/'.$replacements['friendly_single_ucfirst']."Panel.js\n".file_get_contents($this->module_dir.'scripts.txt'));
			$display_file = $this->module_dir.$replacements['friendly_single_ucfirst'].'Panel.js';
			$this->create_file($display_file, 'DisplayPanel.tpl', $replacements, $table);
			
			

			if(isset($table['link_type']) && $table['link_type']>0)
			{
				//add customfields
				$cf = $this->get_template_content('customfields.tpl', $table);
				$this->replace_template($cf, $replacements);
				
				file_put_contents($this->module_dir.'scripts.inc.php', $cf, FILE_APPEND);
				
				//add on_search function
				$this->insert_template($this->module_dir.'classes/'.$this->module.'.class.inc.php','ON_BUILD_SEARCH_INDEX_FUNCTION', 'on_search.tpl', $replacements, $table);

				//add on_delete function
				$this->insert_template($this->module_dir.'classes/'.$this->module.'.class.inc.php','ON_DELETE_LINK_FUNCTION', 'on_delete_link.tpl', $replacements, $table);
				
				//add link handler and singleton dialog
				$this->insert_template($this->module_dir.'MainPanel.js','LINKHANDLERS', 'linkHandler.tpl', $replacements, $table);
				
				//add singleton dialog for linkable item
				$this->insert_template($this->module_dir.'MainPanel.js','LINKDIALOGS', 'linkDialog.tpl', $replacements, $table);
				
				//add new menu item
				$this->insert_template($this->module_dir.'MainPanel.js','NEWMENUITEMS', 'NewMenuItem.tpl', $replacements, $table);
				
				//add link icon style
				$link_icon = $this->get_template_content('LinkIconStyle.tpl', $table);
				$this->replace_template($link_icon, $replacements);				
				file_put_contents($this->module_dir.'themes/Default/style.css', $link_icon, FILE_APPEND);
				
			}
		}
	}
	
	
	function var_to_lang($varname)
	{
		$lang = str_replace('_', ' ', $varname);
		return ucfirst($lang);
	}
	
	function parse_field_type($type)
	{
		$pos = strpos($type,'(');
	
		if($pos)
		{
			$arr['type'] = substr($type,0,$pos);
			$arr['value'] = substr($type,$pos+1,-1);
		}else {
			$arr['type']=$type;
			$arr['value']='';
		}
		return $arr;
	}
	
	public function insert_template($file, $position_tag, $template, $replacements, $conditions)
	{
		$conditions['replacements']=$replacements;
		
		$template_content = $this->get_template_content($template, $conditions);
		
		$this->replace_template($template_content, $replacements);
		
		$template_content=str_replace("\n\n", "\n", $template_content);
		
		$file_content = file_get_contents($file);
		
		$tag = '/* {'.$position_tag.'} */';
		
		$pos = strpos($file_content, $tag);
		
		if($pos)
		{
			$first_part = substr($file_content, 0, $pos);
			$last_part = substr($file_content, $pos);
			
			file_put_contents($file, $first_part.$template_content.$last_part);			
		}
	}	
}