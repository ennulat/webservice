<?php
require_once(dirname(__FILE__).'/../lib/adodb5/adodb.inc.php');
require_once(dirname(__FIlE__).'/../lib/adodb5/adodb-exceptions.inc.php');
require_once(dirname(__FILE__).'/../lib/adodb5/pivottable.inc.php');
//require_once(dirname(__FIlE__).'/../lib/adodb5/adodb-errorhandler.inc.php');
//define('ADODB_ERROR_LOG_TYPE', 3);
//define('ADODB_ERROR_LOG_DEST', dirname(__FILE__).'/../log/adodb_errors.log');
//require_once(dirname(__FILE__).'/InterfaceExceptions.php');

/**
 * 
 * 
 * adodb abstraction layer for db access
 * @author Jes
 *
 */
class db{
	private static $handle = null;
	public static function GetInstance(){
		if(is_null(self::$handle)){
			
			self::$handle = &ADONewConnection('mysql'); 
			self::$handle->connect('localhost','root','blaster','chat');   
			
			//self::$handle->debug = true;
	    	self::$handle->SetFetchMode(ADODB_FETCH_ASSOC);
	    	self::$handle->Execute("SET NAMES utf8");
	
		}
		return self::$handle;
	}
}

