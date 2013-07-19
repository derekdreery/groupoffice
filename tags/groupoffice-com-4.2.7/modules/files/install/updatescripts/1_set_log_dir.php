<?php
$folder = GO_Files_Model_Folder::model()->findByPath("log", true);
$folder->setNewAcl();
$folder->readonly=0;
$folder->save();
