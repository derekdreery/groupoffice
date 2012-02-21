<?php
/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * Extended DateTime class to add GO specific functions 
 * 
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.util.date
 */

class GO_Base_Util_Date_DateTime extends DateTime{
	
	public static function fromUnixtime($time){
		return new self(date('Y-m-d H:i:s',$time), new DateTimeZone(date_default_timezone_get()));
	}
	
	/**
	 * Get the number of days elapsed. We could not use DateTime::diff() because it's only
	 * compatible with PHP 5.3
	 * 
	 * @param GO_Base_Util_Date_DateTime $dateTime
	 * @return int 
	 */
	public function getDaysElapsed($dateTime){
		$jdThis = gregoriantojd($this->format('n'),$this->format('j'),$this->format('Y'));		
		$jdDT = gregoriantojd($dateTime->format('n'),$dateTime->format('j'),$dateTime->format('Y'));
		
		return $jdDT-$jdThis;
	}
	
	/**
	 * Get an array with elapsed days, hours and minutes that can be used for
	 * addDiffCompat. These functions are for php 5.2 compatibility.
	 * 
	 * @param GO_Base_Util_Date_DateTime $dateTime
	 * @return array 
	 */
	public function getDiffCompat($dateTime){
		
		$hours = $dateTime->format('G')-$this->format('G');
		$mins = $dateTime->format('i')-$this->format('i');
		
		return array('days'=>$this->getDaysElapsed($dateTime),'hours'=>$hours, 'mins'=>$mins);
	}
	
	/**
	 * Add a diff array returned by getDiffCompat
	 * 
	 * @param array $diff
	 * @return GO_Base_Util_Date_DateTime 
	 */
	public function addDiffCompat($diff){
		$unixtime = GO_Base_Util_Date::date_add($this->format('U'), $diff['days']);
		$unixtime += (($diff['hours']*60)+$diff['mins'])*60;
				
		if($diff['days']>0)
			$this->setDate($this->format('Y'),$this->format('n'),$this->format('j')+$diff['days']);
		
		if($diff['hours']>0 || $diff['mins']>0)
			$this->setTime($this->format('G')+$diff['hours'], $this->format('i'), $this->format('s'));
		
		return $this;
	}
	
}