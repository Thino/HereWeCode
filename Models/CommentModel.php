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

	// Test for a case with headers problems
	function request_headers()
	{
		if(function_exists("apache_request_headers")) // If apache_request_headers() exists...
		{
			if($headers = apache_request_headers()) // And works...
			{
				return $headers; // Use it
			}
		}

		$headers = array();

		foreach(array_keys($_SERVER) as $skey)
		{
			echo $skey;
			if(substr($skey, 0, 5) == "HTTP_")
			{
				$headername = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($skey, 0, 5)))));
				$headers[$headername] = $_SERVER[$skey];
			}
		}

		return $headers;
	}


	// Return comments of a place (idPlace given)
	public function getCommentsWithPlaceId($id)
	{		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getComments($id);		
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
		$app->response()->status(200);	
		echo json_encode($result->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE);		
	}

	// Add a comment to a place ( must be auth )
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
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(201);					
				return;					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(409);		
				echo ('{"error":"Insert fail"}');	
				return;	
			}
		}	
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
		$app->response()->status(409);		
		echo ('{"error":"Bad json input ( text is NEEDED )"}');	
	}


	// Get a comment from a place
	public function getCommentWithId($id)
	{	
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getCommentWithId($id);	
		if ( $result->rowCount() > 0 )
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(200);			
			echo json_encode($result->fetch(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE);			
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(404);	
			echo ('{"error":"No comment were found"}');	
		} 
	}

	// update a comment ( auth required )
	public function  updateCommentWithId($id)
	{		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if user modify his comment ( or if he is admin )
		$header = $this->request_headers(); 
		$header = $header['Authorization'];		
		
		$authid = BasicHttpAuthentication::authenticate($header);
		echo $header;
		if ( $authid == $id || DalMySql::getInstance()->isAdmin($authid)  )
		{
			$body = $app->request()->getBody();
			$input = json_decode($body); 		
			if ( isset($input->text) && !empty($input->text) )
			{
				if ( DalMySql::getInstance()->updateComment($id,$input->text) )
				{
					$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
					$app->response()->status(204);
				}	
				else
				{
					$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
					$app->response()->status(520);	
					echo ('{"error":"Internal problem with the update"}');			
				}				
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(400);	
				echo ('{"error":"text is required !"}');			
			}
		}			
		else
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can only modify your comments )"}');	
		} 			
	}

	// Delete a comment ( auth required )
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
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(520);	
				echo ('{"error":"Internal problem with the delete"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can delete only comments you have created )"}');	
		}		
	}







}




