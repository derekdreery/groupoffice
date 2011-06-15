<?php
require_once(GO::modules()->modules['wordpress']['class_path'].'wordpress.class.inc.php');
$wp = new wordpress();
$mapping = $wp->get_mapping();

$GO_SCRIPTS_JS .= 'GO.wordpress.mapping='.json_encode($mapping).';';
