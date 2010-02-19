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
	var $name='OSF gastouders';

	/**
	 * Optionally hardcode the list and text separator in the constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->list_separator="\t";
		$this->text_separator='';
	}

	/**
	 * Format the raw database record.
	 */

	function format_record(&$record){
		$record['veld6']='T';
		$record['veld8']='False';
		$record['btime']=date('d/m/Y', $record['btime']);
	}

	/**
	 * Initialize the columns.
	 * Some example custom fields are used here
	 */

	function init_columns(){
		$crediteurnummer = $this->cf->find_first_field_by_name(7, 'Crediteurnummer');
		$tegenrekening = $this->cf->find_first_field_by_name(7, 'Tegenrekening');

		$this->columns=array('order_id','btime','customer_name', 'col_'.$crediteurnummer['id'],'col_'.$tegenrekening['id'],'veld6', 'total', 'veld8');
		$this->headers=array();
	}
}
