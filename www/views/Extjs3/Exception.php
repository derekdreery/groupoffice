<?php
if($this->isAjax()){
	echo json_encode($data);
}else
{
	echo '<h1>Error</h1>';
	echo '<p style="color:red">'.$data['feedback'].'</p>';
	if(GO::config()->debug)
		echo '<pre>'.$data['trace'].'</pre>';
}