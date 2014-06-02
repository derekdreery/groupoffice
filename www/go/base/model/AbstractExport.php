<?php
namespace GO\Base\Model;

use \GO;
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
	 * The key that is used to save the export
	 * 
	 * @var string 
	 */
	public $queryKey;
	
	public $labels = array();
	
	/**
	 * Function that returns the views that are supported for the selected Export.
	 * Possible views: AbstractExport::VIEW_HTML,AbstractExport::VIEW_CSV,AbstractExport::VIEW_PDF,AbstractExport::VIEW_XLS
	 */
	public abstract function getSupportedViews();
	
	public abstract function getData();
	
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
	public function getColumnModel($columns){
		$colModel = new ColumnModel();
		
		foreach($columns as $col){
			
			$format = '$model->'.str_replace('.','->', $col);
			
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
		
		$model = GO::session()->values[$this->queryKey]['model'];
		
		return GO::getModel($model);
	}
	
	/**
	 * Get the columns of the fields that are related
	 * 
	 */
	protected function _getRelatedColumns(){
		$relatedColumns = array();
		
		$model = $this->getModel();
		$relations = $model->getRelations();
		
		foreach($relations as $relation){
			
			if($relation['type'] === $model::BELONGS_TO){
				$rKeys = $model->findRelationsByColumnName($relation['field'],array($model::BELONGS_TO));
				$relatedModel = new $relation['model'];
				
				foreach($rKeys as $rKey){
					
					$rCols = $relatedModel->getColumns();

					foreach($rCols as $rColName=>$rCol){
						//$relatedColumns[] = array('id'=>$relation['field'].'.'.$rColName,'name'=>$relation['field'].'.'	.$rColName,'label'=>$relatedModel->getAttributeLabel($rColName));
						$relatedColumns[] = array('id'=>$rKey.'.'.$rColName,'name'=>$rKey.'.'	.$rColName,'label'=>$relatedModel->getAttributeLabel($rColName), 'field_id'=>$relation['field']);
					}
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
		$aColumns= $this->getModel()->getColumns();
		
		$relatedColumns = $this->_getRelatedColumns();
			
		$availableColumns = array();
		foreach($aColumns as $name=>$column){
			if(!$this->_checkRelatedColumn($name,$relatedColumns)){
				$availableColumns[] = array('id'=>$name,'name'=>$name,'label'=>$this->getModel()->getAttributeLabel($name));
			}
		}
		
		$availableColumns = array_merge($availableColumns,  array_values($relatedColumns));		
		
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