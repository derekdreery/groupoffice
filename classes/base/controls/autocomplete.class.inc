<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/11/23 15:29:45 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create an form input field
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class autocomplete extends html_element
{
	var $hidden_input;
	var $text_input;
	var $container;
	var $xml_source;
	var $xml_fields;
	var $xml_root_element;
	var $form_name;
	var $submit_on_enter;
	var $log=false;
	var $lookup_url='';

	function get_header()
	{
		global $GO_CONFIG;
		
		$header = '';
		
		if(!defined('YAHOO_LOADED'))
		{
			$header= '<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/yahoo/yahoo-min.js"></script>'.
			'<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/dom/dom-min.js"></script>'.
			'<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/event/event-min.js"></script>'.
			'<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/connection/connection-min.js"></script>'.
			'<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/animation/animation-min.js"></script>';
			if($this->log)
			{
				$header .= '<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/logger/logger.js"></script>';
			}
			
			define('YAHOO_LOADED',true);
		}
		
		if(!defined('YAHOO_AC_LOADED'))
		{	
			$header .= '<script type="text/javascript" src="'.$GO_CONFIG->host.'yui/build/autocomplete/autocomplete-min.js"></script>';
			define('YAHOO_AC_LOADED',true);
		}


		$header .= '
		<script type="text/javascript">
		YAHOO.namespace ("'.$this->attributes['id'].'");
		YAHOO.'.$this->attributes['id'].'.ACXml = function(){
			var oACDS;
			var oAutoComp;
			var mylogger;

			return {

				init: function() {
				';
				if($this->log)
				{
					$header .= 'mylogger = new YAHOO.widget.LogReader("'.$this->attributes['id'].'_logger");';
				}
		$header .= '
					oACDS = new YAHOO.widget.DS_XHR("'.$this->xml_source.'",["'.$this->xml_root_element.'"';
					$fields=array();
					foreach($this->xml_fields as $xml_field=>$form_field)
					{
						$fields[]=$xml_field;
					}
					$header .= ',"'.implode('","', $fields).'"';					
					
					$header .=']);
					oACDS.responseType = YAHOO.widget.DS_XHR.TYPE_XML;
					oACDS.queryMatchContains = true;
					oACDS.scriptQueryParam = "query";
					';
					
					if(!empty($this->scriptQueryAppend))
					{
						$header .='oACDS.scriptQueryAppend="'.$this->scriptQueryAppend.'";';
					}

					$header .='
					oAutoComp = new YAHOO.widget.AutoComplete("'.$this->text_input->attributes['id'].'","'.$this->container->attributes['id'].'", oACDS);
					oAutoComp.queryDelay=0.5;
					oAutoComp.autoHighlight = true;
					oAutoComp.maxResultsDisplayed=10;
					
					oAutoComp.formatResult = function(oResultItem, sQuery) {					
						return oResultItem[0];
					};

					var myHandler = function(type, args, me) {
						var oAutoComp = args[0]; // the autocomplete instance
						var elListItem = args[1]; // the result list item element
						';
					
					$count=0;
					foreach ($this->xml_fields as $xml_field=>$form_field)
					{
						if(!empty($form_field))
						{
							if($this->overwrite || (isset($this->hidden_input) && $form_field==$this->hidden_input->attributes['name']))
							{
								$header .= 'document.forms["'.$this->form_name.'"].elements["'.$form_field.'"].value='.
									'args[2]['.$count.'];';
							}else {
								$header .= 'if(document.forms["'.$this->form_name.'"].elements["'.$form_field.'"].value=="" || document.forms["'.$this->form_name.'"].elements["'.$form_field.'"].value=="0")'.
								'{document.forms["'.$this->form_name.'"].elements["'.$form_field.'"].value=args[2]['.$count.'];}';
							}
						}
						//$header .= 'alert("'.$form_field.': "+args[2]['.$count.']);';
						$count++;
					}
					if($this->submit_on_enter)
					{
						$header .= 'document.forms["'.$this->form_name.'"].submit();';
					}
						
					$header .= '
					}
					oAutoComp.itemSelectEvent.subscribe(myHandler);
				}';
		if(!empty($this->lookup_url))		
			$header .= ',		
		
				lookup: function() {
					id = document.forms["'.$this->form_name.'"].elements["'.$this->hidden_input->attributes['name'].'"].value;
					if(id>0)
					{
						document.location="'.$this->lookup_url.'"+id+"&return_to='.urlencode($this->return_to).'";
					}
				}
			';
			
		$header .='};
		}();
		YAHOO.util.Event.addListener(this,"load",YAHOO.'.$this->attributes['id'].'.ACXml.init);
		</script>';
					
		return $header;
	}
	
	function add_bound_hidden_value($input_name, $input_value, $lookup_url='', $return_to='')
	{
		$this->return_to=$return_to;
		$this->lookup_url=$lookup_url;
		$this->hidden_input = new input('hidden', $input_name, $input_value, false);
		$this->text_input->set_attribute('onchange', "document.forms['".$this->form_name."'].elements['".$input_name."'].value=''");
	}
	

	function autocomplete($id='autocomplete', $textarea=false, $text_input_name, $text_input_value, 
		$form_name, $xml_source, $xml_root_element, $xml_fields, $overwrite_values=true, $submit_on_enter=false, $scriptQueryAppend='')
	{
		
		$this->submit_on_enter=$submit_on_enter;
		$this->overwrite=$overwrite_values;
		$this->form_name=$form_name;
		$this->xml_source=$xml_source;
		$this->xml_fields=$xml_fields;
		$this->xml_root_element=$xml_root_element;
		$this->scriptQueryAppend = $scriptQueryAppend;
		
		$this->set_attribute('id', $id);
		$this->set_attribute('class','autocomplete');
		$this->tagname = 'div';
		$this->set_linebreak("\n");
		
		if($textarea)
		{
			$this->text_input = new textarea($text_input_name,$text_input_value);
		}else {
			$this->text_input = new input('text',$text_input_name,$text_input_value,false);
			$this->text_input->set_attribute('autocomplete', 'off');
		}
		$this->text_input->set_attribute('onfocus', 'this.select();');
		//$this->text_input->set_attribute('onchange', 'YAHOO.'.$this->attributes['id'].'.ACXml.changed();');
		$this->text_input->set_attribute('id',$id.'_text_input');
		$this->text_input->set_attribute('class','autocomplete_input');

		$this->container = new html_element('div');
		$this->container->set_attribute('id', $id.'_container');
		$this->container->set_attribute('class','autocomplete_container');
		
		
	}

	function get_html()
	{
		if(isset($this->hidden_input))
		{
			$this->add_html_element($this->hidden_input);		
		}
		$this->add_html_element($this->text_input);				
		if(!empty($this->lookup_url))
		{
			$img = new image('magnifier');
			$img->set_attribute('align','absmiddle');
			$img->set_attribute('style','border:0px;');
			
			$link = new hyperlink('javascript:YAHOO.'.$this->attributes['id'].'.ACXml.lookup();', $img->get_html());
			$this->add_html_element($link);
				
		}
		$this->add_html_element($this->container);
		
		if($this->log)
		{
			$div = new html_element('div');
			$div->set_attribute('id',$this->attributes['id'].'_logger');
			$this->add_html_element($div);
		}
		
		return $this->get_header().parent::get_html();
	}
}
