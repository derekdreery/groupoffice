<?php

class GO_Site_Widget_Pager extends GO_Site_Components_Widget {
	
	/**
	 * A prefix for the pager parameter
	 * @var string 
	 */
	protected $requestPrefix = '';

	/**
	 * A store object that is responsable for fetchin results
	 * 
	 * @var GO_Base_Data_DbStore 
	 */
	protected $store;
	
	/**
	 * The classname for the previous link
	 * @var string 
	 */
	public $previousPageClass = 'previous';
	
	/**
	 * The classname for the previous link
	 * @var string 
	 */
	public $nextPageClass = 'next';
	
	/**
	 * The number of item on 1 page
	 * If not specified it will take the number from the GO config.php
	 * @var int 
	 */
	protected $pageSize;
	
	/**
	 * the current page number
	 * @var int 
	 */
	public $currentPage=1;
	
	
	protected $currentPageClass = 'current';
	
	protected $pageParam = 'p';
	
	private $_models;
	
	
	/**
	 * Constructor for the pagination
	 * 
	 * @param mixed $dataobject an ActiveStatement or a DbStore for data reading
	 * @param array $config key value array with config options for widget.
	 * @param GO_Base_Db_FindParams $findParams Findparams to find the correct models.
	 */
	public function __construct(GO_Base_Data_AbstractStore $store, $config=array()){		

		foreach($config as $key => $value)
			$this->{$key} = $value;

		if(isset($_GET[$this->pageParam]))
			$this->currentPage = $_GET[$this->pageParam];
		
		$this->store = $store;
		
		if(empty($this->pageSize))
			$this->pageSize = GO::config()->nav_page_size;
			
		$this->store->start = $this->pageSize * ($this->currentPage-1);
		$this->store->limit = $this->pageSize;
	}
	
	/**
	 * The current active page
	 * @return integer
	 */
	public function getCurrentPage() {
		return $this->store->start / $this->store->limit;
	}
	
	/**
	 * Total item count of all pages
	 * @return The total items found in the database
	 */
	public function getTotalItems() {
		return $this->store->getTotal();
	}
	
	/**
	 * Get the total number of pages
	 * @return int
	 */
	public function getPageCount() {
		return (int)(($this->store->getTotal()+$this->pageSize-1)/$this->pageSize);
	}

	/**
	 * The link for the page with the given number
	 * @param int $pageNum number of page
	 * @return string URL to page
	 */
	private function getPageUrl($pageNum){
		$params = array_merge($_GET,array($this->requestPrefix.$this->pageParam=>$pageNum));
		return site::urlManager()->createUrl(Site::router()->getRoute(), $params);
	}
	
	/**
	 * Render the pagination.
	 */
	public function render($return = false){

		$result = '';
		if($this->currentPage != 1)
			$result.= '<a class="'.$this->nextPageClass.'" href="'.$this->getPageUrl($this->currentPage-1).'"><</a>';

		for($page=1;$page<=$this->pageCount;$page++)
			$result.= ($page == $this->currentPage) ? '<span class="'.$this->currentPageClass.'">'.$page.'</span>' : '<a href="'.$this->getPageUrl($page).'">'.$page.'</a>';

		if($this->currentPage < $this->pageCount)
			$result.= '<a class="'.$this->previousPageClass.'" href="'.$this->getPageUrl($this->currentPage+1).'">></a>';
		if($return)
			return $result;
		else
			echo $result;
	}
	
	/**
	 * get an array of models with item on the current page
	 * @return array with active records
	 */
	public function getItems() {
		if(empty($this->_models))
			$this->_models = $this->store->getModels();
		return $this->_models;
	}
	
}