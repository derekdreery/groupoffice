<?php
/**
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 *
 */

/**
 * Class to export Contacts with companies together to a .csv file. 
 */
class GO_Addressbook_Controller_ExportContactsWithCompanies extends GO_Base_Controller_AbstractExportController{
	
	/**
	 * Export the contact model to a .csv, including the company.
	 * 
	 * @param array $params
	 */
	public function export($params) {
		
		GO::$disableModelCache=true;		
		GO::setMaxExecutionTime(180);
		
		// Load the data from the session.
		$findParams = GO::session()->values['contact']['findParams'];
		$findParams->getCriteria()->recreateTemporaryTables();
		$model = GO::getModel(GO::session()->values['contact']['model']);
		
		// Include the companies
		$findParams->joinRelation('company','LEFT');
						
		// Let the export handle all found records without a limit
		$findParams->limit(0); 

		// Create the statement
		$stmt = $model->find($findParams);
		
		// Create the csv file
		$csvFile = new GO_Base_Fs_CsvFile(GO_Base_Fs_File::stripInvalidChars('export.csv'));
		
		// Output the download headers
		GO_Base_Util_Http::outputDownloadHeaders($csvFile, false);
				
		$csvWriter = new GO_Base_Csv_Writer('php://output');
		
		$headerPrinted = false; 
		
		foreach($stmt as $m){
			
			$attrs = $m->getAttributes();
			$compAttrs = $m->company->getAttributes();

			$header = array();
			$record = array();
			foreach($attrs as $attr=>$val){
				$header[$attr] = $m->getAttributeLabel($attr);
				$record[$attr] = $m->{$attr};
			}
			
			foreach($compAttrs as $cattr=>$cval){
				$header[GO::t('company','addressbook').$cattr] = GO::t('company','addressbook').':'.$m->company->getAttributeLabel($cattr);
				$record[GO::t('company','addressbook').$cattr] = $m->company->{$cattr};
			}
			
			if(!$headerPrinted){
				$csvWriter->putRecord($header);
				$headerPrinted = true;
			}
			
			$csvWriter->putRecord($record);
		}
	}
	
	/**
	 * Return an empty array because we don't select attributes
	 * 
	 * @return array
	 */
	public function exportableAttributes() {
		return array();
	}
}