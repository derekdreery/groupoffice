<?php
$accountsStmt = GO_Email_Model_Account::model()->find();
while ($accountModel = $accountsStmt->fetch()) {
	if (!empty($accountModel->smtp_password)) {
		// Trick the model this field has been modified, to circumvent
		$pwBuffer = $accountModel->smtp_password;
		$accountModel->smtp_password = "";
		$accountModel->smtp_password = $pwBuffer;
		$accountModel->save();
	}
}
?>
