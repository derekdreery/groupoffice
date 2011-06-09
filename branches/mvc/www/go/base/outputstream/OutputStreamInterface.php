<?php
interface GO_Base_OutputStream_OutputStreamInterface{
	
	public function getHeaders();
	
	public function write($str);
	
}