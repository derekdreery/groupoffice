<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/
require_once("../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('search');
require_once($GO_LANGUAGE->get_language_file('search'));

$query='%'.smart_addslashes($_REQUEST['query']).'%';

require_once($GO_CONFIG->class_path.'/base/search.class.inc');
$search = new search();


header('Content-Type: text/xml; charset: UTF-8');
echo $search->global_search($GO_SECURITY->user_id, $query, 0,0);
