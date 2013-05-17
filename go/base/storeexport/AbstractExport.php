<?php
abstract class GO_Base_Storeexport_AbstractExport {
	
	/**
	 *
	 * @var GO_Base_Data_AbstractStore
	 */
	protected $store;
	
	/**
	 *
	 * @var Boolean 
	 */
	protected $header;
	
	/**
	 *
	 * @var Boolean 
	 */
	protected $humanHeaders = true;
	
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
	
	/**
	 * Display the exporter in the exportDialog?
	 * @var Boolean 
	 */
	public static $showInView=false;
	
	/**
	 * The name that will be displayed in the frontend for this exporter.
	 * 
	 * @var String 
	 */
	public static $name="No name given";

	/**
	 * Can the orientation of this exporter be given by the front end user?
	 * 
	 * @var Boolean 
	 */
	public static $useOrientation=false;
	
	/**
	 * The constructor for the exporter
	 * 
	 * @param GO_Base_Data_Store $store
	 * @param GO_Base_Data_ColumnModel $columnModel
	 * @param Boolean $header
	 * @param Boolean $humanHeaders
	 * @param String $title
	 * @param Mixed $orientation ('P' for Portrait,'L' for Landscape of false for none) 
	 */
	public function __construct(GO_Base_Data_AbstractStore $store, $header=true,$humanHeaders=true, $title=false, $orientation=false) {
		$this->store = $store;
		$this->header = $header;
		$this->title = $title;
		$this->orientation = $orientation;
		$this->humanHeaders= $humanHeaders;

	}

	
	/**
	 * Return an array with all the labels of the columns
	 * 
	 * @return array 
	 */
	public function getLabels(){
		$columns = $this->store->getColumnModel()->getColumns();
		$labels = array();
		foreach($columns as $column)		
			$labels[$column->getDataIndex()]=$column->getLabel();
		
		return $labels;
	}
	
	protected function prepareRecord($record){
		$c = array_keys($this->getLabels());
		$frecord = array();
		
		foreach($c as $key){
			$frecord[$key] = html_entity_decode($record[$key]);
		}

		return $frecord;
	}
	
	/**
	 * Output's all data to the browser.
	 */
	abstract public function output();
	
}