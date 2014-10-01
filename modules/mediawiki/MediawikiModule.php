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
 * @author Michael de Hart <mdhart@intermesh.nl>
 */

namespace GO\Mediawiki;

use GO;
use GO\Base\Model\Acl;
use GO\Base\Model\User;
use GO\Base\Module;
use GO\Notes\Model\Category;

/**
 * 
 * The Notes module maintenance class
 * 
 */
class MediawikiModule extends Module{
	
	public function autoInstall() {
		return true;
	}
	
	
	public function install() {
		parent::install();
		return;
		$category = new Category();
		$category->name=GO::t('general','notes');
		$category->save();
		$category->acl->addGroup(GO::config()->group_everyone, Acl::READ_PERMISSION);
	}
}