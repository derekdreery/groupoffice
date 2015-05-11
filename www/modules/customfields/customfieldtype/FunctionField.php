<?php


namespace GO\Customfields\Customfieldtype;


class FunctionField extends AbstractCustomfieldtype {

	public function name() {
		return 'Function';
	}

	public function formatFormOutput($column, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {
		$result_string = '';

		if (!empty($this->field->function)) {
			$f = $this->field->function;
			preg_match_all('/\{([^}]*)\}/',$f,$matches);
			if (!empty($matches[1])) {
				foreach ($matches[1] as $key) {		
					if(!isset($attributes[$key])||$attributes[$key]==='')
						return null;
					else
						$value = $attributes[$key];
					$f = str_replace('{' . $key . '}', floatval($value), $f);				
				}
			}
			$f = preg_replace('/\{[^}]*\}/', '0',$f);

			eval("\$result_string=" . $f . ";");
			
//			\GO::debug("Function ($column): ".$this->field->function.' => '.$f.' = '.$result_string);
		}
		

		$attributes[$column] = \GO\Base\Util\Number::localize($result_string);
		return $attributes[$column];
	}
	
	public function formatDisplay($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {
		$value = $this->formatFormOutput($key, $attributes, $model);
		if (isset($value)) {
			$prefix = !empty($this->field->prefix) ? $this->field->prefix.' ' : '';
			$suffix = !empty($this->field->suffix) ? ' '.$this->field->suffix : '';
			return $prefix.$value.$suffix;
		} else {
			return null;
		}
	}
	
	public function formatFormInput($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model){
		$result_string = '';

		if (!empty($this->field->function)) {
			$f = $this->field->function;
			foreach ($attributes as $key=>$value) {
				
					$f = str_replace('{' . $key . '}', floatval(\GO\Base\Util\Number::unlocalize($value)), $f);
				
			}
			$f = preg_replace('/\{[^}]*\}/', '0',$f);
			
			$old = ini_set("display_errors", "on"); //if we don't set display_errors to on the next eval will send a http 500 status. Wierd but this works.
			@eval("\$result_string=" . $f . ";");
			if($old!==false)
				ini_set("display_errors", $old);
			
			if($result_string=="")
				$result_string=null;
				
		}

		$attributes[$key] = $result_string;
		return $attributes[$key];
	}

}