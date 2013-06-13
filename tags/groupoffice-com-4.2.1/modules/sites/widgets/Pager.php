<?php
class GO_Sites_Widgets_Pager extends GO_Sites_Widgets_Component {
	
	/**
	 * The limit of models per page
	 * @var int 
	 */
	public $limit=10;
	
	/**
	 * The offset for how many pages will be showed before and after the current page.
	 * @var int 
	 */
	public $offset=0;
	
	/**
	 * The array of the models that are found by the searchquery
	 * @var array 
	 */
	public $models = array();
	
	/**
	 * A prefix for the pager parameter
	 * @var string 
	 */
	private $_requestPrefix = '';
	
	/**
	 * The current page number
	 * @var int 
	 */
	private $_currentPageNumber=1;
	
	/**
	 * The total of models that are found
	 * @var int 
	 */
	private $_totalFound=0;
	
	/**
	 * The total of pages that are found
	 * @var int
	 */
	private $_totalPages=0;
	
	/**
	 * The activestatement that this component uses.
	 * 
	 * @var GO_Base_Db_ActiveStatement 
	 */
	private $_stmt;
	
	/**
	 * The findparams for this component
	 * 
	 * @var GO_Base_Db_FindParams 
	 */
	private $_findParams;
	
	/**
	 * The model for this component
	 * 
	 * @var GO_Base_Db_ActiveRecord 
	 */
	private $_model;
	
	
	/**
	 * Constructor for the pagination
	 * 
	 * @param int $id The identifier for this pagination component. (This is a string e.g." paginationOne)
	 * @param GO_Sites_Controller_Site $controller the page model on where this pagination component is used
	 * @param mixed $model The model to create this pagination for.
	 * @param GO_Base_Db_FindParams $findParams Findparams to find the correct models.
	 * @param int $limit The limiter for how many models will be showed on each page
	 * @param int $offset The number of pages that will be showed before and after the current showed page. e.g. 
	 *	Number 2 will create [2][3][4=current][5][6]
	 *  Number 3 will create [1][2][3][4=current][5][6][7]
	 */
	public function __construct($id, $params, $model, GO_Base_Db_FindParams $findParams, $limit=10, $offset=0){		

		$this->limit = $limit;
		$this->offset = $offset;
		$this->_findParams = $findParams;
		$this->_model = $model;

		parent::__construct($id, $params);
		
		$this->_currentPageNumber = isset($params[$this->getRequestParam()]) ? $params[$this->getRequestParam()] : 1;
		GOS::site()->scripts->registerCssFile(GOS::site()->controller->getTemplateUrl().'css/pager.css');  // Include the right css file in the header

		$this->_initialize();
	}
	
	private function _initialize() {
		$this->_findParams->limit($this->limit);
		$this->_findParams->calcFoundRows();
		$this->_findParams->start(($this->_currentPageNumber-1)*$this->limit);
		
		$this->_stmt=$this->_model->find($this->_findParams);
		while($model = $this->_stmt->fetch()){
			$this->models[] = $model;
		}
	}
	
	/**
	 * Get the paramater to let this pager paginate througt the found pages.
	 * 
	 * @return string 
	 */
	public function getRequestParam(){
		return $this->_requestPrefix.$this->_id;
	}
	
	/**
	 * Get the statement that is used to create the pagination
	 * 
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function getStatement(){
		return $this->_stmt;
	}
	
	private function getPageUrl($pageNum){
		$params = array_merge($this->getAdditionalParams(),array($this->getRequestParam()=>$pageNum));
		
		$params = array_merge($_GET,$params);
		
		return GOS::site()->getController()->createUrl(GOS::site()->route, $params);
	}
	
	/**
	 * Render the pagination table.
	 */
	public function render(){
		$this->_totalFound = $this->_stmt->foundRows;
		
		$this->_totalPages = ceil($this->_totalFound / $this->limit);

		if($this->_currentPageNumber > $this->_totalPages)
			$this->_currentPageNumber = $this->_totalPages;
			
		if($this->_currentPageNumber < 1)
			$this->_currentPageNumber = 1;
		
		$previous = $this->_currentPageNumber-1;
		$next = $this->_currentPageNumber+1;
	
		echo '<div class="pager-container">';
		echo '<table class="pager-table">';
			echo '<tr>';
			
				// START: RENDER THE PAGER PREVIOUS ARROWS 
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == 1)
						echo '<<';
					else
						echo '<a href="'.$this->getPageUrl(1).'"><<</a>';
				echo '</td>';
				
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == 1)
						echo '<';
					else
						echo '<a href="'.$this->getPageUrl($previous).'"><</a>';
				echo '</td>';
				// END: RENDER THE PAGER PREVIOUS ARROWS
				
				// START: RENDER THE PAGE NUMBER BLOCKS
				if($this->offset > 0){
					$offsetStart = $this->_currentPageNumber - $this->offset;
					if($offsetStart < 1)
						$offsetStart = 1;
					$offsetEnd = $this->_currentPageNumber + $this->offset;
					if($offsetEnd > $this->_totalPages)
						$offsetEnd = $this->_totalPages;
				}
				else{
					$offsetStart = 1;
					$offsetEnd = $this->_totalPages;
				}

				for($page=$offsetStart;$page<=$offsetEnd;$page++){
		
					$url = $this->getPageUrl($page);
					if($page == $this->_currentPageNumber) {
						echo '<td class="pager-block pager-active"><a href="'.$url.'">'.$page.'</a></td>';
					} else
						echo '<td class="pager-block pager-inactive"><a href="'.$url.'">'.$page.'</a></td>';
				}	
				// END: RENDER THE PAGE NUMBER BLOCKS
				
				// START: RENDER THE PAGER NEXT ARROWS 
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == $this->_totalPages)
						echo '>';
					else
						echo '<a href="'.$this->getPageUrl($next).'">></a>';
				echo '</td>';
				
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == $this->_totalPages)
						echo '>>';
					else
						echo '<a href="'.$this->getPageUrl($this->_totalPages).'">>></a>';
				echo '</td>';
				// END: RENDER THE PAGER NEXT ARROWS
				
				
			echo '</tr>';
		echo '</table>';
		echo '</div>';

		
	}
	
	/**
	 * Render a Total items block.
	 * This will render a table
	 */
	public function renderTotalFound(){
		echo '<table class="pager-totalfound-table">';
			echo '<tr>';
				echo '<td>'.GOS::site()->getController()->t('pager_totalItems').': '.$this->_totalFound.'</td>';
			echo '</tr>';
		echo '</table>';
	}
	
	/**
	 * Render a Total pages block.
	 * This will render a table
	 */
	public function renderTotalPages(){
		echo '<table class="pager-totalpages-table">';
			echo '<tr>';
				echo '<td>'.GOS::site()->getController()->t('pager_page').': '.$this->_currentPageNumber.' '.$this->_controller->t('pager_of').' '.$this->_totalPages.'</td>';
			echo '</tr>';
		echo '</table>';
	}
	
}