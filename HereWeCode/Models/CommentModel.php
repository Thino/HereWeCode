<?php

require_once 'DB/DalMySql.php';


class CommentModel
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
 
 
  // Return comments of a place (idPlace given)
   public function getCommentsWithPlaceId($id)
   {		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getComments($id);		
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(200);			
		echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));		
   }
   
   public function addCommentToPlace($idPlace)
   {		
		$app = \Slim\Slim::getInstance();
		$body = $app->request()->getBody();
		$input = json_decode($body); 		
		if ( isset($input->text) && !empty($input->text) )
		{
			$headers = apache_request_headers();
			$idUser = BasicHttpAuthentication::authenticate($headers['Authorization']);
			$result = DalMySql::getInstance()->addCommentToPlace($input->text,$idUser,$idPlace);
			if ( $result > 0 )
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(201);					
				return;					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(409);		
				echo ('{"error":"Insert fail"}');	
				return;	
			}
		}	
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(409);		
		echo ('{"error":"Bad json input ( text is NEEDED )"}');	
   }
   
    public function getCommentWithId($id)
   {		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getCommentWithId($id);	
		if ( $result->rowCount() > 0 )
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(200);			
			echo json_encode($result->fetch(PDO::FETCH_ASSOC));	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(404);	
			echo ('{"error":"No comment were found"}');	
		} 
   }
   
    public function  updateCommentWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if user modify his comment ( or if he is admin )
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( $authid == $id || DalMySql::getInstance()->isAdmin($authid)  )
		{
			$body = $app->request()->getBody();
			$input = json_decode($body); 		
			if ( isset($input->text) && !empty($input->text) )
			{
				if ( DalMySql::getInstance()->updateComment($id,$input->text) )
				{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(204);
				}	
				else
				{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(520);	
					echo ('{"error":"Internal problem with the update"}');			
				}				
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(400);	
				echo ('{"error":"text is required !"}');			
			}
		}			
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can only modify your comments )"}');	
		}			
   }
   
    
   public function  deleteCommentWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if the user in an admin or can delete his own comment
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( $authid == $id || DalMySql::getInstance()->isAdmin($authid)  )
		{
			if ( DalMySql::getInstance()->deleteComment($id) )
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(520);	
				echo ('{"error":"Internal problem with the delete"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can delete only comments you have created )"}');	
		}		
   }
   
   
   
   
   
   
   
}

	


