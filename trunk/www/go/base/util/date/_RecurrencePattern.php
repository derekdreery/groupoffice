<?php
class GO_Base_Util_Date_RecurrencePattern {
	
	public static $INTERVAL_TYPE_DAY = 'DAY';
	public static $INTERVAL_TYPE_MONTH = 'MONTH';
	public static $INTERVAL_TYPE_YEAR = 'YEAR';
	
	private $_start;					// From this date (DateTime object)
	private $_end;						// Till this date (DateTime object)
	private $_interval;				// Number (Integer)
	private $_interval_type;	// Day, Month, Year (String)
	
	
	/**
	 * Reset the state  of this object
	 */
	private function _reset() {
		$this->_start = now();
		unset($this->_end);
		unset($this->_interval);
		unset($this->_interval_type);
		return true;
	}
	
	
	/**
	 * Set the parameters of this object
	 * 
	 * @param Array $params 
	 * @return Boolean $success
	 */
	private function _setParams($params) {
		if($this->_reset()) {
			foreach($params as $param=>$value) {
				$parameter = '_'.$param;
				$this->$parameter = $value;
			}
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get the next Recurrence against the given params
	 * 
	 * @param array $params The params that needs to be set to get the next occurrence date 
	 * @return DateTime 
	 */
	public function getNextRecurrence($params = array()) { 
		if(!empty($params)) {
			if($this->_setParams($params)) {
				
				if(!empty($this->_interval_type) && !empty($this->_interval) && !empty($this->_start)) {
					
					$occurrence = '';
					
					switch($this->_interval_type) {
						case GO_Base_Util_Date_RecurrencePattern::$INTERVAL_TYPE_DAY :
							$occurrence = $this->_nextOccurrenceFromStartByDay();
							break;
						case GO_Base_Util_Date_RecurrencePattern::$INTERVAL_TYPE_MONTH :
							$occurrence = $this->_nextOccurrenceFromStartByMonth();
							break;
						case GO_Base_Util_Date_RecurrencePattern::$INTERVAL_TYPE_YEAR :
							$occurrence = $this->_nextOccurrenceFromStartByYear();
							break;
					}
					
					return $occurrence;
				}
				else {
					return false;
				}
			}
		}
	}
	
	
	/**
	 * Get the [$this->_interval] Day count from [$this->_start]
	 * 
	 * @return GO_Base_Util_Date_DateTime $nextOccurrence 
	 */
	private function _nextOccurrenceFromStartByDay()
	{	
		$nextOccurrence = '';
		
		return GO_Base_Util_Date_DateTime($nextOccurrence);
	}
	
	/**
	 * Get the [$this->_interval] Month count from [$this->_start]
	 * 
	 * @return GO_Base_Util_Date_DateTime $nextOccurrence 
	 */
	private function _nextOccurrenceFromStartByMonth()
	{
		$nextOccurrence = '';
		return GO_Base_Util_Date_DateTime($nextOccurrence);
	}
	
	/**
	 * Get the [$this->_interval] Year count from [$this->_start]
	 * 
	 * @return GO_Base_Util_Date_DateTime $nextOccurrence 
	 */
	private function _nextOccurrenceFromStartByYear()
	{
		$nextOccurrence = '';
		return GO_Base_Util_Date_DateTime($nextOccurrence);
	}
	
	
	
	
	
	
	
	
}