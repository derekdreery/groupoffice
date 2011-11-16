<?php
abstract class GO_Base_Export_AbstractExport {
	
	/**
	 *
	 * @var GO_Base_Data_Store
	 */
	protected $store;
	
	/**
	 *
	 * @var GO_Base_Data_ColumnModel 
	 */
	protected $columnModel;
	
	/**
	 *
	 * @var Boolean 
	 */
	protected $header;
	
	/**
	 *
	 * @var String 
	 */
	protected $title;
	
	/**
	 * 
	 * @var String 
	 */
	protected $orientation;
	
	
	public static $showInView=false;
	public static $name="No name given";
	public static $useOrientation=false;
	
	public function __construct($store, $columnModel, $header=true, $title=false, $orientation=false) {
		$this->store = $store;
		$this->columnModel = $columnModel;
		$this->header = $header;
		$this->title = $title;
		$this->orientation = $orientation;
	}
	
	public function getLabels(){
		$columns = $this->columnModel->getColumns();
		$labels = array();
		foreach($columns as $column)			
			$labels[]=$column->getLabel();
		
		return $labels;
	}
	
	/**
	 * Output's all data to the browser.
	 */
	abstract public function output();
	
}