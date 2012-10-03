<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * The ArrayStore provider is useful to generate arrays for output to the view.
 * 
 * @version $Id: ArrayStore.php 7607 2011-08-04 13:41:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.data
 */
class GO_Base_Data_ArrayStore extends GO_Base_Data_AbstractStore {

	
	public function getData() {
		$this->response['total']=$this->getTotal();
		return $this->response;
	}
	
	public function getTotal() {
		return count($this->response['results']);
	}
	
	public function nextRecord() {
		return next($this->response['results']);
	}
}