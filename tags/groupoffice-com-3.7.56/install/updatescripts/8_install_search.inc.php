<?php
global $GO_MODULES;

//there might be an old 2.x search module.
$db->query("DELETE FROM go_modules WHERE id='search'");
$GO_MODULES->add_module('search');