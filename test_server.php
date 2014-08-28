<?php
//give a try
ini_set("soap.wsdl_cache_enabled", 0);
error_reporting(E_ERROR | E_PARSE);

require_once("lib/nusoap/nusoap.php");
require 'kernel/helper.php';
require_once 'model/chat.php';
	
// create a new soap server
$server = new soap_server();
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
$server->debug_flag = true;


$model = new chat();

// configure our WSDL
$server->configureWSDL("testservice");

$namespace = "http://webservice/test_server.php";

// set our namespace
$server->wsdl->schemaTargetNamespace = $namespace;



/**
 * Test Method2
 */
$server->register(
                // method name:
                'HelloComplexWorld', 	
                // parameter list:
                array('name'=>'tns:MyComplexType'), 
                // return value(s):
                array('return'=>'tns:MyComplexType'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'Complex Hello World Method');

                
                
/**
 * Test Method Input Param
 */
$server->wsdl->addComplexType('MyComplexType','complexType','struct','all','',
		array( 'ID' => array('name' => 'ID','type' => 'xsd:int'),
			   'YourName' => array('name' => 'YourName','type' => 'xsd:string')));
                

/**
 * Test method2
 * @param $mycomplextype
 */
function HelloComplexWorld($mycomplextype)
{
	//helper::writelog($mycomplextype);	
	
	return $mycomplextype;	
}


// Get our posted data if the service is being consumed
// otherwise leave this data blank.                
$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:'';

// pass our posted data (or nothing) to the soap service                    
$server->service($POST_DATA);                
?>