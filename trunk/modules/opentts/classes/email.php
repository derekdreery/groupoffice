<?php
#$email= new Email("Meir Michanie","meirm@riunx.com","meirm","hi there","low here");

class Email{
	function Email($from,$femail,$to,$subject,$body){
		$headers = "From: $from <$femail>\n";
		$headers .= "Reply-To: $from <$femail>\n";
		$headers .= "X-Sender: $from <$femail>\n";
		$headers .= "X-Mailer: PHP4\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "Return-Path: <$femail>\n";
		if(mail($to,$subject,$body,$headers))
			return TRUE;
		else
			return FALSE;
	}
}
	
		
?>

