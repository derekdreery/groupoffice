<?php
abstract class GO_Base_Model extends GO_Base_Observable{
	
	
	private static $_models=array();			// class name => model
	
	/**
	 * Returns the static model of the specified AR class.
	 * Every child of this class must override it.
	 * 
	 * @return GO_Base_Model the static model class
	 */
	public static function model($className=__CLASS__)
	{		
		if(isset(self::$_models[$className]))
			return self::$_models[$className];
		else
		{
			$model=self::$_models[$className]=new $className(false);
			return $model;
		}
	}
	
	
}