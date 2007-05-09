<?php

load_basic_controls();

$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);

$html = new html_element('html');

$head = new html_element('head');
$head->add_html_element(new html_element('title',$GO_CONFIG->title));


$meta = new html_element('meta');
$meta->set_attribute('http-equiv', 'Content-Type');
$meta->set_attribute('content', 'text/html; charset='.$charset);
$head->add_html_element($meta);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'javascript/common.js');
$head->add_html_element($script);

$link = new html_element('link');
$link->set_attribute('rel','shotcut icon');
$link->set_attribute('href',$GO_CONFIG->host.'lib/favicon.ico');
$head->add_html_element($link);

$link = new html_element('link');
$link->set_attribute('rel','stylesheet');
$link->set_attribute('type','text/css');
$link->set_attribute('href',$GO_THEME->theme_url.'css/common.css');
$head->add_html_element($link);



$body = new html_element('body');
