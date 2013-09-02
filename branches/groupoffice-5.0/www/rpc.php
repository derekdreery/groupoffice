<?php
require('GO.php');

function rpcRouter($method, $param_arr, $data=null){
	GO::debug($method.'('.var_export($param_arr, true).')');
	
	$parts = explode('_',$method);
	
	$class = "GO_".ucfirst(array_shift($parts))."_Rpc_".ucfirst( array_shift($parts));
	$method = implode('_',$parts);
	
//	if(!class_exists($class) || !method_exists($class, $method)){
//		return 'notfound';
//	}
	
	return call_user_func_array(array($class, $method), $param_arr);
}

$xmlrpcServer = xmlrpc_server_create();

$classes = GO::modules()->findClasses('rpc');

foreach($classes as $class){
	
	$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);
	
	foreach($methods as $method){
		
		$parts = explode('_',$class->getName());
		$methodName = strtolower($parts[1]).'_'. strtolower($parts[3]).'_'.$method->getName();
		xmlrpc_server_register_method($xmlrpcServer, $methodName, "rpcRouter");
	}
	
}


//xmlrpc_server_register_method($xmlrpcServer, 'voipro_hello', array('voipro','hello'));

$response = xmlrpc_server_call_method($xmlrpcServer, file_get_contents('php://input'), null); 

header('Content-Type: text/xml;charset=utf-8');

GO::debug($response);

print $response;

xmlrpc_server_destroy($xmlrpcServer);