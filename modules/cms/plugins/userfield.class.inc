<?php
class cms_userfield {

	var $attributes;
	var $cms_site;

	function cms_userfield($attributes, &$cms_site)
	{
		$this->attributes = $attributes;
		$this->cms_site=&$cms_site;
	}

	function has_permission()
	{
		global $GO_MODULES;
		return true;
	}

	function get_name()
	{
		global $GO_LANGUAGE;
		require($GO_LANGUAGE->get_language_file('webshop'));
		return $cms_userfield;
	}

	function get_header()
	{
	}

	function get_html()
	{
		global $GO_SECURITY, $GO_MODULES, $GO_LANGUAGE, $GO_USERS;

		if($GO_SECURITY->logged_in())
		{
			$user = $GO_USERS->get_user($GO_SECURITY->user_id);
			if(isset($user[$this->attributes['field']]))
			{
				return htmlspecialchars($user[$this->attributes['field']]);
			}			
		}
		return '';
	}
}
?>
