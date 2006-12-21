<?php
class cms_gallery {

	var $attributes;
	
	function cms_gallery($attributes, &$cms_site)
	{	
		$this->attributes = $attributes;		
	}
	
	function has_permission()
	{
		global $GO_MODULES;		
		return (isset($GO_MODULES->modules['gallery']) && $GO_MODULES->modules['gallery']['read_permission']);		
	}
	
	function get_name()
	{
		global $GO_LANGUAGE;
		require($GO_LANGUAGE->get_language_file('gallery'));
		return $lang_modules['gallery'];
	}
	
	function get_header()
	{
	}	
	
	function get_html()
	{
		global $GO_SECURITY, $GO_MODULES, $GO_CONFIG, $GO_LANGUAGE, $GO_USERS;
		
		require($GO_LANGUAGE->get_language_file('gallery'));
		
		require_once($GO_MODULES->modules['gallery']['class_path'].'gallery.class.inc');
		$ig = new gallery();
		
		$gallery = $ig->get_gallery($this->attributes['gallery_id']);
		
		$start = isset($_POST['start'][$this->attributes['gallery_id']]) ? $_POST['start'][$this->attributes['gallery_id']] : 0;
		$offset = $gallery['maxcolumns']*$gallery['maxrows'];
		
		$form = new form('gallery_form');
		$form->add_html_element(new input('hidden','task', 'gallery'));
		$form->add_html_element(new input('hidden', 'start['.$this->attributes['gallery_id'].']', $start, false));
		
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
		
		
		
		
		

		$table = new table();
		$table->set_attribute('style','width:100%;');
		
		
		/*$row = new table_row();
		$cell = new table_cell();
		//$cell->set_attribute('style', 'text-align:center;');
		$cell->set_attribute('colspan', $gallery['maxcolumns']);
		$h1 = new html_element('h1', $gallery['name']);
		$h1->set_attribute('class', 'ig_name');
		$cell->add_html_element($h1);
		
		$p = new html_element('p', $gallery['description']);		
		$p->set_attribute('class', 'ig_description');
		$cell->add_html_element($p);
		$row->add_cell($cell);
		$table->add_row($row);*/
		
		$row = new table_row();
		
		$colcount=0;
		
		$count = $ig->get_images($this->attributes['gallery_id'], $start, $offset);
				
		while($ig->next_record())
		{
			$colcount++;
			
			$path = $GO_CONFIG->local_path.'gallery/'.$this->attributes['gallery_id'].'/'.$ig->f('filename');
			$url = $GO_CONFIG->local_url.'gallery/'.$this->attributes['gallery_id'].'/'.$ig->f('filename');
			
			$cell = new table_cell();
			$cell->set_attribute('style','text-align:center;vertical-align:top');
			
			if($ig->f('width') > $ig->f('height'))
			{
				$dimension = '&w='.$gallery['thumbwidth'];
			}else {
				$dimension = '&h='.$gallery['thumbwidth'];
			}
			
			$thumb = new image('', $GO_CONFIG->control_url.'phpthumb/phpThumb.php?src='.$path.$dimension);
			$thumb->set_attribute('style', 'border:1px solid #aaaaaa;');
			
			
			$user = $GO_USERS->get_user($ig->f('user_id'));
			$name = format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'first_name');
			
			$link = new hyperlink("javascript:popup('".$url."','".($ig->f('width')+30)."','".($ig->f('height')+40)."');",$thumb->get_html(), $ig_uploaded_by.' '.$name);		
			$cell->add_html_element($link);
		
			if($ig->f('description')!='')
			{
				$span = new html_element('span', $ig->f('description'));
				$span->set_attribute('class','ig_thumb_description');
				$cell->add_html_element($span);
			}
			$row->add_cell($cell);
			if($colcount==$gallery['maxcolumns'])
			{		
				$table->add_row($row);
				$row = new table_row();	
				$colcount=0;
			}
		}
		
		if($colcount>0)
		{	
			$colspan = $gallery['maxcolumns']-$colcount;
			$cell = new table_cell();
			$cell->set_attribute('colspan',$colspan);
			$row->add_cell($cell);
			
			$table->add_row($row);
		}
		
		
		$row = new table_row();

		$cell = new table_cell();
		$cell->set_attribute('colspan', '99');
		$cell->set_attribute('style','text-align:center;padding-top:10px;');

		if($offset>0)
		{
			$number_of_pages = ceil($count/$offset);
			$page = $start/$offset;
			
			if($page>0)
			{
				$link = new hyperlink("javascript:document.gallery_form.elements['start[".$this->attributes['gallery_id']."]'].value=".($start-$offset).";document.gallery_form.submit();", '&lt;&lt; '.$GLOBALS['cmdPrevious']);		
				$link->set_attribute('class', 'ig_pagination');
				$cell->add_html_element($link);
			}else {
				$span = new html_element('span', '&lt;&lt; '.$GLOBALS['cmdPrevious']);
				$span->set_attribute('class', 'ig_paginationDisabled');
				$cell->add_html_element($span);
			}
			
			for ($i=1;$i<=$number_of_pages;$i++)
			{
				$new_start = ($i-1)*$offset;
				$link = new hyperlink("javascript:document.gallery_form.elements['start[".$this->attributes['gallery_id']."]'].value=".$new_start.";document.gallery_form.submit();", $i);
				if($new_start==$start)
				{
					$link->set_attribute('class', 'ig_paginationActive');
				}else {
					$link->set_attribute('class', 'ig_pagination');
				}
				$cell->add_html_element($link);
				
				
			}
			if($page<$number_of_pages-1)
			{
				$link = new hyperlink("javascript:document.gallery_form.elements['start[".$this->attributes['gallery_id']."]'].value=".($start+$offset).";document.gallery_form.submit();", $GLOBALS['cmdNext'].' &gt;&gt;');		
				$link->set_attribute('class', 'ig_pagination');
				$cell->add_html_element($link);
			}else {
				$span = new html_element('span', $GLOBALS['cmdNext'].' &gt;&gt;');
				$span->set_attribute('class', 'ig_paginationDisabled');
				$cell->add_html_element($span);
			}
			$row->add_cell($cell);
			$table->add_row($row);
		}
		$form->add_html_element($table);
		
		
		if($GO_SECURITY->logged_in() && $GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_write']))
		{
			
			$return_to = $_SERVER['REQUEST_URI'];
			
			
			
			$link = new hyperlink($GO_MODULES->modules['gallery']['url'].'add.php?gallery_id='.$this->attributes['gallery_id'].'&return_to='.urlencode($return_to),$ig_upload);
			$link->set_attribute('class', 'ig_upload_link');
			
			$form->add_html_element($link);
		}

		
		
		return $form->get_html();
	}
}
