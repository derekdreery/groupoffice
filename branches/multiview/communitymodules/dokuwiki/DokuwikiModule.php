<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

/**
 * 
 * The Dokuwiki module maintenance class
 * 
 */
class GO_Dokuwiki_DokuwikiModule extends GO_Base_Module{
	
	public function autoInstall() {
		return false;
	}
	
	public function author() {
		return 'Wesley Smits';
	}
	
	public function authorEmail() {
		return 'wsmits@intermesh.nl';
	}
}