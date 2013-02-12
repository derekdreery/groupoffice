<?php

/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Frontend loader for websites
 *
 * @package GO.sites
 * @copyright Copyright Intermesh
 * @version $Id GOS.php 2012-06-11 10:02:29 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */

class GOS
{
	private static $_site;

	/**
		* Stores the application instance in the class static member.
		* This method helps implement a singleton pattern for Website.
		* will cause the throw of an exception.
		* To retrieve the site instance, use {@link site()}.
		* @param CApplication $app the application instance. If this is null, the existing
		* application singleton will be removed.
		* @throws Exception if multiple application instances are registered.
		*/
	public static function setSite($app)
	{
		if(self::$_site===null || $app===null)
			self::$_site=$app;
		else
			throw new Exception('Frontend site can only be created once.');
	}

	/**
	 * Returns the website object $_site will be set only once in index.php and
	 * This function will be applied as singleton for getting the website component
	 * @return GO_Sites_Components_Website
	 */
	public static function site()
	{
		return self::$_site;
	}
	
	/**
	 * Handels string translation for sites 
	 */
	public static function t($key)
	{
		return self::$_site->getLanguage()->getTranslation($key);
	}
	
	/**
	 * Init GO and return the website component
	 * @return GO_Sites_Components_Website 
	 */
	public static function launch()
	{
		//Go up 3 dirs (components, sites, modules) to find GO.php
		require(dirname(__FILE__).'/../../../GO.php');

		return new GO_Sites_Components_Website();
	}
}
?>
