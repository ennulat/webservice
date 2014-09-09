<?php

require (dirname(__FIlE__).'/../kernel/db.php');

/**
 * 
 * 
 * DB abstraction Layer for chat interaction
 * @author Jes
 *
 */

class chat{
	
	public $db;
	
	function __construct(){
		$this->db = db::GetInstance();
	}
	
//	public function methodWrapper($methodAndParams){
//		try{
//			
//		}
//		catch(DBErrorException $dbex){
//				helper::writelog($dbex->getMessage());die;
//		}catch(ValidationException $validex){
//				helper::writelog($validex->getMessage());die;
//		}catch(ADODB_Exception $ex){
//				helper::writelog($ex->getMessage());die;
//		}catch(Exception $e){
//				helper::writelog($e->getMessage());die;
//		}
//	}

	
	/**
	 * 
	 * GetAllOperators gives returns all operators which are on
	 * @return unknown
	 */
	public function GetAllOperators(){
		return $this->db->GetAll('select * from operator');
	}
	
	/**
	 * Write new chat message
	 * @param $data:array (dialog_hash, message)
	 * @return $success:bool
	 */
	public function SendChatMessage($data){

		$data['dialog_hash_fk'] = $data['dialog_hash'];
		unset($data['dialog_hash']);
		//helper::writelog($data);
		if(!$this->db->AutoExecute('message', $data, 'INSERT')){
			//helper::writelog('false');
			return  false;
		}else{
			return  true;
		}
			//throw new DBErrorException();
	}
	
	
	/**
	 * get chat messages since given timestamp for appropriated user/operator
	 * @param $data:array (dialog_hash, currenttimestamp, posttimestamp)
	 * @return $result:array of array (message, dialog_hash, timestamp)
	 */
	public function GetNewChatMessages($data){
		
		
		$dialog_hash = $data['dialog_hash'];
		//$jointable = $data['joinTable'];
		$posttimestamp = $data['posttimestamp'];
		$currenttimestamp = $data['currenttimestamp'];

		$sql = "select dialog_hash_fk as dialog_hash, message, timestamp ".
				   "from message ".
			       "where dialog_hash_fk = '".$dialog_hash."' ";
		if(!empty($posttimestamp)){
//			$sql = PivotTableSQL(
//	         $this->db,  # adodb connection
//	         "message m ,".$jointable." f",   # tables
//	         "m.sendby, m.message, m.timestamp, f.name", # rows (multiple fields allowed)
//	         "m.sendby", # column to pivot on 
//	         "m.sendby = f.hash and m.timestamp < '".$currenttimestamp."' and m.timestamp >= '".$posttimestamp."' ".
//			 "and m.sendby = '".$hash."'" //join and where
//			);
			$sql .="and timestamp < '".$currenttimestamp."' and timestamp >= '".$posttimestamp."' ".
				   "order by timestamp asc";
		}else{
//			$sql = PivotTableSQL(
//	         $this->db,  # adodb connection
//	         "message m ,".$jointable." f",   # tables
//	         "m.sendby, m.message, m.timestamp, f.name", # rows (multiple fields allowed)
//	         "m.sendby", # column to pivot on 
//	         "m.sendby = f.hash and m.timestamp < '".$currenttimestamp."' ".
//			 "and m.sendby = '".$hash."'" //join and where
//			);
			$sql .="and timestamp < '".$currenttimestamp."' ".
				   "order by timestamp asc";
		}
		$tmp =	$this->db->GetAll($sql);
		$result = array();
		//$i = 0;
		foreach($tmp as $i => $messagerow){
			
			foreach($messagerow as $col => $value){
//				if($col == $hash || $col == 'Total')
//					continue;
				$result[$i][$col] = $value;
			}
			//$i++;
		}
		//helper::writelog($sql);
		
		return $result;
	}
	
	/**
	 * get all chat messages for appropriated user/operator
	 * @param $dialog_hash:string
	 * @return $result:array of array (message, timestamp) 
	 */
	function GetAllChatMessages($data){
		$dialog_hash = $data['dialog_hash'];
//		$jointable = $data['joinTable'];

//		$sql = PivotTableSQL(
//         $this->db,  # adodb connection
//         "message m ,".$jointable." f",   # tables
//         "m.sendby, m.message, m.timestamp, f.name", # rows (multiple fields allowed)
//         "m.sendby", # column to pivot on 
//         "m.sendby = f.hash and m.sendby = '".$hash."'" //join and where
//		);

		$sql = "select dialog_hash_fk as dialog_hash, message, timestamp from message where dialog_hash_fk = '".$dialog_hash."' order by timestamp asc";

		$tmp =	$this->db->GetAll($sql);
		$result = array();
		$i = 0;
		foreach($tmp as $i => $messagerow){
			
			foreach($messagerow as $col => $value){
//				if($col == $hash || $col == 'Total')
//					continue;
					
				$result[$i][$col] = $value;
			
			}
			$i++;
		}
		//helper::writelog($result);
		
		return $result;
	}
	
	/**
	 * Initialize chat request for current user
	 * - insert/update table:user
	 * - if operator available leave request flag table:user_operator_commitment
	 * - return user_hash and operator_hash
	 * @param $userdata
	 * @return $user_hash, $dialog_hash, $operator_available
	 */
	public function InitChatRequest($userdata){
		
		
		//Insert/Update user
		$name = $userdata['name'];
		$email = $userdata['email'];
		$rs = $this->db->Execute('select * from user where name = ? and email = ?', array($name, $email));
		if($rs->RecordCount()=== 0){//insert	
			$mode='INSERT';
			$newuser = array('name' => $name, 'email' => $email, 'user_hash_pk' => hash('md5', $name.$email) );
			$this->db->AutoExecute('user', $newuser, $mode);
		}	
		
		$result['user_hash'] = $this->db->GetOne('select user_hash_pk from user where name = ? and email = ?', array($name, $email));
		
		//check if any operator is online, if so then leave request flag
		$Operators = $this->db->GetAll('select operator_hash_pk as operator_hash from operator where status = 1');
		$countOps = count($Operators);

	    //helper::writelog($countOps);
	    //helper::writelog($Operators);
		//$randIndex = rand(0, $countOps);//zufalls op wählen
		$result['operator_available'] = '0';
		$result['dialog_hash'] = '0';
		if($countOps > 0){
			$result['operator_available'] = '1';
			
			$dialog_hash = hash('md5',(string)microtime());
			
			//throw new Exception($dialog_hash, 'test');
			$insertdata = array('status' => 1, 'dialog_hash_pk' => $dialog_hash, 'user_hash_fk' => $result['user_hash'] );
			$this->db->AutoExecute('user_operator_commitment', $insertdata, 'INSERT');
			$result['dialog_hash'] = $dialog_hash;
			
//			if($countOps> 1){
//				$result['operatorhash'] = $Operators[$randIndex]['operatorhash']; 
//
//			}else{
//				$result['operatorhash'] = $Operators[0]['operatorhash'];
//			}

		}
		
//		if(isset($result['operatorhash'])){//leave initial chatmessage for operator
//		
//			$initmessage = array('sendby' => $result['userhash'], 'message' => 'warte auf Operator');
//			$this->SendChatMessage($initmessage);
//			
//		}
		
		return $result;
		
	}
	
	/**
	 * ObserveUserOperatorCommitment check if operator has agreed a chatdialog
	 * @param $dialog_hash
	 * @return array(status, operator_hash, dialog_hash);
	 * 
	 */ 
	function ObserveUserOperatorCommitment($dialog_hash){

		$sql = "select status, operator_hash_fk as operator_hash, dialog_hash_pk as dialog_hash ".
		       "from user_operator_commitment ".
		       "where dialog_hash_pk ='".$dialog_hash."'";
		$result = $this->db->GetRow($sql);
		//helper::writelog($result);
		return $result;
	}
	
	
	/**
	 * leave offline message
	 * @param $data:array (user_hash, message)
	 * @return $success:bool
	 */
	public function SendMessage($data){

		$data['user_hash_fk'] = $data['user_hash'];
		unset($data['user_hash']);
		helper::writelog($data);
		if(!$this->db->AutoExecute('offlinemessage', $data, 'INSERT')){
			//helper::writelog('false');
			return  false;
		}else{
			return  true;
		}
			//throw new DBErrorException();
	}
	
	/**
	 * SetUserOperatorStatus set status in table: user_operator_commitment 
	 * for appropriated dialog_hash
	 * @param $data:array(dialog_hash, status)
	 * @return $success:bool;
	 */ 
	function SetUserOperatorStatus($data){
		helper::writelog($data);
		if(!$this->db->AutoExecute('user_operator_commitment', array('status' => $data['status']), 'UPDATE',"dialog_hash_pk = '".$data['dialog_hash']."'" ))
			return false;
		else
			return true;
	}
	
	
	
}