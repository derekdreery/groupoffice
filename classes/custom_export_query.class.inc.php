<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Date.class.inc.php 3589 2009-11-05 13:02:37Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * Example override for a custom export
 */
class custom_export_query extends export_query
{
	var $query='orders';
	var $name='osFinancials';

	function __construct()
	{
		parent::__construct();

		$this->list_separator="\t";
		$this->text_separator='';
	}

	function format_record(&$record){
		$record['customer_no']='???';
		$record['tegenrekening']='<T>';
		$record['veld6']='<T>';
		$record['veld8']='False';
		$record['btime']=date('d/m/Y', $record['btime']);
	}

	function init_columns(){
		$this->columns=array('order_id','btime','customer_name', 'customer_no','tegenrekening','veld6', 'total', 'veld8');
		$this->headers=array();
	}
}