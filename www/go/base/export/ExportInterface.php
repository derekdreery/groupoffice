<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * An export is used by the controllers to output data to the browser.
 * For example JSON, XML, CSV, plain text or HTML
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.export
 */
interface GO_Base_Export_ExportInterface{
	
	public function sendHeaders();
	
	public function write($str);
	
	public function flush();
	
	public function endFlush();
	
	public function getName();
	
}