<?php
class cms_search {

	var $query;
	var $attributes;
	var $cms_site;
	
	function cms_search($attributes, &$cms_site)
	{
		$this->attributes=$attributes;
		$this->cms_site = &$cms_site;
		$this->query = isset($_POST['query']) ? smart_stripslashes($_POST['query']) : '';

	}
	
	function has_permission()
	{
		return true;
	}
	
	function get_name()
	{
		return $GLOBALS['cmdSearch'];
	}
	
	function get_header()
	{
	}
	
	function get_html()
	{
		global $GO_SECURITY;
		
		$form = new form('search_form');
		$form->add_html_element(new input('hidden','task', 'search'));
		$form->add_html_element(new input('hidden','file_id', $this->cms_site->file_id));
		$form->add_html_element(new input('hidden','folder_id', $this->cms_site->folder_id));
		
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
		
		$input = new input('text','query', $this->query);
		$input->set_attribute('style', 'width:200px');
		
		$form->add_html_element($input);
		
			
		
		$button = new button($GLOBALS['cmdSearch'], 'javascript:document.search_form.task.value=\'search\';document.search_form.submit();');
		$button->set_attribute('style', 'margin:0px;');
		$form->add_html_element($button);
		
		
		$query = addslashes($this->query);
		
		if($task == 'search' && !empty($query))
		{
			$cms = new cms();
		
			$files = array();
			$allfiles = $cms->search_files($this->cms_site->site['root_folder_id'], $query);
			while($file=array_shift($allfiles))
			{
				if($file['acl']==0 || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $file['acl']))
				{
					$files[] = $file;
				}		
			}
			$count =count($files);
			
			if($count==1)
			{				
				$form->innerHTML .= '<p class="cms_search_text">'.sprintf($GLOBALS['cms_there_are_results_for_single'],$this->query).'</p>';	
			}else {
				$form->innerHTML .= '<p class="cms_search_text">'.sprintf($GLOBALS['cms_there_are_results_for_multiple'], $count, $this->query).'</p>';	
			}
			
			$count = 0;
			foreach($files as $file)
			{			
		
				$count++;
				$div = new html_element('div');
				$div->set_attribute('class', 'cms_search_result');
				
				$link = new hyperlink($_SERVER['PHP_SELF'].'?folder_id='.$file['folder_id'].'&amp;file_id='.$file['id'], strip_extension($file['name']));
				$div->add_html_element($link);
				
				$stripped_content = $cms->strip_for_multipage($file['content']);
				
				$max = 300;
				$lenght=strlen($stripped_content);
				if($lenght>$max && !empty($this->query))
				{
					$pos=strpos($stripped_content, $this->query);
					if(!$pos)
					{
						$pos=strpos($stripped_content, htmlspecialchars($this->query));					
					}
					if($pos)
					{						
						$start = $pos-($max/2);
						if($start<0)
						{
							$start=0;						
						}
						$end=$start+$max;

						if($end>$lenght)	
						{
							$end=$lenght;
							$start=$lenght-$max;
						}
						//echo $pos.' '.$lenght.' '.$start.' -> '.$end."\n";
						$description = substr($stripped_content, $start, ($end-$start)).'...';
					}else{
						$description = cut_string($stripped_content, 300);
					}
				}else {
					$description=$stripped_content;
				}
				
				$keywords = explode(' ', $this->query);
				foreach($keywords as $keyword)
				{				
					$description=preg_replace("#(".$keyword.")#si",'<span style="font-style:italic; font-weight:bold">\\0</span>',$description);
					$keyword2 = htmlspecialchars($keyword);
					if($keyword!=$keyword2)
					{
						$description=preg_replace("#(".$keyword2.")#si",'<span style="font-style:italic; font-weight:bold">\\0</span>',$description);
					}
				}

				//$description = str_replace(htmlspecialchars($this->query), '<span style="font-style:italic; font-weight:bold">'.htmlspecialchars($this->query).'</span>', $description);
					
								
				
				$p = new html_element('p', $description);
				$div->add_html_element($p);
				$form->add_html_element($div);				
			}			
		}
		return $form->get_html();
	}
}
