<?php

require_once 'DB/Dal.php';


class MemberModel
{

   protected static $instance;   
   protected function __construct() { }  
   protected function __clone() { }    
  
  public static function getInstance()
  {	
    if (!isset(self::$instance)) 
    {
      self::$instance = new self; 
    }    
    return self::$instance;
  }
	
	

   public function getMemberWithId($id)
   {	
		$app = \Slim\Slim::getInstance();
		$result = Dal::getInstance()->select('MEMBER',"idMember = $id",'picture');	
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(200);			
		echo json_encode($result->fetch(PDO::FETCH_ASSOC));		
   }
  
  
 
	

}

	


