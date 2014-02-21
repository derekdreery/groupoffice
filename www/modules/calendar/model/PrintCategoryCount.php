<?php

/**
 * Model that keeps the records of the categorycount print
 */
class GO_Calendar_Model_PrintCategoryCount extends GO_Base_Model {

	public $startDate;
	public $endDate;
	
	public $categories;
	public $calendars;
	
	private $_rows = array();
	private $_headers = array();
	private $_totals = array();
	
	/**
	 * Constructor
	 *  
	 * @param string $startDate
	 * @param string $endDate
	 */
	public function __construct($startDate,$endDate) {
		
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		
		$this->categories = GO_Calendar_Model_Category::model()->find()->fetchAll(); //GLOBAL
		$this->calendars = GO_Calendar_Model_Calendar::model()->find()->fetchAll();
	}
	
	/**
	 * Return an array of the headers of the table
	 * 
	 * @return array
	 */
	public function getHeaders(){
		
		if(empty($this->_headers)){
			$this->_headers[] = GO::t('calendars','calendar');

			foreach($this->categories as $cat){
				$this->_headers[] = $cat->name;
			}
		}
		return $this->_headers;
	}
	
	/**
	 * Get the table rows that need to be printed in the pdf
	 * 
	 * @return array
	 */
	public function getRows(){

		if(empty($this->_rows)){
			foreach($this->calendars as $calendar){

				$row = array();
				$row[] = $calendar->name;

				foreach($this->categories as $category){

					$findParams = GO_Base_Db_FindParams::newInstance();
					$findParams->select('COUNT(*) as count');
//					$findParams->ignoreAcl();							// Only count items that are visible for this user.
	//				$findParams->group('calendar_id');

					$findCriteria = GO_Base_Db_FindCriteria::newInstance();

					$findCriteria->addCondition('category_id', $category->id);
					$findCriteria->addCondition('calendar_id', $calendar->id);
					$findCriteria->addCondition('start_time', strtotime($this->startDate),'>');
					$findCriteria->addCondition('end_time', strtotime($this->endDate),'<');

					$findParams->criteria($findCriteria);

					$result = GO_Calendar_Model_Event::model()->find($findParams)->fetch();

					if(empty($result->count))
						$row[] = 0;
					else
						$row[] = $result->count;
				}

				$this->_rows[] = $row;
			}
		}

		return $this->_rows;
	}
	
	/**
	 * Get the total row as an array
	 * 
	 * @return array
	 */
	public function getTotals(){
		
		if(empty($this->_totals)){
			$rows = $this->getRows();
			
			$this->_totals[] =GO::t('total','calendar');
			
			foreach($rows as $row){
				$i = 1;
				foreach($row as $col){
					if($i > 1){
						if(!isset($this->_totals[$i]))
							$this->_totals[$i] = 0;
						
						$this->_totals[$i] += (int)$col; 
					}
					$i++;
				}
			}
		}

		return $this->_totals;
	}
}