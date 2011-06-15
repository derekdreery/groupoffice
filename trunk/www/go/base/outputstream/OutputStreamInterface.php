<?php
interface GO_Base_OutputStream_OutputStreamInterface{
	
	public function sendHeaders();
	
	public function write($str);
	
}