<?php
require_once($GO_CONFIG->class_path.'sajax.class.inc');
class autocomplete_select
{
	var $id;	
	var $callbackFunction;
	var $height;
	var $width;
	var $value;
	var $text;
	
	function autocomplete_select($id, $value, $callbackFunction, $width='300', $height='22', $text='', $jsaction='')
	{
		$this->text=$text;
		$this->id=$id;
		$this->value=$value;
		$this->callbackFunction=$callbackFunction;
		$this->height=$height;
		$this->width=$width;
		$this->jsaction=$jsaction;
		
		sajax_init();
		// $sajax_debug_mode = 1;
		sajax_export($callbackFunction);
		
		
		
		sajax_handle_client_request();
	}
	
	function get_header()
	{
		$header = '<script type="text/javascript" language="javascript">
		function do_'.$this->id.'_cb(results) {
			var resultsDiv = document.getElementById("results_'.$this->id.'");
			resultsDiv.innerHTML = results;
			var textInput = document.getElementById("text_'.$this->id.'");

			resultsDiv.style.top = findPosY(textInput)+parseInt(textInput.style.height);
			resultsDiv.style.left = findPosX(textInput);
			resultsDiv.style.width=textInput.style.width;
		}
		
		function do_'.$this->id.'_enter()
		{
			var resultsSelect = document.getElementById("'.$this->id.'");
			var textInput = document.getElementById("text_'.$this->id.'");
			var valueInput = document.getElementById("value_'.$this->id.'");
			if(resultsSelect)
			{
				if(textInput.value == "" && resultsSelect.selectedIndex!="undefined")
				{
						resultsSelect.options[resultsSelect.selectedIndex].selected=false;
						resultsSelect.style.display="none";	
				}else if(resultsSelect.selectedIndex!="undefined")
				{
					resultsSelect.style.display="none";				
					textInput.value=resultsSelect.options[resultsSelect.selectedIndex].innerHTML;
					valueInput.value=resultsSelect.options[resultsSelect.selectedIndex].value;
				}else
				{
					valueInput.value=\'\';
				}
			}else
			{
				valueInput.value=\'\';
			}
			
			'.$this->jsaction.'
		}
		
		function do_'.$this->id.'_hide()
		{
			var resultsSelect = document.getElementById("'.$this->id.'");
			var textInput = document.getElementById("text_'.$this->id.'");
			var valueInput = document.getElementById("value_'.$this->id.'");
			if(!resultsSelect || resultsSelect.style.display!="none")
			{
				//textInput.value=\'\';
				valueInput.value=\'\';
				if(resultsSelect)
				{
					resultsSelect.style.display="none";	
				}
			}
		}
		
		function do_'.$this->id.'_search(event) {
			event=event||false;
		
			if(event.which)
			{
				var whichCode = event.which;
			}else
			{		
				var whichCode = event.keyCode;
			}

			var resultsSelect = document.getElementById("'.$this->id.'");

			if(resultsSelect && whichCode==38)
			{
				var selectedIndex = resultsSelect.selectedIndex;
				if(resultsSelect.options[selectedIndex-1])
				{				
					resultsSelect.options[selectedIndex-1].selected=true;			
					resultsSelect.options[selectedIndex].selected=false;
				}
			}else if(resultsSelect && whichCode==40)
			{			
				var selectedIndex = resultsSelect.selectedIndex;
				if(resultsSelect.options[selectedIndex+1])
				{
					resultsSelect.options[selectedIndex+1].selected=true;			
					resultsSelect.options[selectedIndex].selected=false;
				}
			}else if(resultsSelect && whichCode==13)
			{			
				do_'.$this->id.'_enter();
			}else
			{	
				if(resultsSelect) resultsSelect.style.display="inline";
				
				// get the folder name
				var text;
				
				text = document.getElementById("text_'.$this->id.'").value;
				x_'.$this->callbackFunction.'(text, do_'.$this->id.'_cb);
			}		
		}
		'.sajax_get_javascript().'</script>';
	
		return $header;
	}
	
	function get_html()
	{
		//$text = isset($_POST[$this->id]['text']) ? smart_stripslashes($_POST[$this->id]['text']) : '';
		$input = new input('text', $this->id.'[text]',$this->text, false);
		$input->set_attribute('id', 'text_'.$this->id);
		$input->set_attribute('autocomplete','off');
		$input->set_attribute('onkeyup','javacript:do_'.$this->id.'_search(event);');
		$input->set_attribute('style','width:'.$this->width.';height:'.$this->height.';');
		$input->set_attribute('onblur','javascript:setTimeout(\'do_'.$this->id.'_hide();\',250);');
		
		
		$input2=new input('hidden',$this->id.'[value]', $this->value,false);
		$input2->set_attribute('id','value_'.$this->id);
		
		$div = new html_element('div');
		$div->set_attribute('id','results_'.$this->id);
		$div->set_attribute('style','position:absolute;');
		$div->set_attribute('onclick','javascript:do_'.$this->id.'_enter();');
		
		
		return $input2->get_html().$input->get_html().$div->get_html();
	}
}
