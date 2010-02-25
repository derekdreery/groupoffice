<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: class.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class cms extends db {

	var $valid_tags=array('select', 'input', 'file', 'textarea', 'checkbox');

	function get_field($fieldNode){

		

		if(in_array($fieldNode->nodeName, $this->valid_tags))
		{
			$nameNode=$fieldNode->attributes->getNamedItem('name');
			$fieldLabelNode=$fieldNode->attributes->getNamedItem('field_label');
			$option['type']=$fieldNode->nodeName;
			$option['name']=$nameNode->nodeValue;
			$option['fieldLabel']=$fieldLabelNode ? $fieldLabelNode->nodeValue : $option['name'];

			if($option['type']=='select')
			{
				$option['options']=array();

				foreach($fieldNode->childNodes as $optionNode)
				{
					if($optionNode->nodeType == 1)
					{
						$nameNode=$optionNode->attributes->getNamedItem('value');

						$value = isset($nameNode->nodeValue) ? $nameNode->nodeValue : $optionNode->nodeValue;
						
						$option['options'][]=array(
						$value,
						$optionNode->nodeValue
						);
					}
				}
			}elseif($option['type']=='file'){
				$resizeLabelNode=$fieldNode->attributes->getNamedItem('resize');
				$option['resize']=$resizeLabelNode ? $resizeLabelNode->nodeValue : '';
				
				$filterLabelNode=$fieldNode->attributes->getNamedItem('files_filter');
				$option['files_filter']=$filterLabelNode ? $filterLabelNode->nodeValue : '';
			}
			return $option;
		}
		return false;
	}


	function get_template_config($template)
	{
		global $GO_MODULES;

		$doc = new DOMDocument();
		$doc->load($GO_MODULES->modules['cms']['path'].'templates/'.$template.'/config.xml');

		$config['types']=array();

		$optionsNodes = $doc->documentElement->getElementsByTagName('file_options');

		$globalFields=array();

		if($optionsNodes->length)
		{
			$nodeList = $optionsNodes->item(0)->childNodes;

			foreach($nodeList as $typeNode)
			{
				if($typeNode->nodeName=='type')
				{
					$nameNode=$typeNode->attributes->getNamedItem('name');
					$type['name']=$nameNode->nodeValue;
					$type['options']=array();

					foreach($typeNode->childNodes as $fieldNode)
					{
						//echo $fieldNode->nodeName."\n";

						$option = $this->get_field($fieldNode);
						if($option)
							$type['options'][]=$option;
					}
					$config['types'][]=array($type['name'], $type['options']);
				}elseif(in_array($typeNode->nodeName, $this->valid_tags))
				{
					$option = $this->get_field($typeNode);
						if($option)
							$globalFields[]=$option;
				}
			}

			if(count($globalFields))
			{
				for($i=0;$i<count($config['types']);$i++)
				{
					$config['types'][$i][1]=array_merge($config['types'][$i][1], $globalFields);
				}
			}
		}

		$config['templates']=array();
		$templatesNodes = $doc->documentElement->getElementsByTagName('templates');
		if($templatesNodes->length)
		{
			$templatesNode = $templatesNodes->item(0);
			if($templatesNode)
			{
				/*$defaultNode = $templatesNode->attributes->getNamedItem('default');
				 if($defaultNode)
				 {
				 $config['default_template']=$defaultNode->nodeValue;
				 }*/
	
				$nodeList = $templatesNode->childNodes;
	
	
				foreach($nodeList as $tNode)
				{
					if($tNode->nodeName == 'template')
					{
						$htmlDoc = new DOMDocument();
						$htmlDoc->appendChild($htmlDoc->importNode($tNode,true));
	
						$nameNode=$tNode->attributes->getNamedItem('name');
						$config['templates'][]=array(
						$nameNode->nodeValue,
						$htmlDoc->saveHTML()
						);
					}
				}
			}
		}

		return $config;
	}

	function build_template_values_xml($values)
	{
		$doc = new DomDocument('1.0');

		// create root node
		$root = $doc->createElement('template_options');
		$root = $doc->appendChild($root);

		foreach($values as $name=>$value)
		{
			$optionNode = $doc->createElement('option');
			$optionNode->setAttribute('name', $name);
			$optionNode->setAttribute('value', $value);

			$root->appendChild($optionNode);
		}
		return $doc->saveXML();
	}


	function search_files($folder_id, $search_word)
	{

		//$search_word = strtoupper($search_word);
		//$search_word = '%'.$search_word.'%';
		$cms = new cms();

		$files = array();
		/*
		 $sql = "SELECT * FROM cms_files WHERE (extension='html' OR extension='htm') AND folder_id='$folder_id' AND (UPPER(content) REGEXP '[[:<:]]".$search_word."[[:>:]]'";

		 $search_word2 = htmlspecialchars($search_word);
		 if($search_word2 != $search_word)
		 {
		 $sql .= " OR UPPER(content) REGEXP '[[:<:]]".$search_word2."[[:>:]]')";
		 }else
		 {
		 $sql .= ")";
		 }
		 */

		$sql = "SELECT * FROM cms_files WHERE folder_id='".$this->escape($folder_id)."'";

		$allkeywords=array();
		$keywords= explode(' ', $search_word);
		foreach($keywords as $keyword)
		{

			$keyword2 = htmlspecialchars($keyword);
			$sql_str='content LIKE \'%'.$this->escape($keyword).'%\'';
			if($keyword2 != $keyword)
			{
				$sql_str='('.$sql_str.' OR content LIKE \'%'.$this->escape($keyword2).'%\')';
			}
			$allkeywords[]=$sql_str;
		}

		if(count($allkeywords))
		{
			$sql .= ' AND '.implode(' AND ',$allkeywords);
		}


		$this->query($sql);

		while ($file = $this->next_record())
		{
			$file['fstype']='file';
			$files[] = $file;
		}

		$this->get_folders($folder_id);
		while($this->next_record())
		{
			$files = array_merge($files, $cms->search_files($this->f('id'), $search_word));
		}
		return $files;
	}



	function get_title_from_html($html, $title='')
	{
		global $GO_CONFIG;

		$important_tags = array(
		'//h1', '//h2', '//h3', '//h4', '//h5', '//h6', '//strong', '//b', "//*[@style='font-weight: bold;']", '//i', "//*[@style='font-style: italic;']"
		);

		$html = str_replace('>', '> ', $html);
		$html = str_replace('\r','', $html);
		$html = str_replace('\n',' ', $html);
		$html = preg_replace('/&[^;]*;/', '', $html);
		$html = '<html><body>'.strip_tags($html, '<h1><h2><h3><h4><h5><h6><strong><span><ul><ol><li><b><i>').'</body></html>';

		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		foreach($important_tags as $important_tag)
		{
			$nodes = $xpath->query($important_tag);
			foreach($nodes as $node)
			{
				if(strlen($title) == 0)
				{
					$title = trim(strip_tags($node->textContent));
				}else
				{
					$important_words = $this->get_keywords_from_string($node->textContent);

					foreach($important_words as $word)
					{
						if(strlen($title.' '.$word) > 90)
						{
							return $title;
						}elseif(!stristr($title, $word))
						{
							if(!isset($first))
							{
								$title .= ' - ';
								$first = true;
							}else
							{
								$title .= ' ';
							}
							$title .= trim($word);
						}
					}
				}
			}
		}

		return $title;
	}

	function get_keywords_from_string($string)
	{
		$words = explode(' ', $string);

		$important_words = array();
		foreach($words as $word)
		{
			$word = $this->strip_unwanted_chars($word);
			if(strlen($word) > 0)
			{
				if(
				(strlen($word) > 5  || strtoupper($word) == $word)		&&
				!in_array(strtolower($word), $important_words))
				{
					$important_words[] = strtolower($word);
				}
			}
		}
		return $important_words;
	}

	function get_description_from_html($html, $description='')
	{
		global $GO_CONFIG;

		/*$important_tags = array(
		 '//h1', '//h2', '//h3', '//h4', '//h5', '//h6', '//strong', '//b', "//*[@style='font-weight: bold;']", '//i', "//*[@style='font-style: italic;']"
		 );


		 $html = str_replace('>', '> ', $html);
		 $html = str_replace('\r','', $html);
		 $html = str_replace('\n',' ', $html);
		 $html = preg_replace('/&[^;]*;/', '', $html);

		 $html = str_replace('>', '> ', $html);
		 $html = str_replace('\r','', $html);
		 $html = str_replace('\n',' ', $html);
		 $html = preg_replace('/&[^;]*;/', '', $html);
		 $html = '<html><body>'.strip_tags($html, '<h1><h2><h3><h4><h5><h6><strong><span><ul><ol><li><b><i>').'</body></html>';

		 $doc = new DOMDocument();
		 $doc->loadHTML($html);
		 $xpath = new DOMXPath($doc);


		 foreach($important_tags as $important_tag)
		 {
		 $nodes = $xpath->query($important_tag);
		 foreach($nodes as $node)
		 {
		 $important_words = $this->get_keywords_from_string($node->textContent);

		 foreach($important_words as $word)
		 {
		 if(strlen($description.' '.$word) > 250)
		 {
		 return $description;
		 }elseif(!stristr($description, $word))
		 {
		 if(strlen($description) > 0)
		 {
		 $description .= ' ';
		 }
		 $description .= trim($word);
		 }
		 }
		 }
		 }*/

		$html = strip_tags($html);
		$html = $this->strip_unwanted_chars($html);
		return String::cut_string($html, 240);
	}

	function get_keywords_from_html($html, $keywords='')
	{
		$html = $this->strip_unwanted_chars($html);
		
		$keywordsArr = $this->get_keywords_from_html_in_array($html);
		foreach($keywordsArr as $keyword)
		{
			if(!stristr($keywords, $keyword))
			{
				if($keywords != '')
				{
					$keywords .= ', ';
				}
				$keywords .= trim($keyword);
			}
		}
		return $keywords;
	}

	function strip_unwanted_chars($word)
	{
		//cannot yet handle MBCS in html_entity_decode BUG
		//global $charset;
		$word = html_entity_decode($word, ENT_QUOTES, 'UTF-8');

		

		//Workaround:
		$word = str_replace('&nbsp;',' ', $word);
		$word = str_replace('&amp;','&', $word);

		
		$word = str_replace('(','', $word);
		$word = str_replace(')','', $word);
		$word = str_replace('.','', $word);
		$word = str_replace('!','', $word);
		$word = str_replace('?','', $word);
		$word = str_replace(':','', $word);
		$word = str_replace(',','', $word);

		$word = str_replace(chr(194), '', $word);
	
		$word = trim($word);

		return $word;
	}


	function get_keywords_from_html_in_array($html)
	{
		global $GO_CONFIG;

		$important_tags = array(
		'//h1', '//h2', '//h3', '//h4', '//h5', '//h6', '//strong', '//b', "//*[@style='font-weight: bold;']", '//i', "//*[@style='font-style: italic;']"
		);

		$html = str_replace('\r','', $html);
		$html = str_replace('\n',' ', $html);
		$html = str_replace('>','> ', $html);
		$html = preg_replace('/&[^;]*;/', '', $html);
		$html = '<html>'.strip_tags($html, '<h1><h2><h3><h4><h5><h6><strong><span><ul><ol><li><b><i>').'</html>';

		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$important_words = array();

		$strings = array();
		foreach($important_tags as $important_tag)
		{
			$nodes = $xpath->query($important_tag);
			foreach($nodes as $node)
			{
				$strings[] = strip_tags($node->textContent);
			}
		}
			
		$string = implode(' ', $strings);
		$important_words = array_merge($important_words, $this->get_keywords_from_string($string));
			
		//Words that occur more then once
		$text = strip_tags($html);
			
		$words = explode(' ', $text);
			
		foreach($words as $word)
		{
			$word = $this->strip_unwanted_chars($word);
			if(strlen($word) > 0)
			{
				if(
				(strlen($word) > 6  || strtoupper($word) == $word)		&&
				count(array_keys($words, $word)) > 1 &&
				!in_array(strtolower($word), $important_words))
				{
					$important_words[] = strtolower($word);
				}
			}
		}

		return $important_words;
	}




	function get_visible_folders($folder_id)
	{
		$this->query("SELECT * FROM cms_folders WHERE parent_id='".$this->escape($folder_id)."' AND disabled='0' ORDER BY priority ASC");
		return $this->num_rows();
	}

	function get_site_by_domain($domain, $recurse=false)
	{
		$domain = $this->prepare_domain($domain);
		$this->query("SELECT * FROM cms_sites WHERE domain='".$this->escape($domain)."'");
		if ($this->next_record())
		{
			return $this->record;
		}elseif($recurse)
		{
			while($pos = strpos($domain,'.'))
			{
				$domain = substr($domain, $pos+1);
				return $this->get_site_by_domain($domain, true);
			}
		}
		return false;
	}

	function prepare_domain($domain)
	{
		$domain = preg_replace("/http(s?):\/\//i", '', $domain);
		if (substr($domain, -1)=='/')
		{
			$domain = substr($domain,0, -1);
		}
		return $domain;
	}

	/*
	 Check if a folder is in the path of another folder.
	 This is used to check if we can move a folder into another.
	 */
	function is_in_path($check_folder_id, $target_folder_id)
	{
		if($target_folder_id == 0)
		{
			return false;
		}elseif ($target_folder_id == $check_folder_id)
		{
			return true;
		}else
		{
			$folder = $this->get_folder($target_folder_id);
			return $this->is_in_path($check_folder_id, $folder['parent_id']);
		}
	}

	function copy_file($file_id, $new_folder_id)
	{
		//if the name exists add (1) behind it.
		if($file = $this->get_file($file_id, false))
		{
			$name = $file['name'];
			$x=0;
			while ($this->file_exists($new_folder_id, $name))
			{
				$x++;
				$name = $file['name'].' ('.$x.')';
			}
			$file['name']=$name;
			$file['folder_id']=$new_folder_id;
			unset($file['files_folder_id']);
			$file = $file;

			$folder = $this->get_folder($new_folder_id);
			$site = $this->get_site($folder['site_id']);

			return $this->add_file($file, $site);

		}
		return false;
	}

	function copy_folder($folder_id, $new_parent_id)
	{
		$new_folder = $this->get_folder($new_parent_id);
		$folder = $this->get_folder($folder_id);
		if ($folder && $new_folder)
		{
			//don't move folders into thier own path
			if (!$this->is_in_path($folder_id, $new_parent_id))
			{
				//if the name exists add (1) behind it.
				$name = $folder['name'];
				$x=0;
				while ($this->folder_exists($new_parent_id, $name))
				{
					$x++;
					$name = $folder['name'].' ('.$x.')';
				}

				$folder['name']=$name;
				$folder['parent_id']=$new_parent_id;
				$folder['site_id']=$new_folder['site_id'];

				$folder = $folder;

				if($new_folder_id = $this->add_folder($folder))
				{
					$cms = new cms();
					$this->get_files($folder_id);
					while($this->next_record())
					{
						if(!$cms->copy_file($this->f('id'), $new_folder_id))
						{
							return false;
						}
					}

					$this->get_folders($folder_id);
					while($this->next_record())
					{
						if(!$cms->copy_folder($this->f('id'),$new_folder_id))
						{
							return false;
						}
					}
					return true;
				}
			}
		}
		return false;
	}



	function build_path($folder_id, $root_folder_id=0, $path='')
	{
		if($folder_id==0 || $folder_id==$root_folder_id)
		{
			return $path;
		}else
		{
			$folder=$this->get_folder($folder_id);
			if(!$folder)
			return $path;

			$path = empty($path) ? $folder['name'] : $folder['name'].'/'.$path;
			return $this->build_path($folder['parent_id'], $root_folder_id, $path);
		}
	}




	function resolve_url($url, $folder_id)
	{
		if(empty($url))
		{
			return $this->get_folder($folder_id);
		}
		if(substr($url,-1)=='/')
		{
			$url=substr($url,0,-1);
		}
		$parts = explode('/', $url);
		$first_part = array_shift($parts);

		if(count($parts))
		{
			$folder = $this->folder_exists($folder_id, $first_part);
		
			if($folder)
			{			
				return $this->resolve_url(implode('/', $parts), $folder['id']);
			}else
			{				
				return false;
			}
		}else
		{
			$file = $this->file_exists($folder_id, $first_part);
			if(!$file)
			{
				return $this->folder_exists($folder_id, $first_part);
			}else{
				return $file;
			}
		}
	}

	function file_exists($folder_id, $filename)
	{
		$this->query("SELECT * FROM cms_files WHERE folder_id='".$this->escape($folder_id)."' AND  name COLLATE utf8_bin LIKE '".$this->escape($filename)."'");
		if ($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function folder_exists($parent_id, $name)
	{
		$sql = "SELECT * FROM cms_folders WHERE parent_id='".$this->escape($parent_id)."' AND name COLLATE utf8_bin LIKE '".$this->escape($name)."'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Add a Site
	 *
	 * @param Array $site Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_site(&$site)
	{
		$site['id']=$this->nextid('cms_sites');

		$folder['name']='Root';
		$folder['site_id']=$site['id'];
		$folder['parent_id']=0;

		$site['root_folder_id'] = $this->add_folder($folder);

        global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			$f = $files->check_share('public/cms/'.File::strip_invalid_chars($site['name']),$site['user_id'], $site['acl_write'], $site['acl_write']);
            $site['files_folder_id']=$f['id'];
		}

		if($this->insert_row('cms_sites', $site))
		{
			return $site['id'];
		}
		return false;
	}
	/**
	 * Update a Site
	 *
	 * @param Array $site Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_site($site, $old_site=false)
	{
        if(!$old_site)$old_site=$this->get_site($site['id']);
        
        global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']) && $site['name']!=$old_site['name'])
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			$files->move_by_paths('public/cms/'.File::strip_invalid_chars($old_site['name']), 'public/cms/'.File::strip_invalid_chars($site['name']));
		}

		return $this->update_row('cms_sites', 'id', $site);
	}

	/**
	 * Delete a Site
	 *
	 * @param Int $site_id ID of the site
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_site($site_id)
	{
		$cms = new cms();

		if($site = $this->get_site($site_id))
		{

            global $GO_MODULES;
            if(isset($GO_MODULES->modules['files']))
            {
                require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
                $files = new files();

                $folder = $files->resolve_path('public/cms/'.File::strip_invalid_chars($site['name']));
                if($folder){
                    $files->delete_folder($folder);
                }
            }

			$this->delete_folder($site['root_folder_id']);

			if($this->query("DELETE FROM cms_sites WHERE id='".$this->escape($site_id)."'"))
			{
				global $GO_SECURITY;
				$GO_SECURITY->delete_acl($site['acl_write']);
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets a Site record
	 *
	 * @param Int $site_id ID of the site
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_site($site_id)
	{
		$this->query("SELECT * FROM cms_sites WHERE id=".$this->escape($site_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Site record by the name field
	 *
	 * @param String $name Name of the site
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_site_by_name($name)
	{
		$this->query("SELECT * FROM cms_sites WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Sites
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_sites($sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM cms_sites ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}


	/**
	 * Gets all Sites where the user has access for
	 *
	 * @param String $auth_type Can be 'read' or 'write' to fetch readable or writable Sites
	 * @param Int $user_id First record of the total record set to return
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */

	function get_authorized_sites($user_id, $sort='name', $direction='ASC', $start=0, $offset=0)
	{

		$user_id = $this->escape($user_id);

		$sql = "SELECT DISTINCT cms_sites.* FROM cms_sites ".
 		"INNER JOIN go_acl a ON (cms_sites.acl_write = a.acl_id) ";	


		$sql .= "LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) WHERE ((".
 		"ug.user_id = ".$user_id.") OR (a.user_id = ".$user_id.")) ";
		$sql .= " ORDER BY ".$this->escape($sort." ".$direction);

		$this->query($sql);
		$count = $this->num_rows();
		if ($offset > 0)
		{
			$sql ." LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
			return $count;
		}else
		{
			return $count;
		}
	}



	/**
	 * Add a Folder
	 *
	 * @param Array $folder Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_folder($folder)
	{

		$folder['ctime']=$folder['mtime']=time();

		$items = $this->get_items($folder['parent_id']);
		if($last_item = array_pop($items))
		{
			$folder['priority']=$last_item['priority']+1;
		}

		$folder['id']=$this->nextid('cms_folders');
		if($this->insert_row('cms_folders', $folder))
		{
			return $folder['id'];
		}
		return false;
	}
	/**
	 * Update a Folder
	 *
	 * @param Array $folder Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_folder($folder)
	{

		$folder['mtime']=time();

		return $this->update_row('cms_folders', 'id', $folder);
	}

	/**
	 * Delete a Folder
	 *
	 * @param Int $folder_id ID of the folder
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_folder($folder_id)
	{
		if ($folder_id > 0)
		{
			//add a second cms object for simultanious select and delete from the db
			$cms2 = new cms();

			//get all folders
			$this->get_folders($folder_id);
			while($this->next_record())
			{
				if (!$cms2->delete_folder($this->f('id')))
				{
					return false;
				}
			}

			$this->get_files($folder_id);
			while ($this->next_record())
			{
				if(!$cms2->delete_file($this->f('id')))
				{
					return false;
				}
			}

			return $this->query("DELETE FROM cms_folders WHERE id='".$this->escape($folder_id)."'");
		}else
		{
			return false;
		}
	}

	function apply_template_options_recursively($type, $default_template, $folder, $site)
	{
		$cms = new cms();
		$cms->get_folders($folder['id']);
		while($cms->next_record())
		{
			$subfolder['id']=$cms->f('id');
			$subfolder['default_template']=$default_template;
			$subfolder['type']=$type;

			$this->update_folder($subfolder);

			$this->apply_template_options_recursively($type, $default_template, $subfolder, $site);
		}

		$cms->get_files($folder['id']);
		while($cms->next_record())
		{
			/*$template_values = $this->get_template_values($cms->record['option_values']);

			foreach($values as $name=>$value)
			$template_values[$name]=$value;*/

			$file['id']=$cms->f('id');
			$file['type']=$type;
           

			//$file['option_values']=$this->build_template_values_xml($template_values);
			$this->update_file($file, $site);
		}
	}


	/**
	 * Gets a Folder record
	 *
	 * @param Int $folder_id ID of the folder
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_folder($folder_id)
	{
		$this->query("SELECT * FROM cms_folders WHERE id=".$this->escape($folder_id));
		if($this->next_record())
		{

			return $this->record;
		}else
		{
			return false;
		}
	}
	/**
	 * Gets a Folder record by the name field
	 *
	 * @param String $name Name of the folder
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_folder_by_name($name)
	{
		$this->query("SELECT * FROM cms_folders WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Folders
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_folders($folder_id, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM cms_folders WHERE parent_id=".$this->escape($folder_id)." ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}


	function get_items($folder_id)
	{
		$items = array();

		$this->get_folders($folder_id);
		while($this->next_record())
		{
			$priority=$this->f('priority');
			while(isset($items[$priority]))
			$priority++;

			$items[$priority] = $this->record;
			$items[$priority]['fstype']='folder';
		}
		$this->get_files($folder_id);
		while($this->next_record())
		{
			$priority=$this->f('priority');
			while(isset($items[$priority]))
			$priority++;

			$items[$priority] = $this->record;
			$items[$priority]['fstype']='file';
		}
		ksort($items);
		return $items;
	}



	/**
	 * Add a File
	 *
	 * @param Array $file Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_file(&$file, $site)
	{

    global $GO_MODULES;
		if(!isset($file['files_folder_id']) && isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$new_path = $this->build_file_files_path($file, $site);
			$folder = $files->resolve_path($new_path,true,1,'1');
			$file['files_folder_id']=$folder['id'];
		}

		$file['ctime']=$file['mtime']=time();

		$items = $this->get_items($file['folder_id']);
		if($last_item = array_pop($items))
		{
			$file['priority']=$last_item['priority']+1;
		}


		$file['id']=$this->nextid('cms_files');
		if($this->insert_row('cms_files', $file))
		{
			return $file['id'];
		}
		return false;
	}
	/**
	 * Update a File
	 *
	 * @param Array $file Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_file($file, $site, $old_file=false)
	{
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']) && (isset($file['folder_id']) || isset($file['name'])))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			if(!$old_file) $old_file = $this->get_file($file['id']);

            if(!isset($file['folder_id']))
                $file['folder_id']=$old_file['folder_id'];

            if(!isset($file['name']))
                $file['name']=$old_file['name'];

			$new_path = $this->build_file_files_path($file, $site);
			$file['files_folder_id']=$files->check_folder_location($old_file['files_folder_id'], $new_path);
		}

		$file['mtime']=time();

		return $this->update_row('cms_files', 'id', $file);
	}

	/**
	 * Delete a File
	 *
	 * @param Int $file_id ID of the file
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_file($file_id)
	{
    global $GO_MODULES;
		
		if(isset($GO_MODULES->modules['files']))
		{
			$file = $this->get_file($file_id);
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			try{
				$files->delete_folder($file['files_folder_id']);
			}
			catch(Exception $e){}
		}
		return $this->query("DELETE FROM cms_files WHERE id=".$this->escape($file_id));
	}

	/**
	 * Gets a File record
	 *
	 * @param Int $file_id ID of the file
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_file($file_id, $convert_template_values=true)
	{
		$this->query("SELECT * FROM cms_files WHERE id=".$this->escape($file_id));
		if($this->next_record())
		{
			if($convert_template_values)
			$this->record['option_values']=$this->get_template_values($this->record['option_values']);

			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}

	function get_template_values($option_values_xml)
	{
		$option_values=array();
		if(!empty($option_values_xml))
		{
			$doc = new DOMDocument();
			if(@$doc->loadXML($option_values_xml))
			{
				$optionsNodes = $doc->documentElement->getElementsByTagName('file_options');

				$nodeList = $optionsNodes = $doc->documentElement->childNodes;


				foreach($nodeList as $optionNode)
				{
					$valueNode=$optionNode->attributes->getNamedItem('value');
					$nameNode=$optionNode->attributes->getNamedItem('name');

					$option_values[$nameNode->nodeValue]=$valueNode->nodeValue;

				}
			}
		}

		return $option_values;

	}

	/**
	 * Gets a File record by the name field
	 *
	 * @param String $name Name of the file
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_file_by_name($name)
	{
		$this->query("SELECT * FROM cms_files WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Files
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_files($folder_id, $sortfield='priority', $sortorder='ASC', $start=0, $offset=0, $only_visible=false)
	{
		$sql = "SELECT * FROM cms_files WHERE folder_id=".$this->escape($folder_id);

		if($only_visible){
			$sql .= " AND (show_until=0 OR show_until>".time().")";
		}

		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}


	/**
	 * This function is called when a user is deleted
	 *
	 * @param int $user_id
	 */

	public static function user_delete($user)
	{
		$cms = new cms();
		$cms2 = new cms();
		$sql = "SELECT id FROM cms_sites WHERE user_id='".$cms->escape($user['id'])."'";
		$cms->query($sql);
		while($cms->next_record())
		{
			$cms2->delete_site($this->f('id'));
		}
	}


	public function __on_load_listeners($events){
		$events->add_listener('check_database', __FILE__, 'cms', 'check_database');
		$events->add_listener('user_delete', __FILE__, 'cms', 'user_delete');
	}
	
	public static function check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'Website folders'.$line_break;

		if(isset($GO_MODULES->modules['files']))
		{
			$db = new db();
            $cms = new cms();

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$sql = "SELECT * FROM cms_sites";
			$db->query($sql);
			while($site = $db->next_record())
			{
				try{
					$folder = $files->check_share('public/cms/'.$site['name'], $site['user_id'], $site['acl_write'], $site['acl_write'], false);

                    $up_site['id']=$site['id'];
                    $up_site['files_folder_id']=$folder['id'];

                    $cms->update_row('cms_sites', 'id', $up_site);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}

            

            $db->query("SELECT fi.*,s.name AS site_name FROM cms_files fi INNER JOIN cms_folders fo ON fi.folder_id=fo.id INNER JOIN cms_sites s ON s.id=fo.site_id");;
            while($file = $db->next_record())
            {
                try{
					$path= $cms->build_file_files_path($file, array('name'=>$file['site_name']));
                    echo $path .$line_break;
					$up_file['files_folder_id']=$files->check_folder_location($file['files_folder_id'], $path);

					if($up_file['files_folder_id']!=$file['files_folder_id']){
						$up_file['id']=$file['id'];
						$cms->update_row('cms_files', 'id', $up_file);
					}
					$files->set_readonly($up_file['files_folder_id']);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
            }
		}
		echo 'Done'.$line_break.$line_break;
	}

    public function build_file_files_path($file, $site){
        $path = 'public/cms/'.$site['name'];

        $sub = $this->build_path($file['folder_id']).'/';
        $pos = strpos($sub, '/');
        $sub = substr($sub, $pos);
        $path .= $sub.$file['name'];
        return $path;        
    }

		public function has_folder_access($user_id,$folder_id){
			$this->query("SELECT * FROM cms_user_folder_forbid WHERE user_id = $user_id AND folder_id = $folder_id");
			if ($this->num_rows())
				return false;
			else
				return true;
		}

		public function user_folder_allow($user_id,$folder_id) {
			return $this->query("DELETE FROM cms_user_folder_forbid WHERE ".
				"user_id=$user_id AND folder_id=$folder_id");
		}

		public function user_folder_deny($user_id,$folder_id) {
			if ($this->has_folder_access($user_id,$folder_id)) {
				$uf['user_id'] = $user_id;
				$uf['folder_id'] = $folder_id;
				return $this->insert_row('cms_user_folder_forbid', $uf);
			} else {
				return true;
			}
		}
}
