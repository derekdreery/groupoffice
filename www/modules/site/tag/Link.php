<?php

namespace GO\Site\Tag;

class Link implements TagInterface {

	static function render($params, $tag) {

		$html = '<a';

		if (empty($params['slug'])) {
			return "Error: slug must be set in link tag!";
		}


		$model = Content::model()->findBySlug($params['slug'], $this->site_id);
		
		if(!$model){
			return "Broken link to slug: '".$params['slug']."'";
		}
		
		$params['href'] = $model->url;

		if (isset($params['anchor']))
			$params['href'].='#' . $params['anchor'];

		unset($params['anchor'], $params['slug']);


		foreach ($params as $key => $value) {
			$html .= ' ' . $key . '="' . $value . '"';
		}

		$html .= '>' . $tag['innerText'] . '</a>';

		return $html;
	}

}
