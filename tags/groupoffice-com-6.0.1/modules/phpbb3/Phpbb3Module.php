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

namespace GO\Phpbb3;


class Phpbb3Module extends \GO\Base\Module{
	
	public function autoInstall() {
		return false;
	}
}