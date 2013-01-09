<?php
class GO_Linkedin_Model_ImportedContact extends GO_Addressbook_Model_Contact {
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function createImportedContactModel($importedContactInfo,$addressbookId) {
		
		$contactsStmt = self::model()->findByAttributes(array(
				'first_name' => $importedContactInfo['first_name'],
				'addressbook_id' => $addressbookId
			));
		
		$foundContactModel = false;
		
		while ($contactModel = $contactsStmt->fetch() ) {
			if ($contactModel->getName('first_name') === $importedContactInfo['first_name'].' '.$importedContactInfo['last_name']) {
				$foundContactModel = $contactModel;
				break;
			}
		}
		
		if (!$foundContactModel) {
			$foundContactModel = new GO_Addressbook_Model_Contact();
			$foundContactModel->addressbook_id = $addressbookId;
			$foundContactModel->first_name = $importedContactInfo['first_name'];
			$foundContactModel->last_name = $importedContactInfo['last_name'];
		}
		
		if (empty($foundContactModel->city))
			$foundContactModel->city = $importedContactInfo['city'];
		
		if (empty($foundContactModel->photo) && !empty($importedContactInfo['photoUrl'])) {
			
			$imgInfo = getimagesize($importedContactInfo['photoUrl']);
			
			switch ($imgInfo[2]) {
				case IMAGETYPE_JPEG:
					$extension = 'jpg';
					break;
				case IMAGETYPE_BMP:
					$extension = 'bmp';
					break;
				case IMAGETYPE_GIF:
					$extension = 'gif';
					break;
				case IMAGETYPE_PNG:
					$extension = 'png';
					break;
				default:
					$extension = '';
					break;
			}
			
			if (!empty($extension)) {
			
				$handle = fopen($importedContactInfo['photoUrl'],'r');

				$imgContents = '';
				while ($line = fread($handle,1024)) 
					$imgContents .= $line;

				fclose($handle);

				$tmpFile = new GO_Base_Fs_File(GO::config()->tmpdir.'contact_photo.'.$extension);
				$tmpFile->putContents($imgContents);
				$foundContactModel->setPhoto($tmpFile);
				
			}
			
		}
		
		return $foundContactModel;
		
	}
	
}
?>
