<?php
class cms_video {

	var $attributes;
	var $cms_site;

	function cms_video($attributes, &$cms_site)
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
		require($GO_LANGUAGE->get_language_file('cms'));
		return $cms_insert_video;
	}

	function get_header()
	{
	}

	function get_html()
	{
		return '';
	}
}
