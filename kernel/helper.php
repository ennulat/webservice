<?php
/**
 * 
 * 
 * helper Class for e.g. file logging
 * @author Jes
 *
 */

class helper{
	
	static $helper;
	
//	public static function GetInstance(){
//		if(self::$helper == null){
//			self::$helper = new self();
//		
//		}
//		
//		return self::$helper;
//	
//	}

	
	static public function writelog($out){
		
		ob_start();
		
		echo '<pre>class: '.__CLASS__.' | @line:  '.__LINE__;
		var_dump($out);
		echo '</pre>';
	
		
		$out = ob_get_clean();
		$fh = fopen(dirname(__FILE__).'/../log/log.txt', 'a+');
		fwrite($fh, $out);
		fclose($fh);
	
	}
}