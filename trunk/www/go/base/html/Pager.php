<?php
class GO_Base_Html_Pager extends GO_Base_Html_Component {
	
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
	 *
	 * @var GO_Base_Db_ActiveStatement 
	 */
	private $_stmt;
	
	/**
	 * Constructor for the pagination
	 * 
	 * @param int $id The identifier for this pagination component. (This is a string e.g." paginationOne)
	 * @param GO_Sites_Model_Page $page The page model on where this pagination component is used
	 * @param mixed $model The model to create this pagination for.
	 * @param GO_Base_Db_FindParams $findParams Findparams to find the correct models.
	 * @param int $limit The limiter for how many models will be showed on each page
	 * @param int $offset The number of pages that will be showed before and after the current showed page. e.g. 
	 *	Number 2 will create [2][3][4=current][5][6]
	 *  Number 3 will create [1][2][3][4=current][5][6][7]
	 */
	public function __construct($id, GO_Sites_Model_Page $page, $model, GO_Base_Db_FindParams $findParams, $limit=10, $offset=0){		
		
		$this->_page = $page;
		$this->_id=$id;
		$this->_currentPageNumber = isset($_REQUEST[$this->getRequestParam()]) ? $_REQUEST[$this->getRequestParam()] : 1;
		$this->limit = $limit;
		$this->offset = $offset;
				
		$findParams->limit($this->limit);
		$findParams->calcFoundRows();
		$findParams->start(($this->_currentPageNumber-1)*$this->limit);
		
		$this->_stmt=$model->find($findParams);
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
	

		echo '<table class="pager-table">';
			echo '<tr>';
			
				// START: RENDER THE PAGER PREVIOUS ARROWS 
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == 1)
						echo '<<';
					else
						echo '<a href="'.$this->_page->getUrl(array($this->getRequestParam()=>1)).'"><<</a>';
				echo '</td>';
				
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == 1)
						echo '<';
					else
						echo '<a href="'.$this->_page->getUrl(array($this->getRequestParam()=>$previous)).'"><</a>';
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
					if($page == $this->_currentPageNumber)
						echo '<td class="pager-block pager-active"><a href="'.$this->_page->getUrl(array($this->getRequestParam()=>$page)).'">'.$page.'</a></td>';
					else
						echo '<td class="pager-block pager-inactive"><a href="'.$this->_page->getUrl(array($this->getRequestParam()=>$page)).'">'.$page.'</a></td>';
				}	
				// END: RENDER THE PAGE NUMBER BLOCKS
				
				// START: RENDER THE PAGER NEXT ARROWS 
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == $this->_totalPages)
						echo '>';
					else
						echo '<a href="'.$this->_page->getUrl(array($this->getRequestParam()=>$next)).'">></a>';
				echo '</td>';
				
				echo '<td class="pager-block pager-inactive">';
					if($this->_currentPageNumber == $this->_totalPages)
						echo '>>';
					else
						echo '<a href="'.$this->_page->getUrl(array($this->getRequestParam()=>$this->_totalPages)).'">>></a>';
				echo '</td>';
				// END: RENDER THE PAGER NEXT ARROWS
				
				
			echo '</tr>';
		echo '</table>';

		
	}
	
	/**
	 * Render a Total items block.
	 * This will render a table
	 */
	public function renderTotalFound(){
		echo '<table class="pager-totalfound-table">';
			echo '<tr>';
				echo '<td>Total items: '.$this->_totalFound.'</td>';
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
				echo '<td>Page: '.$this->_currentPageNumber.' of '.$this->_totalPages.'</td>';
			echo '</tr>';
		echo '</table>';
	}
	
}