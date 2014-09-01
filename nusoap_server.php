<?php
ini_set("soap.wsdl_cache_enabled", 0);
error_reporting(E_ERROR);

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
$server->configureWSDL("chatservice");

$namespace = "http://webservice/nusoap_server.php";

// set our namespace
$server->wsdl->schemaTargetNamespace = $namespace;

/**
 * ObserveOperatorCommitment check if operator has agreed a chatdialog
 * @param $user_hash
 * @return array(status, operator_hash, dialog_hash);
 * 
 */
$server->register(
                // method name:
                'ObserveOperatorCommitment', 	
                // parameter list:
                array('dialog_hash'=>'xsd:string'), 
                // return value(s):
                array('return'=>'tns:opOperatorAnddialog_hash'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'Observe if operator is online and return operator_hash, dialog_hash, status.');

                
/**
 * ObserveOperatorCommitment Return Param: array(status, operator_hash, dialog_hash)
 */
$server->wsdl->addComplexType('opOperatorAnddialog_hash','complexType','struct','all','',
		array('status' => array('name' => 'status','type' => 'xsd:string'),
			  'operator_hash' => array('name' => 'operator_hash','type' => 'xsd:string'),
			  'dialog_hash' => array('name' => 'dialog_hash','type' => 'xsd:string')));               
 
		
/**
 * SendChatMessage makes insert in message table
 * @param array (dialog_hash, message)
 */
$server->register(
                // method name:
                'SendMessage', 	
                // parameter list:
                array('name'=>'tns:ipMessageparams'), 
                // return value(s):
                array('return'=>'xsd:boolean'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'send new offline message');

/**
 * Send Offline Message Input Param: array(user_hash, message)
 */
$server->wsdl->addComplexType('ipMessageparams','complexType','struct','all','',
		array('user_hash' => array('name' => 'user_hash','type' => 'xsd:string'),
			   'message' => array('name' => 'message','type' => 'xsd:string')));
		
/**
 * SendChatMessage makes insert in message table
 * @param array (dialog_hash, message)
 */
$server->register(
                // method name:
                'SendChatMessage', 	
                // parameter list:
                array('name'=>'tns:chatmessageparams'), 
                // return value(s):
                array('return'=>'xsd:boolean'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'send new chatmessage');
/**
 * SendChatMessage Input Param: array(dialog_hash, message)
 */
$server->wsdl->addComplexType('chatmessageparams','complexType','struct','all','',
		array('dialog_hash' => array('name' => 'dialog_hash','type' => 'xsd:string'),
			   'message' => array('name' => 'message','type' => 'xsd:string')));
                

/**
 * GetNewChatMessages, returns all chatmessages since given timestamp and for the appropriated sendby hash
 */
$server->register(
                // method name:
                'GetNewChatMessages', 	
                // parameter list:
                array('name'=>'tns:ipNewChatMessages'), 
                // return value(s):
                array('return'=>'tns:ChatMessages'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'Get new chat messages since given timestamp and for appropriated sendby hash');		

                
                /**
 * GetAllChatMessages, returns all chatmessages for the appropriated dialog hash
 */
$server->register(
                // method name:
                'GetAllChatMessages', 	
                // parameter list:
                //array('name'=>'tns:ipGetAllChatMessages'), 
                array('dialog_hash' => 'xsd:string'),
                // return value(s):
                array('return'=>'tns:ChatMessages'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'Get new chat messages since given timestamp and for appropriated dialog hash');		
		
		
/**
 * Test Method
 */
$server->register(
                // method name:
                'Test', 	
                // parameter list:
                array(), 
                // return value(s):
                array(),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'testmethod');

/**
 * Test Method Input Param
 */
$server->wsdl->addComplexType('MyComplexType','complexType','struct','all','',
		array( 'ID' => array('name' => 'ID','type' => 'xsd:int'),
			   'YourName' => array('name' => 'YourName','type' => 'xsd:string')));

/**
 * GetNewChatMessages Input param: array(dialog_hash, currenttimestamp, posttimestamp)
 */
$server->wsdl->addComplexType('ipNewChatMessages','complexType','struct','all','',
		array( 'dialog_hash' => array('name' => 'dialog_hash','type' => 'xsd:string'),
			   'posttimestamp' => array('name' => 'posttimestamp','type' => 'xsd:string'),
			   'currenttimestamp' => array('name' => 'currenttimestamp','type' => 'xsd:string')
		));

		
/**
 * GetAllChatMessages Input param: dialog_hash
 */
//$server->wsdl->addComplexType('ipGetAllChatMessages','complexType','struct','all','',
//		array( 'dialog_hash' => array('name' => 'dialog_hash','type' => 'xsd:string')
//		));

		
/**
 * GetNewChatMessages AND GetAllChatMessages Return param (inner array)
 */
$server->wsdl->addComplexType('ChatMessage','complexType','struct','all','',
		array( 'dialog_hash' => array('name' => 'dialog_hash','type' => 'xsd:string'),
			   //'name' => array('name' => 'name','type' => 'xsd:string'),
			   'message' => array('name' => 'message','type' => 'xsd:string'),
			   'timestamp' => array('name' => 'timestamp','type' => 'xsd:string')));
/**
 * GetNewChatMessages AND GetAllChatMessages Return param (outer array)
 */
$server->wsdl->addComplexType(
    'ChatMessages',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array( 'ChatMessages' => array('name' => 'ChatMessages', 'type' => 'tns:ChatMessage') ),
    array( array( "ref" => "SOAP-ENC:arrayType",
                  "wsdl:arrayType" => "tns:ChatMessage[]")
         ),
    "tns:ChatMessage"
);		
		
/**
 * GetAllOperators Return param (inner array)
 */	
$server->wsdl->addComplexType(
    'operator_row',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'operator_hash' => array('name' => 'operator_hash', 'type' => 'xsd:string'),
        'status' => array('name' => 'status', 'type' => 'xsd:string'),
        'name' => array('name' => 'name', 'type' => 'xsd:string')
    )
);
/**
 * GetAllOperators Return param (outer array)
 */	
$server->wsdl->addComplexType(
    'operator_rows',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array( 'operator_rows' => array('name' => 'operator_rows', 'type' => 'tns:operator_row') ),
    array( array( "ref" => "SOAP-ENC:arrayType",
                  "wsdl:arrayType" => "tns:operator_row[]")
         ),
    "tns:operator_row"
);


/**
 * InitChatRequest Input Param
 */
$server->wsdl->addComplexType(
    'userdata',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'email' => array('name' => 'email', 'type' => 'xsd:string')
    )
);
/**
 * InitChatRequest return Param
 */
$server->wsdl->addComplexType(
    'opInitChatParams',
    'complexType',
    'struct',
    'all',
    '',
    array(
    	'dialog_hash' => array('name' => 'dialog_hash', 'type' => 'xsd:string'),
        'user_hash' => array('name' => 'user_hash', 'type' => 'xsd:string'),
        'operator_available' => array('name' => 'operator_available', 'type' => 'xsd:string')
    )
);


/**
 * GetAllOperators, gib alle Operator zurück die grade on sind
 * @param empty array
 * @return array of array(hash, status, name)
 * 
 */
$server->register('GetAllOperators',
  				// parameter list:
                array(), 
                // return value(s):
                array('return'=>'tns:operator_rows'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'retrieve all chat operators'); 		

                
/**
 * InitChatRequest, check if current user is available
 * -makes insert/update on user table
 * -makes operater request in table: user_operator_commitment
 * -return dialog_hash, user_hash, operator_available
 */
$server->register(
                // method name:
                'InitChatRequest', 	
                // parameter list:
                array('name'=>'tns:userdata'), 
                // return value(s):
                array('return'=>'tns:opInitChatParams'),
                // namespace:
                $namespace,
                // soapaction: (use default)
                false,
                // style: rpc or document
                'rpc',
                // use: encoded or literal
                'encoded',
                // description: documentation for the method
                'Initialize a chat request');
		
		
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
 * Test Method
 * @param $name
 */
function Test()
{
	global $model;
	$sql = 'select us_op.hash as chat_hash, us_op.operator_hash from user_operator as us_op'.
				   ' inner join operator as op '.
			       ' on us_op.operator_hash = op.hash '.
				   ' and op.status = 1 ';
	helper::writelog($sql);
	$rs = $model->db->query($sql);
	

  	 while ($res = $rs->fetchRow()) {
			helper::writelog($res);
   	 }	
   	 
	
	
	
}

/**
 * Test method2
 * @param $mycomplextype
 */
function HelloComplexWorld($mycomplextype)
{
	//helper::writelog($mycomplextype);	
	
	return $mycomplextype;	
}

/**
 * ObserveOperatorCommitment check if operator has agreed a chatdialog
 * @param $user_hash
 * @return array(status, operator_hash, dialog_hash);
 * 
 */ 
function ObserveOperatorCommitment($dialog_hash){
	global $model;
	
	try{
		$result = $model->ObserveOperatorCommitment($dialog_hash);
	}catch(Exception $ex){
		//helper::writelog($ex->getMessage());
		$sf = new soap_fault($ex->getCode(), '', $ex->getMessage(),'');
		$sf->soap_defencoding = 'UTF-8';
		return $sf;

	}
	
	return $result;
	
}



/**
 * leave offline message in table: offlinemessage
 * @param $chatmessageparams:array (user_hash, message)
 * @return $success:bool
 */
function SendMessage($messageparams){
	
	global $model;

	//helper::writelog($chatmessageparams);
	if(!$model->SendMessage($messageparams))
		return false;
	else 
		return  true;
}


/**
 * Write new chat message
 * @param $chatmessageparams:array (dialog_hash, message)
 * @return $success:bool
 */
function SendChatMessage($chatmessageparams){
	
	global $model;

	helper::writelog($chatmessageparams);
	if(!$model->SendChatMessage($chatmessageparams))
		return false;
	else 
		return  true;
}

/**
 * get chat messages since given timestamp for appropriated user/operator
 * @param $filter:array (dialog_hash, posttimestamp, currenttimestamp)
 * @return $result:array of array (message, dialog_hash, timestamp)
 */
function GetNewChatMessages($filter){
	helper::writelog($filter);
	global $model;
	$result = $model->GetNewChatMessages($filter);
	helper::writelog($result);
	return $result;
}

/**
 * get all chat messages for appropriated user/operator
 * @param $filter:array (dialog_hash)
 * @return $result:array of array (message, timestamp) 
 */
function GetAllChatMessages($filter){
	//helper::writelog($filter);
	global $model;
	$result = $model->GetAllChatMessages($filter);
	
	return $result;
	
}



/**
 * Initialize chat request for current user
 * - insert/update table:user
 * - if operator available leave request flag table:user_operator_commitment
 * @param $userdata
 * @return array(dialog_hash,user_hash,operator_available)
 */
function InitChatRequest($userdata){
    helper::writelog($userdata);
	global $model;
	$result = null;
	try{
		helper::writelog($userdata);
		$result = $model->InitChatRequest($userdata);
	}catch(Exception $ex){
		//helper::writelog($ex->getMessage());
		$sf = new soap_fault($ex->getCode(), '', $ex->getMessage(),'');
		$sf->soap_defencoding = 'UTF-8';
		return $sf;
	}
	
	return $result;

}

/**
 * 
 * GetAllOperators gives returns all operators which are on
 * @return array(array()) (whole operator rows)
 */
function GetAllOperators(){
	require 'kernel/db.php';
	require 'kernel/helper.php';
	
	$result = db::GetInstance()->GetAll('select *, operator_hash_pk as operator_hash from operator');
//	helper::writelog($result);
	//$result =array(array('hash'=>'dsfdsf', 'status'=> '1', 'name' => 'skjdfkds'), array('hash'=>'dsfdsf', 'status'=> '1', 'name' => 'skjdfkds'),array('hash'=>'dsfdsf', 'status'=> '1', 'name' => 'skjdfkds'));
	return $result;
}

//function newOperator(){
//	require 'db.php';
////	$record["name"] = "Krüger"; 
////    $record["status"] = "1"; 
////    $record["hash"] = "dfdfsfdxvre"; 	
//    $sql = "insert into operator (name,status,hash) ";
//	
//	$sql .= "values ('Krüger','1','sdlkfmdksmf045')";
//	$sql = utf8_encode($sql);
//   
//
//	db::GetInstance()->Execute($sql);
//    
//    //$insertSQL = db::GetInstance()->AutoExecute($rs, $record, 'INSERT'); 
//
//}

// Get our posted data if the service is being consumed
// otherwise leave this data blank.                
$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:'';

// pass our posted data (or nothing) to the soap service                    
$server->service($POST_DATA);                
?>