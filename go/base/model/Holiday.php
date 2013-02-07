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
 * The Holiday model
 * 
 * @version $Id: Holiday.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model 
 * 
 * @property int $id 
 * @property String $date 
 * @property String $name
 * @property String $region
 */
class GO_Base_Model_Holiday extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_Module 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'go_holidays';
	}
	
	/**
	 * Get the holidays between the given start and end date
	 * By default this will return all the holidays within all the locales.
	 * You can pass a locale to return only the holidays of that locale
	 * Example locales: 'en','nl','no'
	 * 
	 * When the $check parameter is set to true then the function will check the 
	 * holidays table for existing holidays in the given locale and year.
	 * If the holidays don't exist then it will generate them automatically
	 * 
	 * If $force is set to true then the current holidays in the given period and 
	 * locale will be deleted and recreated from the holidays file.
	 * 
	 * @param string $startDate
	 * @param string $endDate
	 * @param string $locale
	 * @param boolean $check
	 * @param boolean $force
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function getHolidaysInPeriod($startDate,$endDate,$locale=false,$check=true,$force=false){
		
		$startDate = strtotime($startDate);
		$endDate = strtotime($endDate);
		
		if(!empty($locale) && $check){
			$year = date('Y',$startDate);
						
			if($force || !$this->checkHolidaysExist($year,$locale))
				$this->generateHolidays($year,$locale);
		}
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('date', $startDate,'>=')
						->addCondition('date', $endDate, '<=');	
		
		if(!empty($locale))
			$findCriteria->addCondition('region', $locale);
			
		$findParams = GO_Base_Db_FindParams::newInstance()
						->criteria($findCriteria);
		
		return GO_Base_Model_Holiday::model()->find($findParams);
	}
	
	/**
	 * Check if the requested holidays are available in the database.
	 * 
	 * @param string $year
	 * @param string $locale
	 * @return int
	 * @throws Exception 
	 */
	public function checkHolidaysExist($year,$locale){

		if(empty($year) || empty($locale))
			Throw new Exception('No year or locale given for the holidays checker.');
		
		$startYear = mktime(0, 0, 0, 1, 1, $year);
		$endYear   = mktime(23, 59, 59, 12, 31, $year);
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
					->addCondition('date', $startYear,'>=')
					->addCondition('date', $endYear, '<=')
					->addCondition('region', $locale);

		$findParams = GO_Base_Db_FindParams::newInstance()
					->criteria($findCriteria);

		$result = GO_Base_Model_Holiday::model()->find($findParams);

		return ($result->rowCount() >= 1);
	}
	
	/**
	 * Delete all the holidays of the given year and locale
	 * 
	 * @param string $year
	 * @param string $locale
	 * @throws Exception 
	 */
	public function deleteHolidays($year,$locale='en'){
		
		if(empty($year) || empty($locale))
			Throw new Exception('No year or locale given for the holidays delete function.');
		
		$startYear = mktime(0, 0, 0, 1, 1, $year);
		$endYear   = mktime(23, 59, 59, 12, 31, $year);
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
					->addCondition('date', $startYear,'>=')
					->addCondition('date', $endYear, '<=')
					->addCondition('region', $locale);

		$findParams = GO_Base_Db_FindParams::newInstance()
					->criteria($findCriteria);

		$holidays = GO_Base_Model_Holiday::model()->find($findParams);
		
		while($holiday = $holidays->fetch()){
			$holiday->delete();
		}
	}
	
	/**
	 * Generate the holidays from the holidays file for the given year and locale.
	 * 
	 * @param string $year
	 * @param string $locale
	 * @throws Exception 
	 */
	public function generateHolidays($year,$locale='en'){
		
		$this->deleteHolidays($year,$locale);
		
		// Load the holidays file for the given $locale
		if(is_file(GO::config()->root_path.'language/holidays/'.$locale.'.php'))
			require(GO::config()->root_path.'language/holidays/'.$locale.'.php');
//		else
//			throw new Exception('No holidays file for this language: '.$locale.'.');
		
		if(empty($year)) {			
			$year = date('Y');
		}
		
		$in_holidays = array();
		
		if(!empty($input_holidays))
			$in_holidays = $input_holidays;

		$holidays = array();
		
		// Prepare the holidays array
		foreach($in_holidays as $key => $date)
			$holidays[$key] = $date;
		
		// Set the fixed holidays from the holidays file
		if(isset($holidays['fix'])) {
			foreach($holidays['fix'] as $key => $name) {
				$month_day = explode("-", $key);
				$date = mktime(0,0,0,$month_day[0],$month_day[1],$year);
				
				$holiday = new GO_Base_Model_Holiday();
				$holiday->name = $name;
				$holiday->date = $date;
				$holiday->region = $locale;
				$holiday->save();
			}
		}
		
		// Set the variable holidays
		if(isset($holidays['var']) && function_exists('easter_date') && $year > 1969 && $year < 2037) {
			$easter_day = easter_date($year);
			foreach($holidays['var'] as $key => $name) {
				$date = strtotime($key." days", $easter_day);
				
				$holiday = new GO_Base_Model_Holiday();
				$holiday->name = $name;
				$holiday->date = $date;
				$holiday->region = $locale;
				$holiday->save();
			}
		}

		if(isset($holidays['spc'])) {
			$weekday = $this->get_weekday("24","12",$year);
			foreach($holidays['spc'] as $key => $name) {
				$count = $key - $weekday;
				$date = strtotime($count." days", mktime(0,0,0,"12","24",$year));
				
				$holiday = new GO_Base_Model_Holiday();
				$holiday->name = $name;
				$holiday->date = $date;
				$holiday->region = $locale;
				$holiday->save();
			}
		}
	}
	
}