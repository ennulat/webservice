<?php
class DBErrorException extends Exception{
	
	public function __construct($message = "", $code = 0){
		parent::__construct($message, $code);
	}

}

class ValidationException extends  Exception{
	public function __construct($message = "", $code = 0){
		parent::__construct($message, $code);
	}
}