<?php
namespace GO\Base\Model;

use \GO;
use GO\Base\Data\Store;
use GO\Base\Data\ColumnModel;

abstract class AbstractExport {
	
	/**
	 * Possible views
	 */
	const VIEW_HTML = 'Html';
	const VIEW_CSV	= 'Csv';
	const VIEW_PDF	= 'Pdf';
	const VIEW_XLS	= 'Xls';
	
	/**
	 * The columns that need to be exported
	 * 
	 * @var array 
	 */
	protected $_columns;
	
	/**
	 * The model for this export
	 * 
	 * @var GO\Base\Model 
	 */
	protected $_model;
	
	/**
	 * The key that is used to save the export
	 * 
	 * @var string 
	 */
	public $queryKey;
	
	/**
	 * Key value array to set custom labels for the columns
	 * 
	 * @var array
	 */
	public $labels = array();
	
	/**
	 * This are the columns that may not be exported
	 * 
	 * @var array 
	 */
	public $notExportableColumns = array(
			'password',
			'mUser.password',
			'user.password'
	);
	
	
	/**
	 * Give the columns that need to be exported to the constructor.
	 * This can be a string with comma separated columnnames or
	 * it can be an array with the column names.
	 * 
	 * @param mixed(string/array) $columns
	 */
	public function __construct($columns=false) {
		
		$this->_model = GO::getModel(GO::session()->values[$this->queryKey]['model']);
		
		if(!$columns){
			$this->_columns = array_keys($this->_model->getColumns());
		} else {
			if(is_array($columns)){		
				$this->_columns = $columns;
			} else {
				$this->_columns = explode(',',$columns);
			}
		}
	}
	
	/**
	 * Function that returns the views that are supported for the selected Export.
	 * Possible views: AbstractExport::VIEW_HTML,AbstractExport::VIEW_CSV,AbstractExport::VIEW_PDF,AbstractExport::VIEW_XLS
	 */
	public abstract function getSupportedViews();
		
	/**
	 * Grab the label for the given attribute.
	 * This also checks for the labels inside the relational fields
	 * 
	 * @param string $column
	 * @return string
	 */
	public function getLabel($column){
		
		if(in_array($column, array_keys($this->labels))){
			return $this->labels[$column];
		}

		$model = $this->getModel();
		
		if(strpos($column,'.')){
			
			$relationNames = explode('.',$column);
			$relationName = $relationNames[0];
			
			$relation = $model->getRelation($relationName);
			
			$relationModel = GO::getModel($relation['model']);
			
			return $relationModel->getAttributeLabel($relationNames[1]);
		} else {
			return $model->getAttributeLabel($column);
		}
	}
	
	
	/**
	 * Get the columnmodel for this export
	 * 
	 * @param array $columns
	 * @return \GO\Base\Model\ColumnModel
	 */
	public function getColumnModel($columns=false){
		$colModel = new ColumnModel();

		if(!$columns)
			$columns = $this->_columns;
		
		foreach($columns as $col){
			
//			$format = '$model->'.str_replace('.','->', $col);
			
			
			
			$format = '$model->resolveAttribute("'.$col.'","formatted");';

			
			$colModel->formatColumn($col, $format, array(), '', $this->getLabel($col));
			
		}
		
		return $colModel;
	}
	
	/**
	 * Get the model that is used in this export
	 * 
	 * @return ActiveRecord 
	 */
	public function getModel(){
		return $this->_model;
	}
	
	/**
	 * Get the name for the exported file
	 * 
	 * @return string
	 */
	public function getName(){
		return GO::session()->values[$this->queryKey]['name'];
	}
	
	/**
	 * Get the findParams for this export.
	 * They will be pulled from the grid session.
	 * 
	 * @return \GO\Base\Db\FindParams
	 */
	public function getFindParams(){
		$findParams = GO::session()->values[$this->queryKey]['findParams'];
		$findParams->limit(0); // Let the export handle all found records without a limit
		$findParams->getCriteria()->recreateTemporaryTables();
		$findParams->selectAllFromTable('t');
		
		return $findParams;
	}
	
	/**
	 * Get the store that is needed for this export.
	 * 
	 * @return \GO\Base\Data\Store
	 */	
	public function getStore(){
		return new Store($this->getColumnModel());
	}
	
	/**
	 * Get the columns of the fields that are related
	 * 
	 */
	protected function _getRelatedColumns(){
		$relatedColumns = array();
		
		$model = $this->getModel();
		$relations = $model->getRelations();

		foreach($relations as $rKey=>$relation){
			
			if($relation['type'] === $model::BELONGS_TO){
				//$rKeys = $model->findRelationsByColumnName($relation['field'],array($model::BELONGS_TO));

				if(GO::classExists($relation['model'])){
					$relatedModel = GO::getModel($relation['model']);

					//foreach($rKeys as $rKey){

						$rCols = $relatedModel->getColumns();

						foreach($rCols as $rColName=>$rCol){
							$relatedColumns[] = array('id'=>$rKey.'.'.$rColName,'name'=>$rKey.'.'	.$rColName,'label'=>$relatedModel->getAttributeLabel($rColName), 'field_id'=>$relation['field']);
						}
					//}
				}
			}
		}
		
		
		return $relatedColumns;
		
	}
	
		/**
	 * Return the array with the columns that could be exported
	 * 
	 * @return array
	 */
	public function getColumns(){
		$aColumns= $this->_model->getColumns();
		
		$relatedColumns = $this->_getRelatedColumns();
			
		$availableColumns = array();
		foreach($aColumns as $name=>$column){
			//if(!$this->_checkRelatedColumn($name,$relatedColumns)){
				$availableColumns[] = array('id'=>$name,'name'=>$name,'label'=>$this->_model->getAttributeLabel($name));
			//}
		}
		
		//var_dump($availableColumns);
		
		$availableColumns = array_merge($availableColumns,  array_values($relatedColumns));		
		
		// Remove columns that are not exportable
		foreach($this->notExportableColumns as $notExp){
			
			foreach($availableColumns as $key=>$ac){
				if(isset($ac['id']) && $ac['id']===$notExp){
					unset($availableColumns[$key]);
				}
			}
		}
		
		sort($availableColumns);
		
		return $availableColumns;
	}
	
	/**
	 * Check if the current columnname is a columnname for a related column
	 * 
	 * @param string $name
	 * @param array $relatedColumns
	 * @return boolean
	 */
	protected function _checkRelatedColumn($name,$relatedColumns){
		
		foreach ($relatedColumns as $relatedColumn){
			
			if($relatedColumn['field_id'] === $name)
				return true;
			
		}
		
		return false;
	}
	
}